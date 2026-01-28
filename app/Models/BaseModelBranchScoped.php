<?php


namespace App\Models;

use App\Models\Scopes\BranchScope;

class BaseModelBranchScoped extends BaseModel
{
    protected static function booted()
    {
        static::addGlobalScope(new BranchScope());
    }
}
