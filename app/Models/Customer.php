<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_type',
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'tax_number',
        'currency_id',
        'status',
        'address',
        'opening_balance',
        'opening_balance_date',
        'opening_balance_type',
        'credit_limit',
        'has_credit_limit',
        'photo',
        'file',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'id' => 'integer',
    //         'status' => 'boolean',
    //         'opening_balance_date' => 'date',
    //         'has_credit_limit' => 'boolean',
    //     ];
    // }
}
