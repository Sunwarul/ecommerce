<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosOrder extends BaseModel
{
    use HasBranch, HasFactory, SoftDeletes;

    protected $table = 'pos_orders';

    protected $fillable = [
        'pos_session_id',
        'branch_id',
        'warehouse_id',
        'customer_id',
        'discount_id',
        'user_id',
        'invoice_no',
        'reference_no',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'emi_charge_amount',
        'installment_charge_amount',
        'late_fee_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'change_amount',
        'payment_status',
        'status',
        'sale_type',
        'warranty_info',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'emi_charge_amount' => 'decimal:2',
        'installment_charge_amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d h:i A T',
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

    public function installmentSale()
    {
        return $this->hasOne(InstallmentSale::class, 'order_id');
    }

    public function emiTransactions()
    {
        return $this->hasMany(EmiTransaction::class, 'order_id');
    }

    /**
     * Re-derive paid / due / change / payment_status from the payment rows.
     */
    public function recalculatePayments(): self
    {
        $total = (float) $this->total_amount;
        $paid = (float) $this->payments()->sum('amount');

        $this->forceFill([
            'paid_amount' => $paid,
            'due_amount' => max(0, round($total - $paid, 2)),
            'change_amount' => max(0, round($paid - $total, 2)),
            'payment_status' => $paid <= 0 ? 'unpaid' : ($paid + 0.009 >= $total ? 'paid' : 'partial'),
        ])->save();

        return $this;
    }

    public static function generateInvoiceNo(int $orderId): string
    {
        return 'POS-'.now()->format('YmdHis').'-'.$orderId;
    }
}
