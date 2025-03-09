<?php

namespace App\Services;

use App\Interfaces\CustomerServiceInterface;
use App\Models\Customer;

class CustomerService implements CustomerServiceInterface
{
    use ServiceTrait;

    public $model = Customer::class;
}
