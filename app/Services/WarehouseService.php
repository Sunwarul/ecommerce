<?php

namespace App\Services;

use App\Interfaces\WarehouseServiceInterface;
use App\Models\Warehouse;

class WarehouseService implements WarehouseServiceInterface
{
    use ServiceTrait;

    public $model = Warehouse::class;
}
