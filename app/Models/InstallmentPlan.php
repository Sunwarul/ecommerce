<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentPlan extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'number_of_installments',
        'frequency',
        'interval_days',
        'interest_type',
        'interest_rate',
        'min_down_payment_percent',
        'processing_fee_type',
        'processing_fee',
        'late_fee_type',
        'late_fee_amount',
        'grace_days',
        'is_active',
    ];

    protected $casts = [
        'number_of_installments' => 'integer',
        'interval_days' => 'integer',
        'interest_rate' => 'float',
        'min_down_payment_percent' => 'float',
        'processing_fee' => 'float',
        'late_fee_amount' => 'float',
        'grace_days' => 'integer',
        'is_active' => 'boolean',
    ];
}
