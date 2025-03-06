<?php

namespace Modules\Product\Services;

use App\Interfaces\SupplierServiceInterface;
use Modules\Product\Models\Supplier;

class SupplierService implements SupplierServiceInterface
{
    use ServiceTrait;

    public $model = Supplier::class;
}
