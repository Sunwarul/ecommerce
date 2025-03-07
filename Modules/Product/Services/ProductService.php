<?php

namespace Modules\Product\Services;

use App\Interfaces\ProductServiceInterface;
use Modules\Product\Models\Product;

class ProductService implements ProductServiceInterface
{
    use ServiceTrait;

    public $model = Product::class;
}
