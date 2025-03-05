<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\WarrantyGuaranteeStoreRequest;
use Modules\Product\Http\Requests\WarrantyGuaranteeUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\WarrantyServiceInterface;
use Modules\Product\Models\WarrantyGuarantee;

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
