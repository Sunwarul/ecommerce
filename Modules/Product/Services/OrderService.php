<?php

namespace Modules\Product\Services;

use App\Interfaces\OrderServiceInterface;
use Modules\Product\Models\Order;

class OrderService implements OrderServiceInterface
{
    use ServiceTrait;

    public $model = Order::class;
}
