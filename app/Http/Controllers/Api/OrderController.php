<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\OrderServiceInterface;
use App\Models\Order;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return $this->orderService->all();
    }

    public function store(OrderStoreRequest $request)
    {
        $order = $this->orderService->store($request->validated());

        return ApiResponse::created($order, 'Order created successfully!');
    }

    public function show(Order $order)
    {
        return $this->orderService->find($order);
    }

    public function update(OrderUpdateRequest $request, Order $order)
    {
        $order = $this->orderService->update($request->validated(), $order);

        return ApiResponse::created($order, 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $order = $this->orderService->delete($order);

        return ApiResponse::created($order, 'Order deleted successfully!');
    }
}
