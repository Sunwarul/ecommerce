<?php

namespace Modules\Product\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Models\Category;

class CategoryService implements CategoryServiceInterface
{
    use ServiceTrait;

    public $model = Category::class;
}
