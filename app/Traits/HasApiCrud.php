<?php

namespace App\Traits;

use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

trait HasApiCrud
{
    use HasCrudConfig;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = $this->modelClass::query();

        if (! empty($this->withRelations)) {
            $query->with($this->withRelations);
        }

        $query->when($search, function ($query, $search) {
            if (isset($this->searchColumns) && ! empty($this->searchColumns)) {
                $query->where(function ($query) use ($search) {
                    foreach ($this->searchColumns as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        });

        if ($request->has('trashed')) {
            $query->when($request->trashed, fn($query) => $query->onlyTrashed());
        }

        $query = $this->modifyQuery($query);
        $items = $query->latest()->paginate($perPage);

        return ApiResponse::ok($items);
    }

    protected function modifyQuery($query)
    {
        return $query;
    }

    public function store(Request $request)
    {
        $validatedData = app($this->storeRequestClass)->validated();
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
        }
        $model = new $this->modelClass;
        $model->fill($validatedData);
        $model->save();

        return ApiResponse::created($model);
    }

    public function update(Request $request, $id)
    {
        $validatedData = app($this->updateRequestClass)->validated();
        $model = $this->modelClass::findOrFail($id);
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
            if ($model->photo && Storage::fileExists($model->photo)) {
                Storage::delete($model->photo);
            }
        }
        $model->update($validatedData);

        return ApiResponse::updated($model);
    }

    public function show($id)
    {
        $model = $this->modelClass::findOrFail($id);

        return ApiResponse::ok($model);
    }

    public function destroy($id)
    {
        $model = $this->modelClass::findOrFail($id);
        if ($model->photo && Storage::exists($model->photo)) {
            Storage::delete($model->photo);
        }
        $model->delete();

        return ApiResponse::deleted(null);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:' . $this->modelClass . ',id']);
        foreach ($request->ids as $id) {
            $model = $this->modelClass::find($id);
            if ($model) {
                $model->delete();
            }
        }

        return ApiResponse::deleted(null, __('Items deleted successfully'));
    }

    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:' . $this->modelClass . ',id']);
        $this->modelClass::whereIn('id', $request->ids)->restore();

        return ApiResponse::updated(null, __('Items restored successfully'));
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:' . $this->modelClass . ',id']);
        // $this->modelClass::whereIn('id', $request->ids)->forceDelete();
        foreach ($request->ids as $id) {
            $model = $this->modelClass::find($id);
            if ($model) {
                if ($model->photo && Storage::exists($model->photo)) {
                    Storage::delete($model->photo);
                }
                $model->forceDelete();
            }
        }

        return ApiResponse::deleted(null, __('Items permanently deleted successfully'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $filename = strtolower(class_basename($this->modelClass)) . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(new $this->exportClass($search), $filename);
    }

    protected function ensureModelClass()
    {
        if (! $this->modelClass) {
            throw new \Exception('Model class not defined in trait usage');
        }
    }
}