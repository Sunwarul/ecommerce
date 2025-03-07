<?php

namespace Modules\Product\Services;

use App\Interfaces\PaymentServiceInterface;
use Modules\Product\Models\Payments;

class PaymentService implements PaymentServiceInterface
{
    use ServiceTrait;

    public $model = Payments::class;
}
