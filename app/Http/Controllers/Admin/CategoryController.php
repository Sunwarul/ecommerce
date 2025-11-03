<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CategoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class CategoryController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'categories',
            modelClass: Category::class,
            storeRequestClass: CategoryStoreRequest::class,
            updateRequestClass: CategoryUpdateRequest::class,
            searchColumns: ['name'],
            exportClass: CategoryExport::class,
            componentPath: 'Admin/Categories/Index',
            withRelations: ['parent'],
            addProps: $this->addProps(),
        ));
    }
    protected function addProps(): array
    {
        $parentCategories = Category::select('id', 'name')->whereNull('parent_id')->get();
        return [
            'parentCategories' => $parentCategories,
        ];
    }
}
