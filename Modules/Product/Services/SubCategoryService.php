<?php

namespace Modules\Product\Services;

use App\Interfaces\SubCategoryServiceInterface;
use Modules\Product\Models\SubCategory;

class SubCategoryService implements SubCategoryServiceInterface
{
    use ServiceTrait;

    public $model = SubCategory::class;
}
