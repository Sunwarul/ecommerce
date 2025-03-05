<?php

namespace Modules\Product\Services;

use App\Interfaces\ShoppingServiceInterface;
use App\Models\Shopping;

class ShoppingService implements ShoppingServiceInterface
{
    use ServiceTrait;

    public $model = Shopping::class;
}
