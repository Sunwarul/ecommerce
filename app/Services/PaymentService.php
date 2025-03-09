<?php

namespace App\Services;

use App\Interfaces\PaymentServiceInterface;
use App\Models\Payments;

class PaymentService implements PaymentServiceInterface
{
    use ServiceTrait;

    public $model = Payments::class;
}
