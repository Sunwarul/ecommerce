<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductAttribute;
// use App\Exports\ProductAttributeExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\ProductAttributeStoreRequest;
use App\Http\Requests\ProductAttributeUpdateRequest;

class ProductAttributeController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'product_attributes',
            modelClass: ProductAttribute::class,
            storeRequestClass: ProductAttributeStoreRequest::class,
            updateRequestClass: ProductAttributeUpdateRequest::class,
            componentPath: 'ProductAttributes/Index',
            searchColumns: [],
            // exportClass: ProductAttributeExport::class,
            withRelations: [],
        ));
    }

    
}
