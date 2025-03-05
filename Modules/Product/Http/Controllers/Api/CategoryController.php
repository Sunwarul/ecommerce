<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\CategoryStoreRequest;
use Modules\Product\Http\Requests\CategoryUpdateRequest;
use Modules\Product\Http\Response\ApiResponse;
use Modules\Product\Interfaces\CategoryServiceInterface;
use Modules\Product\Models\Category;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryServiceInterface $categoryService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->categoryService->all();

        return ApiResponse::ok($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Modules\Product\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        $category = $this->categoryService->store($request->validated());

        return ApiResponse::created($category, 'Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->categoryService->find($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Modules\Product\Http\Requests\UpdateCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category = $this->categoryService->update($request->validated(), $category);

        return ApiResponse::created($category, 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category = $this->categoryService->delete($category);

        return ApiResponse::created($category, 'Category deleted successfully');
    }
}
