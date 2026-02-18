<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosOrder extends BaseModel
{
    use HasFactory, HasBranch;

    protected $table = 'pos_orders';

    protected $fillable = [
        'pos_session_id',
        'branch_id',
        'customer_id',
        'user_id',
        'invoice_no',
        'reference_no',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_status',
        'status',
        'warranty_info',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d h:i A T'
    ];

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PosOrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(PosPayment::class, 'order_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
