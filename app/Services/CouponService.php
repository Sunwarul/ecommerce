<?php

namespace App\Services;

use App\Interfaces\CouponServiceInterface;
use App\Models\Coupon;

class CouponService implements CouponServiceInterface
{
    use ServiceTrait;

    public $model = Coupon::class;
}
