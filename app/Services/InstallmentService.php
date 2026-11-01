<?php

namespace App\Services;

use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSale;
use App\Models\InstallmentSchedule;
use App\Models\PosOrder;
use App\Models\PosPayment;
use App\Models\Setting;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstallmentService
{
    public const FREQUENCIES = ['weekly', 'biweekly', 'monthly', 'custom'];

    public const INTEREST_TYPES = ['none', 'flat', 'reducing'];

    public function __construct(protected SmsManager $sms) {}

    /** Validation rules for an "installment" block in a request. */
    public static function rules(string $prefix = 'installment.'): array
    {
        return [
            $prefix.'installment_plan_id' => ['nullable', 'exists:installment_plans,id'],
            $prefix.'number_of_installments' => ['required', 'integer', 'min:1', 'max:120'],
            $prefix.'frequency' => ['required', 'in:'.implode(',', self::FREQUENCIES)],
            $prefix.'interval_days' => ['nullable', 'required_if:'.$prefix.'frequency,custom', 'integer', 'min:1', 'max:365'],
            $prefix.'interest_type' => ['required', 'in:'.implode(',', self::INTEREST_TYPES)],
            $prefix.'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            $prefix.'processing_fee_type' => ['nullable', 'in:fixed,percent'],
            $prefix.'processing_fee' => ['nullable', 'numeric', 'min:0'],
            $prefix.'late_fee_type' => ['nullable', 'in:fixed,percent'],
            $prefix.'late_fee_amount' => ['nullable', 'numeric', 'min:0'],
            $prefix.'grace_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            $prefix.'first_due_date' => ['required', 'date'],
            $prefix.'customer_nid' => ['nullable', 'string', 'max:50'],
            $prefix.'guarantor_name' => ['nullable', 'string', 'max:191'],
            $prefix.'guarantor_phone' => ['nullable', 'string', 'max:30'],
            $prefix.'guarantor_nid' => ['nullable', 'string', 'max:50'],
            $prefix.'guarantor_address' => ['nullable', 'string', 'max:500'],
            $prefix.'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pure calculation, no DB writes.
     *
     * @param  float  $saleAmount  order total (before installment charges)
     * @param  float  $downPayment  amount paid now
     */
    public function calculate(float $saleAmount, float $downPayment, array $c): array
    {
        $n = max(1, (int) ($c['number_of_installments'] ?? 1));
        $frequency = $c['frequency'] ?? 'monthly';
        $intervalDays = (int) ($c['interval_days'] ?? 0);
        $interestType = $c['interest_type'] ?? 'none';
        $rate = (float) ($c['interest_rate'] ?? 0);

        $feeValue = (float) ($c['processing_fee'] ?? 0);
        $processingFee = round(($c['processing_fee_type'] ?? 'fixed') === 'percent'
            ? $saleAmount * $feeValue / 100
            : $feeValue, 2);

        $financed = round($saleAmount + $processingFee - $downPayment, 2);

        $periodDays = match ($frequency) {
            'weekly' => 7,
            'biweekly' => 14,
            'custom' => max(1, $intervalDays),
            default => 30,
        };

        // Interest per installment (before rounding)
        $rows = [];
        if ($financed > 0) {
            if ($interestType === 'reducing' && $rate > 0) {
                $r = $frequency === 'monthly' ? $rate / 100 / 12 : $rate / 100 * $periodDays / 365;
                $emi = $financed * $r * (1 + $r) ** $n / ((1 + $r) ** $n - 1);
                $balance = $financed;
                for ($i = 1; $i <= $n; $i++) {
                    $interest = $balance * $r;
                    $principal = $emi - $interest;
                    $balance -= $principal;
                    $rows[] = ['principal' => $principal, 'interest' => $interest];
                }
            } else {
                $totalInterest = $interestType === 'flat' ? $financed * $rate / 100 : 0;
                for ($i = 1; $i <= $n; $i++) {
                    $rows[] = ['principal' => $financed / $n, 'interest' => $totalInterest / $n];
                }
            }
        }

        $interestAmount = round(array_sum(array_column($rows, 'interest')), 2);
        $totalPayable = round($financed + $interestAmount, 2);

        // Round each installment (optionally to whole currency); last one absorbs the remainder.
        $roundInteger = Setting::get('installment_rounding', 'integer') === 'integer';
        $base = $n > 0 ? $totalPayable / $n : 0;
        $base = $roundInteger ? round($base) : round($base, 2);

        $first = Carbon::parse($c['first_due_date'] ?? today()->addMonth());
        $schedules = [];
        $allocated = 0;
        $interestAllocated = 0;
        foreach ($rows as $i => $row) {
            $no = $i + 1;
            $amount = $no === $n ? round($totalPayable - $allocated, 2) : $base;
            $interest = $no === $n ? round($interestAmount - $interestAllocated, 2) : round($row['interest'], 2);
            $interest = min($interest, max(0, $amount));
            $allocated += $amount;
            $interestAllocated += $interest;

            $schedules[] = [
                'installment_no' => $no,
                'due_date' => $this->dueDate($first, $i, $frequency, $periodDays)->toDateString(),
                'principal' => round($amount - $interest, 2),
                'interest' => $interest,
                'amount' => round($amount, 2),
            ];
        }

        return [
            'sale_amount' => round($saleAmount, 2),
            'down_payment' => round($downPayment, 2),
            'processing_fee' => $processingFee,
            'financed_amount' => $financed,
            'interest_amount' => $interestAmount,
            'total_payable' => $totalPayable,
            'installment_amount' => $schedules[0]['amount'] ?? 0,
            'grand_total' => round($saleAmount + $processingFee + $interestAmount, 2),
            'schedules' => $schedules,
        ];
    }

    private function dueDate(Carbon $first, int $index, string $frequency, int $periodDays): Carbon
    {
        return $frequency === 'monthly'
            ? $first->copy()->addMonthsNoOverflow($index)
            : $first->copy()->addDays($periodDays * $index);
    }

    /** Merge plan defaults under explicit values. */
    public function withPlanDefaults(array $c): array
    {
        if (empty($c['installment_plan_id'])) {
            return $c;
        }
        $plan = InstallmentPlan::find($c['installment_plan_id']);
        if (! $plan) {
            return $c;
        }

        foreach ([
            'number_of_installments', 'frequency', 'interval_days', 'interest_type', 'interest_rate',
            'processing_fee_type', 'processing_fee', 'late_fee_type', 'late_fee_amount', 'grace_days',
        ] as $key) {
            if (! array_key_exists($key, $c) || $c[$key] === null || $c[$key] === '') {
                $c[$key] = $plan->{$key};
            }
        }
        $c['_min_down_payment_percent'] = (float) $plan->min_down_payment_percent;

        return $c;
    }

    /**
     * Turn a completed order's remaining due into an installment agreement.
     * Must be called inside a DB transaction, after the order's payments are saved.
     */
    public function createForOrder(PosOrder $order, array $c, ?int $userId = null): InstallmentSale
    {
        if (! $order->customer_id) {
            throw ValidationException::withMessages(['customer_id' => [__('A customer is required for installment sales.')]]);
        }
        if ($order->installmentSale()->exists()) {
            throw ValidationException::withMessages(['installment' => [__('This order already has an installment agreement.')]]);
        }

        $c = $this->withPlanDefaults($c);
        $saleAmount = (float) $order->total_amount - (float) $order->installment_charge_amount;
        $downPayment = (float) $order->payments()->sum('amount');

        $minPercent = (float) ($c['_min_down_payment_percent'] ?? 0);
        if ($minPercent > 0 && $downPayment + 0.009 < $saleAmount * $minPercent / 100) {
            throw ValidationException::withMessages([
                'installment' => [__('Minimum down payment for this plan is :percent% (:amount).', ['percent' => $minPercent, 'amount' => number_format($saleAmount * $minPercent / 100, 2)])],
            ]);
        }

        $calc = $this->calculate($saleAmount, $downPayment, $c);
        if ($calc['financed_amount'] <= 0) {
            throw ValidationException::withMessages(['installment' => [__('Nothing left to pay in installments.')]]);
        }

        $sale = InstallmentSale::create([
            'agreement_no' => 'INS-'.now()->format('ymd').'-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'branch_id' => $order->branch_id,
            'installment_plan_id' => $c['installment_plan_id'] ?? null,
            'sale_amount' => $calc['sale_amount'],
            'down_payment' => $calc['down_payment'],
            'processing_fee' => $calc['processing_fee'],
            'financed_amount' => $calc['financed_amount'],
            'interest_type' => $c['interest_type'] ?? 'none',
            'interest_rate' => (float) ($c['interest_rate'] ?? 0),
            'interest_amount' => $calc['interest_amount'],
            'total_payable' => $calc['total_payable'],
            'number_of_installments' => count($calc['schedules']),
            'frequency' => $c['frequency'] ?? 'monthly',
            'interval_days' => $c['interval_days'] ?? null,
            'installment_amount' => $calc['installment_amount'],
            'first_due_date' => $calc['schedules'][0]['due_date'],
            'late_fee_type' => $c['late_fee_type'] ?? 'fixed',
            'late_fee_amount' => (float) ($c['late_fee_amount'] ?? 0),
            'grace_days' => (int) ($c['grace_days'] ?? 0),
            'status' => 'active',
            'customer_nid' => $c['customer_nid'] ?? null,
            'guarantor_name' => $c['guarantor_name'] ?? null,
            'guarantor_phone' => $c['guarantor_phone'] ?? null,
            'guarantor_nid' => $c['guarantor_nid'] ?? null,
            'guarantor_address' => $c['guarantor_address'] ?? null,
            'notes' => $c['notes'] ?? null,
            'created_by' => $userId,
        ]);

        foreach ($calc['schedules'] as $row) {
            $sale->schedules()->create($row + ['status' => 'pending']);
        }

        $charges = round($calc['processing_fee'] + $calc['interest_amount'], 2);
        $order->forceFill([
            'sale_type' => 'installment',
            'installment_charge_amount' => $charges,
            'total_amount' => round($saleAmount + $charges, 2),
        ])->save();
        $order->recalculatePayments();

        $sale->load(['customer', 'order']);
        DB::afterCommit(fn () => $this->sms->notify('installment_created', $sale->customer, [
            'agreement_no' => $sale->agreement_no,
            'invoice_no' => $order->invoice_no,
            'installments' => $sale->number_of_installments,
            'installment_amount' => SmsManager::money($sale->installment_amount),
            'next_due_date' => $sale->first_due_date->format('d-m-Y'),
            'due' => SmsManager::money($sale->total_payable),
        ], $sale));

        return $sale;
    }

    /** Charge late fees on schedules past due + grace (once per schedule). */
    public function applyLateFees(InstallmentSale $sale): float
    {
        if ($sale->status !== 'active' || (float) $sale->late_fee_amount <= 0) {
            return 0;
        }

        $cutoff = today()->subDays((int) $sale->grace_days);
        $added = 0;

        $schedules = $sale->schedules()->unpaid()
            ->where('late_fee', 0)
            ->whereDate('due_date', '<', $cutoff)
            ->get();

        foreach ($schedules as $schedule) {
            $fee = $sale->late_fee_type === 'percent'
                ? round((float) $schedule->amount * (float) $sale->late_fee_amount / 100, 2)
                : (float) $sale->late_fee_amount;
            if ($fee <= 0) {
                continue;
            }
            $schedule->update(['late_fee' => $fee]);
            $added += $fee;
        }

        if ($added > 0) {
            $sale->increment('late_fee_charged', $added);
            $order = $sale->order;
            $order->forceFill([
                'late_fee_amount' => round((float) $order->late_fee_amount + $added, 2),
                'total_amount' => round((float) $order->total_amount + $added, 2),
            ])->save();
            $order->recalculatePayments();
        }

        return $added;
    }

    public function waiveLateFee(InstallmentSchedule $schedule): float
    {
        $waive = round((float) $schedule->late_fee - (float) $schedule->late_fee_paid, 2);
        if ($waive <= 0) {
            return 0;
        }

        $sale = $schedule->sale;
        $schedule->update(['late_fee' => $schedule->late_fee_paid]);
        $sale->decrement('late_fee_charged', $waive);

        $order = $sale->order;
        $order->forceFill([
            'late_fee_amount' => max(0, round((float) $order->late_fee_amount - $waive, 2)),
            'total_amount' => round((float) $order->total_amount - $waive, 2),
        ])->save();
        $order->recalculatePayments();
        $this->refreshScheduleStatus($schedule->fresh());

        return $waive;
    }

    /**
     * Collect money against an agreement. Oldest installment first; late fee before principal.
     *
     * @param  array  $payments  [{payment_method_id, amount, transaction_ref?, notes?, meta?}]
     */
    public function collect(InstallmentSale $sale, array $payments, ?int $userId = null): array
    {
        if ($sale->status !== 'active') {
            throw ValidationException::withMessages(['payments' => [__('This agreement is not active.')]]);
        }

        $this->applyLateFees($sale);
        $sale->refresh();

        $total = round(collect($payments)->sum(fn ($p) => (float) $p['amount']), 2);
        if ($total <= 0) {
            throw ValidationException::withMessages(['payments' => [__('Enter an amount to collect.')]]);
        }
        if ($total > $sale->balance + 0.009) {
            throw ValidationException::withMessages(['payments' => [__('Amount exceeds the outstanding balance of :amount.', ['amount' => number_format($sale->balance, 2)])]]);
        }

        $order = $sale->order;
        $schedules = $sale->schedules()->unpaid()->get();
        $paidTowardsPrincipal = 0;
        $paidTowardsLateFee = 0;

        foreach ($payments as $p) {
            $remaining = round((float) $p['amount'], 2);
            if ($remaining <= 0) {
                continue;
            }

            $posPayment = PosPayment::create([
                'order_id' => $order->id,
                'branch_id' => $order->branch_id ?? session('current_branch_id') ?? 1,
                'payment_method_id' => $p['payment_method_id'],
                'amount' => $remaining,
                'paid_at' => now(),
                'transaction_ref' => $p['transaction_ref'] ?? null,
                'notes' => $p['notes'] ?? null,
                'meta' => array_filter(array_merge($p['meta'] ?? [], [
                    'type' => 'installment',
                    'installment_sale_id' => $sale->id,
                ])),
            ]);

            foreach ($schedules as $schedule) {
                if ($remaining <= 0) {
                    break;
                }
                $feeDue = round((float) $schedule->late_fee - (float) $schedule->late_fee_paid, 2);
                $amtDue = round((float) $schedule->amount - (float) $schedule->paid_amount, 2);
                if ($feeDue <= 0 && $amtDue <= 0) {
                    continue;
                }

                $fee = min($feeDue, $remaining);
                $remaining = round($remaining - $fee, 2);
                $amt = min($amtDue, $remaining);
                $remaining = round($remaining - $amt, 2);

                $schedule->late_fee_paid = round((float) $schedule->late_fee_paid + $fee, 2);
                $schedule->paid_amount = round((float) $schedule->paid_amount + $amt, 2);
                $this->refreshScheduleStatus($schedule);

                InstallmentPayment::create([
                    'installment_sale_id' => $sale->id,
                    'installment_schedule_id' => $schedule->id,
                    'pos_payment_id' => $posPayment->id,
                    'amount' => $amt,
                    'late_fee' => $fee,
                    'received_by' => $userId,
                ]);

                $paidTowardsPrincipal += $amt;
                $paidTowardsLateFee += $fee;
            }
        }

        $sale->forceFill([
            'paid_amount' => round((float) $sale->paid_amount + $paidTowardsPrincipal, 2),
            'late_fee_paid' => round((float) $sale->late_fee_paid + $paidTowardsLateFee, 2),
        ]);
        if (! $sale->schedules()->unpaid()->exists()) {
            $sale->status = 'completed';
            $sale->completed_at = now();
        }
        $sale->save();
        $order->recalculatePayments();

        $next = $sale->nextSchedule()->first();
        $sale->loadMissing('customer');
        DB::afterCommit(fn () => $this->sms->notify('payment_received', $sale->customer, [
            'amount' => SmsManager::money($total),
            'invoice_no' => $order->invoice_no,
            'agreement_no' => $sale->agreement_no,
            'due' => SmsManager::money($sale->fresh()->balance),
            'next_due_date' => $next?->due_date?->format('d-m-Y') ?? '-',
            'installment_amount' => SmsManager::money($next?->remaining ?? 0),
        ], $sale));

        return [
            'collected' => $total,
            'principal' => round($paidTowardsPrincipal, 2),
            'late_fee' => round($paidTowardsLateFee, 2),
        ];
    }

    public function refreshScheduleStatus(InstallmentSchedule $schedule): void
    {
        $fullyPaid = (float) $schedule->paid_amount + 0.009 >= (float) $schedule->amount
            && (float) $schedule->late_fee_paid + 0.009 >= (float) $schedule->late_fee;

        $schedule->status = $fullyPaid ? 'paid' : (((float) $schedule->paid_amount > 0 || (float) $schedule->late_fee_paid > 0) ? 'partial' : 'pending');
        $schedule->paid_at = $fullyPaid ? ($schedule->paid_at ?? now()) : null;
        $schedule->save();
    }

    public function cancel(InstallmentSale $sale): void
    {
        $sale->update(['status' => 'cancelled']);
        $sale->schedules()->unpaid()->update(['status' => 'cancelled']);
    }

    /**
     * Daily job: apply late fees, send upcoming and overdue reminders.
     */
    public function processDaily(): array
    {
        $stats = ['late_fees' => 0, 'reminders' => 0, 'overdue_notices' => 0];

        InstallmentSale::where('status', 'active')->with('order')->chunkById(100, function ($sales) use (&$stats) {
            foreach ($sales as $sale) {
                DB::transaction(function () use ($sale, &$stats) {
                    $stats['late_fees'] += $this->applyLateFees($sale);
                });
            }
        });

        $daysBefore = (int) Setting::get('sms_installment_reminder_days', 3);
        $remindOn = today()->addDays($daysBefore);

        // Upcoming
        InstallmentSchedule::unpaid()
            ->whereDate('due_date', $remindOn)
            ->whereNull('reminded_at')
            ->whereHas('sale', fn ($q) => $q->where('status', 'active'))
            ->with('sale.customer')
            ->chunkById(200, function ($rows) use (&$stats) {
                foreach ($rows as $schedule) {
                    if ($this->sms->notify('installment_reminder', $schedule->sale->customer, $this->scheduleVars($schedule), $schedule->sale)) {
                        $stats['reminders']++;
                    }
                    $schedule->update(['reminded_at' => now()]);
                }
            });

        // Overdue: notify the day after due date + grace
        InstallmentSchedule::unpaid()
            ->whereHas('sale', fn ($q) => $q->where('status', 'active'))
            ->with('sale.customer')
            ->whereDate('due_date', '<', today())
            ->chunkById(200, function ($rows) use (&$stats) {
                foreach ($rows as $schedule) {
                    $notifyOn = $schedule->due_date->copy()->addDays((int) $schedule->sale->grace_days + 1);
                    if (! $notifyOn->isSameDay(today())) {
                        continue;
                    }
                    if ($this->sms->notify('installment_overdue', $schedule->sale->customer, $this->scheduleVars($schedule), $schedule->sale)) {
                        $stats['overdue_notices']++;
                    }
                }
            });

        return $stats;
    }

    private function scheduleVars(InstallmentSchedule $schedule): array
    {
        return [
            'agreement_no' => $schedule->sale->agreement_no,
            'installment_no' => $schedule->installment_no,
            'installment_amount' => SmsManager::money($schedule->remaining),
            'next_due_date' => $schedule->due_date->format('d-m-Y'),
            'due' => SmsManager::money($schedule->sale->balance),
        ];
    }
}
