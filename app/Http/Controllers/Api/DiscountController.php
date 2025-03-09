<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountStoreRequest;
use App\Http\Requests\DiscountUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\DiscountServiceInterface;
use App\Models\Discount;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountServiceInterface $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        return $this->discountService->all();
    }

    public function store(DiscountStoreRequest $request)
    {
        $discount = $this->discountService->store($request->validated());

        return ApiResponse::created($discount, 'Discount created successfully!');
    }

    public function show(Discount $discount)
    {
        return $this->discountService->find($discount);
    }

    public function update(DiscountUpdateRequest $request, Discount $discount)
    {
        $discount = $this->discountService->update($request->validated(), $discount);

        return ApiResponse::updated($discount, 'Discount updated successfully!');
    }

    public function destroy(Discount $discount)
    {
        $discount = $this->discountService->delete($discount);

        return ApiResponse::created($discount, 'Discount deleted successfully!');
    }
}
