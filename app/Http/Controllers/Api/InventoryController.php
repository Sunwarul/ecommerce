<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Requests\InventoryUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\InventoryServiceInterface;
use App\Models\Inventory;

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
