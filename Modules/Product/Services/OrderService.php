<?php

namespace Modules\Product\Services;

use App\Interfaces\OrderServiceInterface;
use App\Models\Order;

class OrderService implements OrderServiceInterface
{
    use ServiceTrait;

    public $model = Order::class;
}
