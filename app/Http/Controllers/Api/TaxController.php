<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaxStoreRequest;
use App\Http\Requests\Admin\TaxUpdateRequest;
use App\Models\Tax;
use App\Traits\HasApiCrud;
use App\Utils\CrudConfig;

class TaxController extends Controller
{
    use HasApiCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'task',
            modelClass: Tax::class,
            storeRequestClass: TaxStoreRequest::class,
            updateRequestClass: TaxUpdateRequest::class,
            searchColumns: ['title'],
        ));
    }
}
