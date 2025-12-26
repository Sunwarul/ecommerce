<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosPayment extends BaseModel
{
    use HasFactory;

    protected $table = 'pos_payments';

    protected $fillable = [
        'order_id',
        'branch_id',
        'payment_method_id',
        'amount',
        'paid_at',
        'transaction_ref',
        'notes',
        'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'meta' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
