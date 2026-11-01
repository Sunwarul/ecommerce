<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmiBank extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'short_name', 'notes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tenures()
    {
        return $this->hasMany(EmiBankTenure::class)->orderBy('months');
    }
}
