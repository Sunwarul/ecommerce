<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarrantyGuaranteeStoreRequest;
use App\Http\Requests\WarrantyGuaranteeUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\WarrantyServiceInterface;
use App\Models\WarrantyGuarantee;

class WarrantyGuaranteeController extends Controller
{
    protected $warrantyService;

    public function __construct(WarrantyServiceInterface $warrantyService)
    {
        $this->warrantyService = $warrantyService;
    }

    public function index()
    {
        return $this->warrantyService->all();
    }

    public function store(WarrantyGuaranteeStoreRequest $request)
    {
        $warranty = $this->warrantyService->store($request->validated());

        return ApiResponse::created($warranty, 'Warranty created successully!');
    }

    public function show(WarrantyGuarantee $warranty)
    {
        return $this->warrantyService->find($warranty);
    }

    public function update(WarrantyGuaranteeUpdateRequest $request, WarrantyGuarantee $warranty)
    {
        $warranty = $this->warrantyService->update($request->validated(), $warranty);

        return ApiResponse::updated($warranty, 'Warranty update successully!');
    }

    public function destroy(WarrantyGuarantee $warranty)
    {
        $warranty = $this->warrantyService->delete($warranty);

        return ApiResponse::created($warranty, 'Warranty deleted successully!');
    }
}
