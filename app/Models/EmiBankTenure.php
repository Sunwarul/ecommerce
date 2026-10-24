<?php

namespace App\Models;

class EmiBankTenure extends BaseModel
{
    protected $fillable = [
        'emi_bank_id',
        'months',
        'customer_charge_percent',
        'merchant_charge_percent',
        'min_amount',
        'is_active',
    ];

    protected $casts = [
        'months' => 'integer',
        'customer_charge_percent' => 'float',
        'merchant_charge_percent' => 'float',
        'min_amount' => 'float',
        'is_active' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(EmiBank::class, 'emi_bank_id');
    }
}
