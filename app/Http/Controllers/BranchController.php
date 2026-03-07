<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchStoreRequest;
// use App\Exports\BranchExport;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class BranchController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'branches',
            modelClass: Branch::class,
            storeRequestClass: BranchStoreRequest::class,
            updateRequestClass: BranchUpdateRequest::class,
            componentPath: 'Branches/Index',
            searchColumns: ['name', 'address', 'code', 'phone'],
            // exportClass: BranchExport::class,
            withRelations: [],
        ));
    }
}
