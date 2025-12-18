<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosOrderItem extends BaseModel
{
    use HasFactory;

    protected $table = 'pos_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'variation_id',
        'sku',
        'name',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
