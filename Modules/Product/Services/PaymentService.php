<?php

namespace Modules\Product\Services;

use App\Interfaces\PaymentServiceInterface;
use App\Models\Payments;

class PaymentService implements PaymentServiceInterface
{
    use ServiceTrait;

    public $model = Payments::class;
}
