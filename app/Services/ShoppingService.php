<?php

namespace App\Services;

use App\Interfaces\ShoppingServiceInterface;
use App\Models\Shopping;

class ShoppingService implements ShoppingServiceInterface
{
    use ServiceTrait;

    public $model = Shopping::class;
}
