<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
// use App\Exports\UnitExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\UnitStoreRequest;
use App\Http\Requests\UnitUpdateRequest;

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
            addProps: $this->addProps(),
        ));
    }


    protected function addProps(): array
    {
        return [
            'baseUnits' => [
                ['id' => 1, 'name' => 'Unit 1'],
                ['id' => 2, 'name' => 'Unit 2'],
                ['id' => 3, 'name' => 'Unit 3'],
            ],
        ];
    }
}
