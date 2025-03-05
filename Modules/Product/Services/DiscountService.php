<?php

namespace Modules\Product\Services;

use App\Interfaces\DiscountServiceInterface;
use App\Models\Discount;

class DiscountService implements DiscountServiceInterface
{
    use ServiceTrait;

    public $model = Discount::class;
}
