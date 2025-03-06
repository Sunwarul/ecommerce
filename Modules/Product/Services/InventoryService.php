<?php

namespace Modules\Product\Services;

use App\Interfaces\InventoryServiceInterface;
use Modules\Product\Models\Inventory;

class InventoryService implements InventoryServiceInterface
{
    use ServiceTrait;

    public $model = Inventory::class;
}
