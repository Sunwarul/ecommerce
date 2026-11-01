<?php

namespace App\Services;

use App\Models\EmiBankTenure;
use App\Models\EmiTransaction;
use App\Models\PosOrder;
use App\Models\PosPayment;
use App\Models\Setting;
use Illuminate\Validation\ValidationException;

/**
 * Bank card EMI: the customer's card is charged in full on the bank's POS terminal and the bank
 * converts it to monthly instalments. The shop records the bank/tenure/approval code here and
 * tracks the bank's settlement (card amount minus the bank's merchant charge).
 */
class EmiService
{
    public static function paymentMethodId(): ?int
    {
        $id = Setting::get('emi_payment_method_id');

        return $id ? (int) $id : null;
    }

    public function quote(EmiBankTenure $tenure, float $principal): array
    {
        $customerCharge = round($principal * $tenure->customer_charge_percent / 100, 2);
        $cardAmount = round($principal + $customerCharge, 2);
        $merchantCharge = round($cardAmount * $tenure->merchant_charge_percent / 100, 2);

        return [
            'emi_bank_id' => $tenure->emi_bank_id,
            'emi_bank_tenure_id' => $tenure->id,
            'months' => $tenure->months,
            'principal_amount' => round($principal, 2),
            'customer_charge_percent' => $tenure->customer_charge_percent,
            'customer_charge_amount' => $customerCharge,
            'card_amount' => $cardAmount,
            'merchant_charge_percent' => $tenure->merchant_charge_percent,
            'merchant_charge_amount' => $merchantCharge,
            'net_receivable' => round($cardAmount - $merchantCharge, 2),
            'monthly_amount' => round($cardAmount / max(1, $tenure->months), 2),
        ];
    }

    /**
     * Expand EMI payment rows: the row amount is the part of the bill put on EMI;
     * the stored payment amount becomes the card amount (incl. customer charge).
     *
     * @return array{0: array, 1: float} [payments, total customer charge]
     */
    public function preparePayments(array $payments): array
    {
        $chargeTotal = 0;

        foreach ($payments as $i => $p) {
            if (empty($p['emi']['emi_bank_tenure_id'])) {
                unset($payments[$i]['emi']);

                continue;
            }

            $tenure = EmiBankTenure::with('bank')->where('is_active', true)->find($p['emi']['emi_bank_tenure_id']);
            if (! $tenure || ! $tenure->bank?->is_active) {
                throw ValidationException::withMessages(['payments' => [__('Selected EMI bank / tenure is not available.')]]);
            }
            if ((float) $p['amount'] < (float) $tenure->min_amount) {
                throw ValidationException::withMessages([
                    'payments' => [__('Minimum amount for :bank :months months EMI is :amount.', ['bank' => $tenure->bank->name, 'months' => $tenure->months, 'amount' => number_format($tenure->min_amount, 2)])],
                ]);
            }

            $quote = $this->quote($tenure, (float) $p['amount']);
            $payments[$i]['amount'] = $quote['card_amount'];
            $payments[$i]['emi'] = array_merge($p['emi'], $quote);
            $chargeTotal += $quote['customer_charge_amount'];
        }

        return [array_values($payments), round($chargeTotal, 2)];
    }

    public function record(PosOrder $order, PosPayment $payment, array $emi): EmiTransaction
    {
        return EmiTransaction::create([
            'order_id' => $order->id,
            'pos_payment_id' => $payment->id,
            'branch_id' => $order->branch_id,
            'emi_bank_id' => $emi['emi_bank_id'],
            'emi_bank_tenure_id' => $emi['emi_bank_tenure_id'],
            'months' => $emi['months'],
            'principal_amount' => $emi['principal_amount'],
            'customer_charge_percent' => $emi['customer_charge_percent'],
            'customer_charge_amount' => $emi['customer_charge_amount'],
            'card_amount' => $emi['card_amount'],
            'merchant_charge_percent' => $emi['merchant_charge_percent'],
            'merchant_charge_amount' => $emi['merchant_charge_amount'],
            'net_receivable' => $emi['net_receivable'],
            'monthly_amount' => $emi['monthly_amount'],
            'card_holder_name' => $emi['card_holder_name'] ?? null,
            'card_last4' => $emi['card_last4'] ?? null,
            'approval_code' => $emi['approval_code'] ?? null,
            'terminal_ref' => $emi['terminal_ref'] ?? null,
            'settlement_status' => 'pending',
        ]);
    }
}
