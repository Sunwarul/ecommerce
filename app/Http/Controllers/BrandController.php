<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\BrandExport;
use App\Http\Requests\BrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $brands = Brand::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhere('updated_at', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage);

        return Inertia::render('Brands/Index', [
            'brands' => $brands,
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
    public function store(BrandStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('brands');
        }
        Brand::create($data);
        return redirect()->route('brands.index')->with('success', 'Brand created successfully');
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
    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('brands');
            // Delete existing photo
            if ($brand->photo && Storage::fileExists($brand->photo)) {
                Storage::delete($brand->photo);
            }
        }
        $res = $brand->update($data);
        return to_route('brands.index')->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->photo && Storage::fileExists($brand->photo)) {
            Storage::delete($brand->photo);
        }
        $brand->delete();
        return to_route('brands.index')->with('success', 'Brand deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['brandIds' => 'required|array']);
        $brandIds = $request->input('brandIds');
        foreach ($brandIds as $id) {
            $brand = Brand::find($id);
            if ($brand->photo && Storage::fileExists($brand->photo)) {
                Storage::delete($brand->photo);
            }
            $brand->delete();
        }
        // Brand::destroy($brandIds);
        return to_route('brands.index')->with('success', 'Brands deleted successfully');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = 'brands-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        return Excel::download(new BrandExport($search), $filename);
    }
}
