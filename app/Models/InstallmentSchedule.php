<?php

namespace App\Models;

class InstallmentSchedule extends BaseModel
{
    protected $fillable = [
        'installment_sale_id',
        'installment_no',
        'due_date',
        'principal',
        'interest',
        'amount',
        'paid_amount',
        'late_fee',
        'late_fee_paid',
        'status',
        'paid_at',
        'reminded_at',
    ];

    protected $casts = [
        'due_date' => 'date:Y-m-d',
        'principal' => 'decimal:2',
        'interest' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'late_fee_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'reminded_at' => 'datetime',
    ];

    protected $appends = ['remaining', 'is_overdue'];

    public function sale()
    {
        return $this->belongsTo(InstallmentSale::class, 'installment_sale_id');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'partial']);
    }

    public function getRemainingAttribute(): float
    {
        return round(max(0, (float) $this->amount - (float) $this->paid_amount)
            + max(0, (float) $this->late_fee - (float) $this->late_fee_paid), 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['pending', 'partial'], true)
            && $this->due_date
            && $this->due_date->lt(today());
    }
}
