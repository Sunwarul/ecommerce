<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductAttributeStoreRequest;
// use App\Exports\ProductAttributeExport;
use App\Http\Requests\ProductAttributeUpdateRequest;
use App\Models\ProductAttribute;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

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
