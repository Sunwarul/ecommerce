<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Product\Models\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Http\Requests\BrandStoreRequest;
use Modules\Product\Http\Requests\BrandUpdateRequest;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate();
        return Inertia::render('Brands/Index', [
            'brands' => $brands,
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
}
