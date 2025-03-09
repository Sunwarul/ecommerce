<?php

namespace App\Services;

use App\Interfaces\InventoryServiceInterface;
use App\Models\Inventory;

class InventoryService implements InventoryServiceInterface
{
    use ServiceTrait;

    public $model = Inventory::class;
}
