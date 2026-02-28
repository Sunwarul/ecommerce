<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WarrantyGuaranteeStoreRequest;
use App\Http\Requests\Admin\WarrantyGuaranteeUpdateRequest;
use App\Models\Category;
use App\Models\WarrantyGuarantee;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class WarrantyGuaranteeController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'warranty_guarantees',
            modelClass: WarrantyGuarantee::class,
            storeRequestClass: WarrantyGuaranteeStoreRequest::class,
            updateRequestClass: WarrantyGuaranteeUpdateRequest::class,
            componentPath: 'Admin/WarrantyGuarantees/Index',
            searchColumns: ['name', 'description'],
            withRelations: ['category'],
            addProps: $this->addProps(),
        ));
    }

    protected function addProps(): array
    {
        return [
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
