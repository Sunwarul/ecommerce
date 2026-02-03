<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductAttributeValue;
// use App\Exports\ProductAttributeValueExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\ProductAttributeValueStoreRequest;
use App\Http\Requests\ProductAttributeValueUpdateRequest;
use App\Models\ProductAttribute;

class ProductAttributeValueController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'product_attribute_values',
            modelClass: ProductAttributeValue::class,
            storeRequestClass: ProductAttributeValueStoreRequest::class,
            updateRequestClass: ProductAttributeValueUpdateRequest::class,
            componentPath: 'ProductAttributeValues/Index',
            searchColumns: [],
            // exportClass: ProductAttributeValueExport::class,
            withRelations: ['attribute'],
            addProps: $this->addProps(),
        ));
    }

    protected function addProps(): array
    {
        $attributes = ProductAttribute::select('id', 'display_name', 'type')->get();
        return [
            'attributes' => $attributes,
        ];
    }

}
