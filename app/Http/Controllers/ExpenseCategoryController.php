<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
// use App\Exports\ExpenseCategoryExport;
use App\Utils\CrudConfig;
use App\Traits\HasCrud;
use App\Http\Requests\ExpenseCategoryStoreRequest;
use App\Http\Requests\ExpenseCategoryUpdateRequest;

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
