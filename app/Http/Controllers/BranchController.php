<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
// use App\Exports\BranchExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\BranchStoreRequest;
use App\Http\Requests\BranchUpdateRequest;

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
