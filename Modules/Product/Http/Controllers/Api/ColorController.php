<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\ColorStoreRequest;
use Modules\Product\Http\Requests\ColorUpdateRequest;
use Modules\Product\Interfaces\ColorServiceInterface;
use Modules\Product\Models\Color;

class ColorController extends Controller
{
    protected $colorService;

    public function __construct(ColorServiceInterface $colorService)
    {
        $this->colorService = $colorService;
    }

    public function index()
    {
        return $this->colorService->all();
    }

    public function create()
    {
        //
    }

    public function store(ColorStoreRequest $request)
    {
        $colorStore = $this->colorService->store($request->validated());

        return response()->json(['data' => $colorStore, 'message' => 'Color created successfully!'], 201);
    }

    public function show(Color $color)
    {
        return $this->colorService->find($color);
    }

    public function edit(Color $color)
    {
        //
    }

    public function update(ColorUpdateRequest $request, Color $color)
    {
        $colorUpdate = $this->colorService->update($request->validated(), $color);

        return response()->json(['data' => $colorUpdate, 'message' => 'Color updated successfully!'], 201);
    }

    public function destroy(Color $color)
    {
        return $this->colorService->delete($color);
    }
}
