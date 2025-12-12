<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
// use App\Exports\TaxExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\TaxStoreRequest;
use App\Http\Requests\TaxUpdateRequest;

class TaxController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'taxes',
            modelClass: Tax::class,
            storeRequestClass: TaxStoreRequest::class,
            updateRequestClass: TaxUpdateRequest::class,
            componentPath: 'Taxes/Index',
            searchColumns: [],
            // exportClass: TaxExport::class,
            withRelations: [],
        ));
    }

    
}
