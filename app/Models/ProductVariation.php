<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'stock_quantity',
        'stock_status',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    const STOCK_IN_STOCK = 'in_stock';

    const STOCK_OUT_OF_STOCK = 'out_of_stock';

    const STOCK_PRE_ORDER = 'pre_order';

    public static function getStockStatuses(): array
    {
        return [
            self::STOCK_IN_STOCK,
            self::STOCK_OUT_OF_STOCK,
            self::STOCK_PRE_ORDER,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variation_attributes',
            'variation_id',
            'attribute_value_id'
        )->withPivot(['attribute_id', 'product_id']);
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', self::STOCK_IN_STOCK)
            ->where('stock_quantity', '>', 0);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'variation_id');
    }

    public function simpleStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class)->whereNull('variation_id');
    }

    public function variationStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class)->whereNotNull('variation_id');
    }


    // Helper methods
    public function getCurrentPrice(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function hasDiscount(): bool
    {
        return !is_null($this->discount_price);
    }

    public function getDiscountPercentage(): ?float
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function isInStock(): bool
    {
        return $this->stock_status === self::STOCK_IN_STOCK && $this->stock_quantity > 0;
    }

    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->stock_quantity -= $quantity;
        if ($this->stock_quantity === 0) {
            $this->stock_status = self::STOCK_OUT_OF_STOCK;
        }

        return $this->save();
    }

    public function increaseStock(int $quantity): bool
    {
        $this->stock_quantity += $quantity;
        if ($this->stock_quantity > 0 && $this->stock_status === self::STOCK_OUT_OF_STOCK) {
            $this->stock_status = self::STOCK_IN_STOCK;
        }

        return $this->save();
    }

    public function getTotalWarehouseStockAttribute(): float
    {
        return (float) $this->stocks()->sum('quantity');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variation_id');
    }


}
