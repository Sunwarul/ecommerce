<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\UnitStoreRequest;
use Modules\Product\Http\Requests\UnitUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\UnitServiceInterface;
use Modules\Product\Models\Unit;

class UnitController extends Controller
{
    private $unitService;

    public function __construct(UnitServiceInterface $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        return $this->unitService->all();
    }

    public function store(UnitStoreRequest $request)
    {
        $unit = $this->unitService->store($request->validated());

        return ApiResponse::created($unit, 'Unit created successfully!');
    }

    public function show(Unit $unit)
    {
        return $this->unitService->find($unit);
    }

    public function update(UnitUpdateRequest $request, Unit $unit)
    {
        $unit = $this->unitService->update($request->validated(), $unit);

        return ApiResponse::created($unit, 'Unit updated successfully!');
    }

    public function destroy(Unit $unit)
    {
        $unit = $this->unitService->delete($unit);

        return ApiResponse::created($unit, 'Unit deleted successfully!');
    }
}
