<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =
    [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'zip_code',
        'land_mark',
        'is_active',
    ];
}
