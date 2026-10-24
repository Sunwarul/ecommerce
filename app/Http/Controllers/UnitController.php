<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitStoreRequest;
// use App\Exports\UnitExport;
use App\Http\Requests\UnitUpdateRequest;
use App\Models\Unit;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class UnitController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'units',
            modelClass: Unit::class,
            storeRequestClass: UnitStoreRequest::class,
            updateRequestClass: UnitUpdateRequest::class,
            componentPath: 'Units/Index',
            searchColumns: [],
            // exportClass: UnitExport::class,
            withRelations: [],
        ));
    }

    protected function addProps(): array
    {
        return [
            'baseUnits' => \App\Models\BaseUnit::select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
