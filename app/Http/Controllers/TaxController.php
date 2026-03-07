<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxStoreRequest;
// use App\Exports\TaxExport;
use App\Http\Requests\TaxUpdateRequest;
use App\Models\Tax;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

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
