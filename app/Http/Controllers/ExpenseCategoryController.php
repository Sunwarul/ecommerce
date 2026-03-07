<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryStoreRequest;
// use App\Exports\ExpenseCategoryExport;
use App\Http\Requests\ExpenseCategoryUpdateRequest;
use App\Models\ExpenseCategory;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class ExpenseCategoryController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'expense_categories',
            modelClass: ExpenseCategory::class,
            storeRequestClass: ExpenseCategoryStoreRequest::class,
            updateRequestClass: ExpenseCategoryUpdateRequest::class,
            componentPath: 'ExpenseCategories/Index',
            searchColumns: [],
            // exportClass: ExpenseCategoryExport::class,
            withRelations: [],
        ));
    }
}
