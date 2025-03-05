<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\SupplierStoreRequest;
use Modules\Product\Http\Requests\SupplierUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\SupplierServiceInterface;
use Modules\Product\Models\Supplier;

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
