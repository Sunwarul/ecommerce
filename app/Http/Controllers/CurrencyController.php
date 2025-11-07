<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
// use App\Exports\CurrencyExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\CurrencyStoreRequest;
use App\Http\Requests\CurrencyUpdateRequest;

class CurrencyController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'currencies',
            modelClass: Currency::class,
            storeRequestClass: CurrencyStoreRequest::class,
            updateRequestClass: CurrencyUpdateRequest::class,
            componentPath: 'Currencies/Index',
            searchColumns: [],
            // exportClass: CurrencyExport::class,
            withRelations: [],
        ));
    }

    
}
