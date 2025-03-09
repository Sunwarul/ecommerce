<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\SupplierServiceInterface;
use App\Models\Supplier;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierServiceInterface $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        return $this->supplierService->all();
    }

    public function store(SupplierStoreRequest $request)
    {
        $supplier = $this->supplierService->store($request->validated());

        return ApiResponse::created($supplier, 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        return $this->supplierService->find($supplier);
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $supplier = $this->supplierService->update($request->validated(), $supplier);

        return ApiResponse::created($supplier, 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier = $this->supplierService->delete($supplier);

        return ApiResponse::created($supplier, 'Supplier deleted successfully!');
    }
}
