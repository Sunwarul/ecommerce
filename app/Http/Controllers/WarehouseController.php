<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use Illuminate\Support\Facades\Storage;

class WarehouseController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'warehouses',
            modelClass: Warehouse::class,
            storeRequestClass: WarehouseStoreRequest::class,
            updateRequestClass: WarehouseUpdateRequest::class,
            componentPath: 'Warehouses/Index',
            searchColumns: [],
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
        
        cache()->increment('warehouses_version', 1);
        
        return to_route('warehouses.index')->with('success', 'Created successfully');
    }

    public function update(Request $request, $id)
    {
        $validatedData = app($this->updateRequestClass)->validated();
        $model = $this->modelClass::findOrFail($id);
        $model->update($validatedData);
        
        cache()->increment('warehouses_version', 1);
        
        return to_route('warehouses.index')->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $model = $this->modelClass::findOrFail($id);
        $model->delete();
        
        cache()->increment('warehouses_version', 1);
        
        return to_route('warehouses.index')->with('success', 'Deleted successfully');
    }
}
