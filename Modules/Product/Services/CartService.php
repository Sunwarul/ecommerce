<?php

namespace Modules\Product\Services;

use App\Interfaces\CartServiceInterface;
use Modules\Product\Models\Cart;

class CartService implements CartServiceInterface
{
    use ServiceTrait;

    public $model = Cart::class;
}
