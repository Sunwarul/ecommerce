<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\CustomerStoreRequest;
use Modules\Product\Http\Requests\CustomerUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\CustomerServiceInterface;
use Modules\Product\Models\Customer;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return $this->customerService->all();
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = $this->customerService->store($request->validated());

        return ApiResponse::created($customer, 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        return $this->customerService->find($customer);
    }

    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customer = $this->customerService->update($request->validated(), $customer);

        return ApiResponse::created($customer, 'Customer created successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer = $this->customerService->delete($customer);

        return ApiResponse::created($customer, 'Customer deleted successfully!');
    }
}
