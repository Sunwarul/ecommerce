<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaseUnit;
// use App\Exports\BaseUnitExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\BaseUnitStoreRequest;
use App\Http\Requests\BaseUnitUpdateRequest;

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
