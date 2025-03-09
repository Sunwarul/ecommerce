<?php

namespace App\Services;

use App\Interfaces\SubCategoryServiceInterface;
use App\Models\SubCategory;

class SubCategoryService implements SubCategoryServiceInterface
{
    use ServiceTrait;

    public $model = SubCategory::class;
}
