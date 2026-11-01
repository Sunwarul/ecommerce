<?php

namespace App\Models;

class InstallmentSale extends BaseModel
{
    protected $fillable = [
        'agreement_no',
        'order_id',
        'customer_id',
        'branch_id',
        'installment_plan_id',
        'sale_amount',
        'down_payment',
        'processing_fee',
        'financed_amount',
        'interest_type',
        'interest_rate',
        'interest_amount',
        'total_payable',
        'number_of_installments',
        'frequency',
        'interval_days',
        'installment_amount',
        'first_due_date',
        'late_fee_type',
        'late_fee_amount',
        'grace_days',
        'paid_amount',
        'late_fee_charged',
        'late_fee_paid',
        'status',
        'customer_nid',
        'guarantor_name',
        'guarantor_phone',
        'guarantor_nid',
        'guarantor_address',
        'notes',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'sale_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'financed_amount' => 'decimal:2',
        'interest_rate' => 'float',
        'interest_amount' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee_charged' => 'decimal:2',
        'late_fee_paid' => 'decimal:2',
        'first_due_date' => 'date:Y-m-d',
        'completed_at' => 'datetime',
    ];

    protected $appends = ['balance'];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }

    public function schedules()
    {
        return $this->hasMany(InstallmentSchedule::class)->orderBy('installment_no');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Outstanding installment balance incl. unpaid late fees. */
    public function getBalanceAttribute(): float
    {
        return round(
            max(0, (float) $this->total_payable - (float) $this->paid_amount)
            + max(0, (float) $this->late_fee_charged - (float) $this->late_fee_paid),
            2
        );
    }

    public function nextSchedule()
    {
        return $this->hasOne(InstallmentSchedule::class)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('installment_no');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->whereHas('schedules', fn ($s) => $s->unpaid()->whereDate('due_date', '<', today()));
    }
}
