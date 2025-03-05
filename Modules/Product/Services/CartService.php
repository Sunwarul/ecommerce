<?php

namespace Modules\Product\Services;

use App\Interfaces\CartServiceInterface;
use App\Models\Cart;

class CartService implements CartServiceInterface
{
    use ServiceTrait;

    public $model = Cart::class;
}
