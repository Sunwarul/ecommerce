<?php

namespace Modules\Product\Services;

use App\Interfaces\WarrantyServiceInterface;
use App\Models\WarrantyGuarantee;

class WarrantyService implements WarrantyServiceInterface
{
    use ServiceTrait;

    public $model = WarrantyGuarantee::class;
}
