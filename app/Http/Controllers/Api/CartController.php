<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\CartServiceInterface;
use App\Models\Cart;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        return $this->cartService->all();
    }

    public function store(CartStoreRequest $request)
    {
        $cart = $this->cartService->store($request->validated());

        return ApiResponse::created($cart, 'Cart created successfully!');
    }

    public function show(Cart $cart)
    {
        return $this->cartService->find(($cart));
    }

    public function update(CartUpdateRequest $request, Cart $cart)
    {
        $cart = $this->cartService->update($request->validated(), $cart);

        return ApiResponse::created($cart, 'Cart update successfully!');
    }

    public function destroy(Cart $cart)
    {
        $cart = $this->cartService->delete(($cart));

        return ApiResponse::created($cart, 'Cart deleted successfully!');
    }
}
