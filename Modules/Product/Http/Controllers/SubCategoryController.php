<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Models\Category;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Models\SubCategory;
use Modules\Product\Exports\SubCategoriesExport;
use Modules\Product\Http\Requests\SubCategoryStoreRequest;
use Modules\Product\Http\Requests\SubCategoryUpdateRequest;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $categories = Category::get();
        $sub_categories = SubCategory::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhere('updated_at', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage);

        return Inertia::render('Product::SubCategories/Index', [
            'sub_categories' => $sub_categories,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(SubCategoryStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('sub-categories');
        }
        SubCategory::create($data);
        return redirect()->route('sub-categories.index')->with('success', 'Sub Category created successfully');
    }

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

    public function destroy(SubCategory $sub_category)
    {
        if ($sub_category->photo && Storage::fileExists($sub_category->photo)) {
            Storage::delete($sub_category->photo);
        }
        $sub_category->delete();
        return to_route('sub-categories.index')->with('success', 'Category deleted successfully');
    }

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

    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = 'sub-categories-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        return Excel::download(new SubCategoriesExport($search), $filename);
    }
}
