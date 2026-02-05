<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductAttributeValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'attribute_id',
        'value',
        'display_value',
        'color_code',
    ];

    protected $casts = [
        'color_code' => 'string',
    ];

    // Relationships
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariation::class,
            'product_variation_attributes',
            'attribute_value_id',
            'variation_id'
        )->withPivot('attribute_id');
    }

    // Helper methods
    public function getDisplayValue(): string
    {
        return $this->display_value ?? $this->value;
    }

    public function isColorAttribute(): bool
    {
        return $this->attribute->type === ProductAttribute::TYPE_COLOR;
    }

    public function getFormattedValue(): string
    {
        if ($this->isColorAttribute() && $this->color_code) {
            return sprintf('%s (%s)', $this->getDisplayValue(), $this->color_code);
        }

        return $this->getDisplayValue();
    }

    public function scopeByAttribute($query, $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('attribute', function ($q) {
            $q->where('is_active', true);
        });
    }
}
