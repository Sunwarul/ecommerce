<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
// use App\Exports\SupplierExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;

/*
* @author Sunwarul Islam
*/
class SupplierController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'suppliers',
            modelClass: Supplier::class,
            storeRequestClass: SupplierStoreRequest::class,
            updateRequestClass: SupplierUpdateRequest::class,
            componentPath: 'Suppliers/Index',
            searchColumns: [],
            // exportClass: SupplierExport::class,
            withRelations: [],
        ));
    }

    
}
