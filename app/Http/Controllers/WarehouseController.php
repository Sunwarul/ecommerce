<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
// use App\Exports\WarehouseExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;

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
            // exportClass: WarehouseExport::class,
            withRelations: [],
        ));
    }

    
}
