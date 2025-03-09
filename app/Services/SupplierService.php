<?php

namespace App\Services;

use App\Interfaces\SupplierServiceInterface;
use App\Models\Supplier;

class SupplierService implements SupplierServiceInterface
{
    use ServiceTrait;

    public $model = Supplier::class;
}
