<?php

namespace App\Services;

use App\Interfaces\ProductServiceInterface;
use App\Models\Product;

class ProductService implements ProductServiceInterface
{
    use ServiceTrait;

    public $model = class;
}
