<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'is_active',
        'parent_id',
    ];

    /**
     * Parent category (nullable)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Direct children categories
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Recursive children (for nested trees)
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Check if this category is a descendant of another category (to prevent cycles)
     */
    public function isDescendantOf($possibleAncestorId): bool
    {
        $parent = $this->parent;
        while ($parent !== null) {
            if ((int)$parent->id === (int)$possibleAncestorId) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }
}
