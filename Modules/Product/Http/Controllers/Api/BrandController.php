<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\BrandStoreRequest;
use Modules\Product\Http\Requests\BrandUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\BrandServiceInterface;
use Modules\Product\Models\Brand;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->brandService->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Modules\Product\Http\Requests\StoreBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandStoreRequest $request)
    {
        $brand = $this->brandService->store($request->validated());

        return ApiResponse::created($brand, 'Brand created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return $this->brandService->find($brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Modules\Product\Http\Requests\UpdateBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $brand = $this->brandService->update($request->validated(), $brand);

        return ApiResponse::updated($brand, 'Brand updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand = $this->brandService->delete($brand);

        return ApiResponse::created($brand, 'Brand deleted successfully!');
    }
}
