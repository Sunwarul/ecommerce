<?php

namespace App\Models;

class EmiTransaction extends BaseModel
{
    protected $fillable = [
        'order_id',
        'pos_payment_id',
        'emi_bank_id',
        'emi_bank_tenure_id',
        'branch_id',
        'months',
        'principal_amount',
        'customer_charge_percent',
        'customer_charge_amount',
        'card_amount',
        'merchant_charge_percent',
        'merchant_charge_amount',
        'net_receivable',
        'monthly_amount',
        'card_holder_name',
        'card_last4',
        'approval_code',
        'terminal_ref',
        'settlement_status',
        'settled_amount',
        'settled_at',
        'settlement_ref',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'customer_charge_percent' => 'float',
        'customer_charge_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'merchant_charge_percent' => 'float',
        'merchant_charge_amount' => 'decimal:2',
        'net_receivable' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'settled_at' => 'date:Y-m-d',
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function bank()
    {
        return $this->belongsTo(EmiBank::class, 'emi_bank_id');
    }

    public function tenure()
    {
        return $this->belongsTo(EmiBankTenure::class, 'emi_bank_tenure_id');
    }

    public function posPayment()
    {
        return $this->belongsTo(PosPayment::class);
    }
}
