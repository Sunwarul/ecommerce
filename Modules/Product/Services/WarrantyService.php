<?php

namespace Modules\Product\Services;

use App\Interfaces\WarrantyServiceInterface;
use Modules\Product\Models\WarrantyGuarantee;

class WarrantyService implements WarrantyServiceInterface
{
    use ServiceTrait;

    public $model = WarrantyGuarantee::class;
}
