<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15); // Default to 15 if not specified

        $categories = Category::query()
            ->paginate($perPage);

        return Inertia::render('Categories/Index', [
            'categories' => $categories
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
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('categories');
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
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('categories');
            // Delete existing photo
            if ($category->photo && Storage::fileExists($category->photo)) {
                Storage::delete($category->photo);
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
        if ($category->photo && Storage::fileExists($category->photo)) {
            Storage::delete($category->photo);
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
        Category::destroy($categoryIds);
        return to_route('categories.index')->with('success', 'Categories deleted successfully');
    }
}
