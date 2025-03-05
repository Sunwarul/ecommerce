<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\CouponStoreRequest;
use Modules\Product\Http\Requests\CouponUpdateRequest;
use Modules\Product\Interfaces\CouponServiceInterface;
use Modules\Product\Models\Coupon;

class CouponController extends Controller
{
    private $couponService;

    public function __construct(CouponServiceInterface $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        return $this->couponService->all();
    }

    public function create()
    {
        //
    }

    public function store(CouponStoreRequest $request)
    {
        $couponStore = $this->couponService->store($request->validated());

        return response()->json(['data' => $couponStore, 'message' => 'Coupon created successfully!'], 201);
    }

    public function show(Coupon $coupon)
    {
        return $this->couponService->find($coupon);
    }

    public function edit(Coupon $coupon)
    {
        //
    }

    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $couponUpdate = $this->couponService->update($request->validated(), $coupon);

        return response()->json(['data' => $couponUpdate, 'message' => 'Coupon updated successfully!'], 201);
    }

    public function destroy(Coupon $coupon)
    {
        $couponDelete = $this->couponService->delete($coupon);

        return response()->json(['data' => $couponDelete, 'message' => 'Coupon deleted successfully!'], 200);
    }
}
