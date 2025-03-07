<?php

namespace Modules\Product\Services;

use App\Interfaces\CustomerServiceInterface;
use Modules\Product\Models\Customer;

class CustomerService implements CustomerServiceInterface
{
    use ServiceTrait;

    public $model = Customer::class;
}
