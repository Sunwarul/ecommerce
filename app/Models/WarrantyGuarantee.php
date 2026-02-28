<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarrantyGuarantee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type', 'duration', 'description', 'is_active', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
