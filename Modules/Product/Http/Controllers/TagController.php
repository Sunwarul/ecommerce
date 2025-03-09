<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Product\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\TagExport;
use Modules\Product\Http\Requests\TagStoreRequest;
use Modules\Product\Http\Requests\TagUpdateRequest;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();

        return Inertia::render('Product::Tags/Index', [
            'items' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Product::Tags/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagStoreRequest $request)
    {
        $data = $request->validated();
        Tag::create($data);
        // return redirect()->route('tags.index')->with('success', 'Tag created successfully');
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
    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $data = $request->validated();
        $res = $tag->update($data);
        return to_route('tags.index')->with('success', 'Tag updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->photo && Storage::fileExists($tag->photo)) {
            Storage::delete($tag->photo);
        }
        $tag->delete();
        return to_route('tags.index')->with('success', 'Tag deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $tagIds = $request->input('ids');
        foreach ($tagIds as $id) {
            $tag = Tag::find($id);
            if ($tag) {
                if ($tag->photo && Storage::fileExists($tag->photo)) {
                    Storage::delete($tag->photo);
                }
                $tag->delete();
            }
        }
        return to_route('tags.index')->with('success', 'Tags deleted successfully');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = 'tags-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        return Excel::download(new TagExport($search), $filename);
    }
}
