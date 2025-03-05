<?php

namespace Modules\Product\Services;

use App\Interfaces\ProductServiceInterface;
use App\Models\Product;

class ProductService implements ProductServiceInterface
{
    use ServiceTrait;

    public $model = Product::class;
}
