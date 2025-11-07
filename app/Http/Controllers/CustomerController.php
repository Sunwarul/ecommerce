<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
// use App\Exports\CustomerExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;

class CustomerController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'customers',
            modelClass: Customer::class,
            storeRequestClass: CustomerStoreRequest::class,
            updateRequestClass: CustomerUpdateRequest::class,
            componentPath: 'Customers/Index',
            searchColumns: [],
            // exportClass: CustomerExport::class,
            withRelations: [],
        ));
    }

    
}
