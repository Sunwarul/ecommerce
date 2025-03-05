<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\TagStoreRequest;
use Modules\Product\Http\Requests\TagUpdateRequest;
use Modules\Product\Interfaces\TagServiceInterface;
use Modules\Product\Models\Tag;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagServiceInterface $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        return $this->tagService->all();
    }

    public function create()
    {
        //
    }

    public function store(TagStoreRequest $request)
    {
        $tagStore = $this->tagService->store($request->validated());

        return response()->json(['data' => $tagStore, 'message' => 'Tag created successfully!'], 201);
    }

    public function show(Tag $tag)
    {
        return $this->tagService->find($tag);
    }

    public function edit(Tag $tag)
    {
        //
    }

    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $tagUpdate = $this->tagService->update($request->validated(), $tag);

        return response()->json(['data' => $tagUpdate, 'message' => 'Tag updated successfully!'], 201);
    }

    public function destroy(Tag $tag)
    {
        return $this->tagService->delete($tag);
    }
}
