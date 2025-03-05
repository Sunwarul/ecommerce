<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\WarehouseStoreRequest;
use Modules\Product\Http\Requests\WarehouseUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\WarehouseServiceInterface;
use Modules\Product\Models\Warehouse;

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
