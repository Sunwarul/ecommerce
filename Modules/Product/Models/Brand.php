<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Database\Factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'photo', 'is_active'];


    protected static function newFactory()
    {
        return BrandFactory::new();
    }
}
