<?php

namespace Modules\Product\Services;

use App\Interfaces\CategoryServiceInterface;
use Modules\Product\Models\Category;

class CategoryService implements CategoryServiceInterface
{
    use ServiceTrait;

    public $model = Category::class;
}
