<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStockStoreRequest;
use App\Http\Requests\ProductStockUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\ProductStockServiceInterface;
use App\Models\ProductStock;

class ProductStockController extends Controller
{
    protected $productStockService;

    public function __construct(ProductStockServiceInterface $productStockService)
    {
        $this->productStockService = $productStockService;
    }

    public function index()
    {
        return $this->productStockService->all();
    }

    public function store(ProductStockStoreRequest $request)
    {
        $productStock = $this->productStockService->store($request->validated());

        return ApiResponse::created($productStock, 'Product stock created successfully!');
    }

    public function show(ProductStock $productStock)
    {
        return $this->productStockService->find($productStock);
    }

    public function update(ProductStockUpdateRequest $request, ProductStock $productStock)
    {
        $productStock = $this->productStockService->update($request->validated(), $productStock);

        return ApiResponse::created($productStock, 'Product stock updated successfully!');
    }

    public function destroy(ProductStock $productStock)
    {
        $productStock = $this->productStockService->delete($productStock);

        return ApiResponse::created($productStock, 'Product stock deleted successfully!');
    }
}
