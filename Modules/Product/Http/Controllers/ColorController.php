<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Product\Models\Color;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Http\Requests\ColorStoreRequest;
use Modules\Product\Http\Requests\ColorUpdateRequest;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::paginate();
        return Inertia::render('Product::Colors/Index', [
            'colors' => $colors,
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
    public function store(ColorStoreRequest $request)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('colors');
        }
        Color::create($data);
        return redirect()->route('colors.index')->with('success', 'Color created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ColorUpdateRequest $request, Color $color)
    {
        $data = $request->validated();
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('colors');
            // Delete existing photo
            if ($color->photo && Storage::fileExists($color->photo)) {
                Storage::delete($color->photo);
            }
        }
        $res = $color->update($data);
        return to_route('colors.index')->with('success', 'Color updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        if ($color->photo && Storage::fileExists($color->photo)) {
            Storage::delete($color->photo);
        }
        $color->delete();
        return to_route('colors.index')->with('success', 'Color deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['colorIds' => 'required|array']);
        $colorIds = $request->input('colorIds');
        foreach ($colorIds as $id) {
            $color = Color::find($id);
            if ($color->photo && Storage::fileExists($color->photo)) {
                Storage::delete($color->photo);
            }
            $color->delete();
        }
        // color::destroy($colorIds);
        return to_route('colors.index')->with('success', 'colors deleted successfully');
    }
}
