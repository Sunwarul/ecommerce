<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BrandExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandStoreRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use App\Models\Brand;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'brands',
            modelClass: Brand::class,
            storeRequestClass: BrandStoreRequest::class,
            updateRequestClass: BrandUpdateRequest::class,
            componentPath: 'Admin/Brands/Index',
            searchColumns: ['name'],
            exportClass: BrandExport::class,
            withRelations: [],
        ));
    }

    public function store(Request $request)
    {
        $this->ensureModelClass();
        $validatedData = app($this->storeRequestClass)->validated();
        $model = new $this->modelClass;
        $model->fill($validatedData);
        $model->save();

        cache()->increment('brands_version', 1);

        return to_route('brands.index')->with('success', 'Created successfully');
    }

    public function update(Request $request, $id)
    {
        $validatedData = app($this->updateRequestClass)->validated();
        $model = $this->modelClass::findOrFail($id);
        $model->update($validatedData);

        cache()->increment('brands_version', 1);

        return to_route('brands.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $model = $this->modelClass::findOrFail($id);
        $model->delete();

        cache()->increment('brands_version', 1);

        return to_route('brands.index')->with('success', 'Deleted successfully');
    }
}
