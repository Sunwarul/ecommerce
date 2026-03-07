<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CategoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'categories',
            modelClass: Category::class,
            storeRequestClass: CategoryStoreRequest::class,
            updateRequestClass: CategoryUpdateRequest::class,
            searchColumns: ['name'],
            exportClass: CategoryExport::class,
            componentPath: 'Admin/Categories/Index',
            withRelations: ['parent'],
        ));
    }

    protected function addProps(): array
    {
        $parentCategories = Category::select('id', 'name')->whereNull('parent_id')->get();

        return [
            'parentCategories' => $parentCategories,
        ];
    }

    public function store(Request $request)
    {
        $this->ensureModelClass();
        $validatedData = app($this->storeRequestClass)->validated();
        $model = new $this->modelClass;
        $model->fill($validatedData);
        $model->save();

        cache()->increment('categories_version', 1);

        return to_route('categories.index')->with('success', 'Created successfully');
    }

    public function update(Request $request, $id)
    {
        $validatedData = app($this->updateRequestClass)->validated();
        $model = $this->modelClass::findOrFail($id);
        $model->update($validatedData);

        cache()->increment('categories_version', 1);

        return to_route('categories.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $model = $this->modelClass::findOrFail($id);
        $model->delete();

        cache()->increment('categories_version', 1);

        return to_route('categories.index')->with('success', 'Deleted successfully');
    }
}
