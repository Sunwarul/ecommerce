<?php

namespace App\Services;

use App\Interfaces\UnitServiceInterface;
use App\Models\Unit;

class UnitService implements UnitServiceInterface
{
    use ServiceTrait;

    public $model = Unit::class;
}
