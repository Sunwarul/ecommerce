<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\WarehouseServiceInterface;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    private $warehouseService;

    public function __construct(WarehouseServiceInterface $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        return $this->warehouseService->all();
    }

    public function store(WarehouseStoreRequest $request)
    {
        $warehouse = $this->warehouseService->store($request->validated());

        return ApiResponse::created($warehouse, 'Warehouse created successfully!');
    }

    public function show(Warehouse $warehouse)
    {
        return $this->warehouseService->find($warehouse);
    }

    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse)
    {
        $warehouse = $this->warehouseService->update($request->validated(), $warehouse);

        return ApiResponse::created($warehouse, 'Warehouse updated successfully!');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse = $this->warehouseService->delete($warehouse);

        return ApiResponse::created($warehouse, 'Warehouse deleted successfully!');
    }
}
