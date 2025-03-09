<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'tax_id',
        'brand_id',
        'tag_id',
        'added_by',
        'product_name',
        'price',
        'discount_price',
        'title',
        'code',
        'slug',
        'dimantion',
        'weight',
        'sku',
        'meterials',
        'description',
        'other_info',
        'pro_photo',
        'is_active',
    ];
}
