<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SubCategoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategoryStoreRequest;
use App\Http\Requests\Admin\SubCategoryUpdateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class SubCategoryController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'sub-categories',
            modelClass: SubCategory::class,
            storeRequestClass: SubCategoryStoreRequest::class,
            updateRequestClass: SubCategoryUpdateRequest::class,
            searchColumns: ['name'],
            exportClass: SubCategoryExport::class,
            componentPath: 'Admin/SubCategories/Index',
            withRelations: ['category'],
        ));
    }

    protected function addProps(): array
    {
        return [
            'categories' => Category::select('id', 'name')->get(),
        ];
    }
}
