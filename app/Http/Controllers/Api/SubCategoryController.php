<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryStoreRequest;
use App\Http\Requests\SubCategoryUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Interfaces\SubCategoryServiceInterface;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    protected $subCategory;

    public function __construct(SubCategoryServiceInterface $subCategory)
    {
        $this->subCategory = $subCategory;
    }

    public function index()
    {
        return $this->subCategory->all();
    }

    public function store(SubCategoryStoreRequest $request)
    {
        $subCategory = $this->subCategory->store($request->validated());

        return ApiResponse::created($subCategory, 'SubCategory created successfully!');
    }

    public function show(SubCategory $subCategory)
    {
        return $this->subCategory->find($subCategory);
    }

    public function update(SubCategoryUpdateRequest $request, SubCategory $subCategory)
    {
        $subCategory = $this->subCategory->update($request->validated(), $subCategory);

        return ApiResponse::created($subCategory, 'SubCategory Updated successfully!');
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory = $this->subCategory->delete($subCategory);

        return ApiResponse::created($subCategory, 'SubCategory deleted successfully!');
    }

    public function create()
    {
        //
    }

    public function edit(SubCategory $subCategory)
    {
        //
    }
}
