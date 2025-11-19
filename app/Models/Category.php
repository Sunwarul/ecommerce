<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Str;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'parent_id',
        'name',
        'slug',
        'slug',
        'photo',
        'is_active',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // children
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // mutator for convert title to slug
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
