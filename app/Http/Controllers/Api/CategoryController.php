<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Response\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Interfaces\CategoryServiceInterface;

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
        $categories =  $this->categoryService->all();
        return ApiResponse::ok($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->categoryService->find($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category = $this->categoryService->delete($category);
        return ApiResponse::created($category, 'Category deleted successfully');
    }
}
