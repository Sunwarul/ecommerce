<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductAttribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Available attribute types
    const TYPE_SELECT = 'select';

    const TYPE_RADIO = 'radio';

    const TYPE_COLOR = 'color';

    const TYPE_BUTTON = 'button';

    public static function getTypes(): array
    {
        return [
            self::TYPE_SELECT,
            self::TYPE_RADIO,
            self::TYPE_COLOR,
            self::TYPE_BUTTON,
        ];
    }

    // Relationships
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variation_attributes', 'attribute_id', 'product_id')
            ->distinct();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function isColorType(): bool
    {
        return $this->type === self::TYPE_COLOR;
    }

    public function getValuesByProduct(Product $product)
    {
        return $this->values()
            ->whereHas('variations', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->get();
    }
}
