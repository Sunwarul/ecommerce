<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

trait HasCrud
{
    use HasCrudConfig;
    use HasCustomLogger;

    public function index(Request $request)
    {
        $this->ensureModelClass();

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
            $query->when($request->trashed, fn ($query) => $query->onlyTrashed());
        }

        $query = $this->modifyQuery($query);
        $items = $query->latest()->paginate($perPage);

        $dataArray = [
            'items' => $items,
            'filters' => ['search' => $search],
            'config' => $this->makeConfig(),
            ...$this->addProps(),
        ];

        return Inertia::render($this->componentPath, $dataArray);
    }

    protected function addProps(): array
    {
        return [];
    }

    protected function modifyQuery($query)
    {
        return $query;
    }

    public function create()
    {
        return Inertia::render($this->componentPath);
    }

    public function store(Request $request)
    {
        $this->ensureModelClass();
        $validatedData = app($this->storeRequestClass)->validated();
        if ($request->file('photo')) {
            $validatedData['photo'] = $request->file('photo')->store($this->resource);
        }
        $model = new $this->modelClass;
        $model->fill($validatedData);
        $model->save();
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
        $res = $model->update($validatedData);

        return to_route($this->resource.'.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $model = $this->modelClass::findOrFail($id);
        // if ($model->photo && Storage::exists($model->photo)) {
        //     Storage::delete($model->photo);
        // }
        $model->delete();

        return to_route($this->resource.'.index')->with('success', 'Deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:'.$this->modelClass.',id']);
        foreach ($request->ids as $id) {
            $model = $this->modelClass::find($id);
            if ($model) {
                $model->delete();
            }
        }

        return to_route($this->resource.'.index')->with('success', 'Items deleted successfully');
    }

    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:'.$this->modelClass.',id']);
        $this->modelClass::whereIn('id', $request->ids)->restore();
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:'.$this->modelClass.',id']);
        // $this->modelClass::whereIn('id', $request->ids)->forceDelete();
        foreach ($request->ids as $id) {
            $model = $this->modelClass::withTrashed()->find($id);
            if ($model) {
                if ($model->photo && Storage::exists($model->photo)) {
                    Storage::delete($model->photo);
                }
                $model->forceDelete();
            }
        }
    }

    public function export(Request $request)
    {
        $search = $request->input('search');

        $filename = strtolower(class_basename($this->modelClass)).'-'.now()->format('Y-m-d-H-i-s').'.xlsx';

        return Excel::download(new $this->exportClass($search), $filename);
    }

    protected function ensureModelClass()
    {
        if (! $this->modelClass) {
            throw new \Exception('Model class not defined in trait usage');
        }
    }
}
