<?php

namespace Modules\Product\Services;

use App\Interfaces\CouponServiceInterface;
use Modules\Product\Models\Coupon;

class CouponService implements CouponServiceInterface
{
    use ServiceTrait;

    public $model = Coupon::class;
}
