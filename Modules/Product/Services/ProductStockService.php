<?php

namespace Modules\Product\Services;

use App\Interfaces\ProductStockServiceInterface;
use App\Models\ProductStock;

class ProductStockService implements ProductStockServiceInterface
{
    use ServiceTrait;

    public $model = ProductStock::class;
}
