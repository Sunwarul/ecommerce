<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\CartStoreRequest;
use Modules\Product\Http\Requests\CartUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\CartServiceInterface;
use Modules\Product\Models\Cart;

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
