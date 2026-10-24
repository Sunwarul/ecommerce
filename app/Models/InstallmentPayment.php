<?php

namespace App\Models;

class InstallmentPayment extends BaseModel
{
    protected $fillable = [
        'installment_sale_id',
        'installment_schedule_id',
        'pos_payment_id',
        'amount',
        'late_fee',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(InstallmentSale::class, 'installment_sale_id');
    }

    public function schedule()
    {
        return $this->belongsTo(InstallmentSchedule::class, 'installment_schedule_id');
    }

    public function posPayment()
    {
        return $this->belongsTo(PosPayment::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
