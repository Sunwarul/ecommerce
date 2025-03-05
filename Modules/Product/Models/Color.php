<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name',
        'color_code',
        'is_active'
    ];
}
