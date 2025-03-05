<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\ShoppingStoreRequest;
use Modules\Product\Http\Requests\ShoppingUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\ShoppingServiceInterface;
use Modules\Product\Models\Shopping;

class ShoppingController extends Controller
{
    protected $shoppingService;

    public function __construct(ShoppingServiceInterface $shoppingService)
    {
        $this->shoppingService = $shoppingService;
    }

    public function index()
    {
        return $this->shoppingService->all();
    }

    public function store(ShoppingStoreRequest $request)
    {
        $shopping = $this->shoppingService->store($request->validated());

        return ApiResponse::created($shopping, 'Shopping created successfully!');
    }

    public function show(Shopping $shopping)
    {
        return $this->shoppingService->find($shopping);
    }

    public function update(ShoppingUpdateRequest $request, Shopping $shopping)
    {
        $shopping = $this->shoppingService->update($request->validated(), $shopping);

        return ApiResponse::created($shopping, 'Shopping update successfully!');
    }

    public function destroy(Shopping $shopping)
    {
        $shopping = $this->shoppingService->delete($shopping);

        return ApiResponse::created($shopping, 'Shopping deleted successfully!');
    }
}
