<?php

namespace Modules\Product\Services;

use App\Interfaces\DiscountServiceInterface;
use Modules\Product\Models\Discount;

class DiscountService implements DiscountServiceInterface
{
    use ServiceTrait;

    public $model = Discount::class;
}
