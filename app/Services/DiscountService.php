<?php

namespace App\Services;

use App\Interfaces\DiscountServiceInterface;
use App\Models\Discount;

class DiscountService implements DiscountServiceInterface
{
    use ServiceTrait;

    public $model = Discount::class;
}
