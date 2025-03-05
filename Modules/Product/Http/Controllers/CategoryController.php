<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Models\Category;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Exports\CategoriesExport;
use Modules\Product\Http\Requests\CategoryStoreRequest;
use Modules\Product\Http\Requests\CategoryUpdateRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $categories = Category::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhere('updated_at', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
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

    /**
     * Export categories to Excel
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = 'categories-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(new CategoriesExport($search), $filename);
    }
}
