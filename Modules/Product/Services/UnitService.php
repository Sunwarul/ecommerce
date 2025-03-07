<?php

namespace Modules\Product\Services;

use App\Interfaces\UnitServiceInterface;
use Modules\Product\Models\Unit;

class UnitService implements UnitServiceInterface
{
    use ServiceTrait;

    public $model = Unit::class;
}
