<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'secur_number',
        'nid_number',
        'email',
        'address',
        'city',
        'country',
        'zip_code',
        'group',
        'land_mark',
        'is_active',
    ];
}
