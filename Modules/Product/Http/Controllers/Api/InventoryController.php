<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\InventoryStoreRequest;
use Modules\Product\Http\Requests\InventoryUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\InventoryServiceInterface;
use Modules\Product\Models\Inventory;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        return $this->inventoryService->all();
    }

    public function store(InventoryStoreRequest $request)
    {
        $inventory = $this->inventoryService->store($request->validated());

        return ApiResponse::created($inventory, 'Inventory created successfully!');
    }

    public function show(Inventory $inventory)
    {
        return $this->inventoryService->find($inventory);
    }

    public function update(InventoryUpdateRequest $request, Inventory $inventory)
    {
        $inventory = $this->inventoryService->update($request->validated(), $inventory);

        return ApiResponse::created($inventory, 'Inventory updated successfully!');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory = $this->inventoryService->delete($inventory);

        return ApiResponse::created($inventory, 'Inventory deleted successfully!');
    }
}
