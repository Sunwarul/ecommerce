<?php

namespace Modules\Product\Services;

use App\Interfaces\WarehouseServiceInterface;
use Modules\Product\Models\Warehouse;

class WarehouseService implements WarehouseServiceInterface
{
    use ServiceTrait;

    public $model = Warehouse::class;
}
