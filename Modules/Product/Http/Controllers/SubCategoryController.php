<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Product\Models\Category;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Models\SubCategory;
use Modules\Product\Http\Requests\SubCategoryStoreRequest;
use Modules\Product\Http\Requests\SubCategoryUpdateRequest;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_categories = SubCategory::with('category')->paginate();
        $categories = Category::get();
        return Inertia::render('SubCategories/Index', [
            'sub_categories' => $sub_categories,
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
    public function store(SubCategoryStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('sub-categories');
        }
        SubCategory::create($data);
        return redirect()->route('sub-categories.index')->with('success', 'Sub Category created successfully');
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
    public function update(SubCategoryUpdateRequest $request, SubCategory $sub_category)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('categories');
            // Delete existing photo
            if ($sub_category->photo && Storage::fileExists($sub_category->photo)) {
                Storage::delete($sub_category->photo);
            }
        }
        $res = $sub_category->update($data);
        return to_route('sub-categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $sub_category)
    {
        if ($sub_category->photo && Storage::fileExists($sub_category->photo)) {
            Storage::delete($sub_category->photo);
        }
        $sub_category->delete();
        return to_route('sub-categories.index')->with('success', 'Category deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['sub_categoryIds' => 'required|array']);
        $sub_categoryIds = $request->input('sub_categoryIds');
        foreach ($sub_categoryIds as $id) {
            $sub_category = SubCategory::find($id);
            if ($sub_category->photo && Storage::fileExists($sub_category->photo)) {
                Storage::delete($sub_category->photo);
            }
            $sub_category->delete();
        }
        return to_route('sub-categories.index')->with('success', 'Sub-Categories deleted successfully');
    }
}
