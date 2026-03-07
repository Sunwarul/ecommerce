<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseUnitStoreRequest;
// use App\Exports\BaseUnitExport;
use App\Http\Requests\BaseUnitUpdateRequest;
use App\Models\BaseUnit;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class BaseUnitController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'base_units',
            modelClass: BaseUnit::class,
            storeRequestClass: BaseUnitStoreRequest::class,
            updateRequestClass: BaseUnitUpdateRequest::class,
            componentPath: 'BaseUnits/Index',
            searchColumns: [],
            // exportClass: BaseUnitExport::class,
            withRelations: [],
        ));
    }
}
