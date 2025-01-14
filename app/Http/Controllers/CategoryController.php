<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use Exception;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate();
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->file('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('categories');
        }
        Category::create($data);
        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $data = $request->validated();
        if ($request->file('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('categories');
            // Delete existing thumbnail
            if ($category->thumbnail && Storage::fileExists($category->thumbnail)) {
                Storage::delete($category->thumbnail);
            }
        }
        $res = $category->update($data);
        return to_route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->thumbnail && Storage::fileExists($category->thumbnail)) {
            Storage::delete($category->thumbnail);
        }
        $category->delete();
        return to_route('categories.index')->with('success', 'Category deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['categoryIds' => 'required|array']);
        $categoryIds = $request->input('categoryIds');
        foreach ($categoryIds as $id) {
            $category = Category::find($id);
            if ($category->thumbnail && Storage::fileExists($category->thumbnail)) {
                Storage::delete($category->thumbnail);
            }
            $category->delete();
        }
        // Category::destroy($categoryIds);
        return to_route('categories.index')->with('success', 'Categories deleted successfully');
    }
}
