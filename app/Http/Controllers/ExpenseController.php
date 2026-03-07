<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
// use App\Exports\ExpenseExport;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Warehouse;
use App\Traits\HasCrud;
use App\Utils\CrudConfig;

class ExpenseController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->init(new CrudConfig(
            resource: 'expenses',
            modelClass: Expense::class,
            storeRequestClass: ExpenseStoreRequest::class,
            updateRequestClass: ExpenseUpdateRequest::class,
            componentPath: 'Expenses/Index',
            searchColumns: [],
            // exportClass: ExpenseExport::class,
            withRelations: [],
        ));
    }

    protected function addProps(): array
    {
        $users = User::select('id', 'name')->get();
        $warehouses = Warehouse::select('id', 'name')->get();
        $expenseCategories = ExpenseCategory::select('id', 'name')->get();

        return [
            'users' => $users,
            'warehouses' => $warehouses,
            'expenseCategories' => $expenseCategories,
        ];
    }
}
