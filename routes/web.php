<?php

use App\Utils\CrudRouter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BaseUnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PosOrderController;
use App\Http\Controllers\Admin\PosSessionController;
use App\Http\Controllers\Frontend\WelcomePageController;
use App\Http\Controllers\SettingController;

require_once __DIR__ . '/auth.php';

Route::get('/', WelcomePageController::class)->name('welcome');

// AUTH & VERIFIED

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    CrudRouter::setFor('products', ProductController::class);
    Route::get('/admin/products/{product}/edit-data', [ProductController::class, 'editData'])
        ->name('products.edit-data');

    CrudRouter::setFor('categories', CategoryController::class);
    CrudRouter::setFor('tags', TagController::class);
    CrudRouter::setFor('brands', BrandController::class);
    CrudRouter::setFor('sub-categories', SubCategoryController::class);
    CrudRouter::setFor('payment-methods', PaymentMethodController::class);
    CrudRouter::setFor('todos', TodoController::class);
    CrudRouter::setFor('tasks', TaskController::class);
    CrudRouter::setFor('users', UserController::class);
    CrudRouter::setFor('branches', BranchController::class);
    CrudRouter::setFor('warehouses', WarehouseController::class);
    CrudRouter::setFor('suppliers', SupplierController::class);
    CrudRouter::setFor('base-units', BaseUnitController::class);
    CrudRouter::setFor('units', UnitController::class);
    CrudRouter::setFor('expense-categories', ExpenseCategoryController::class);
    CrudRouter::setFor('expenses', App\Http\Controllers\ExpenseController::class);

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    CrudRouter::setFor('currencies', App\Http\Controllers\CurrencyController::class);
    CrudRouter::setFor('customers', App\Http\Controllers\CustomerController::class);

    Route::get('/settings', [SettingController::class, 'general'])->name('settings.general');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // POS
    Route::get('/pos', [PosOrderController::class, 'index'])->name('pos.index');
    Route::post('/pos/orders', [PosOrderController::class, 'store'])->name('pos.orders.store');

    Route::get('/pos/session/current', [PosSessionController::class, 'current'])->name('pos.session.current');
    Route::post('/pos/session/open', [PosSessionController::class, 'open'])->name('pos.session.open');
    Route::post('/pos/session/close', [PosSessionController::class, 'close'])->name('pos.session.close');


    // POS
    Route::get('/pos/sales', [PosOrderController::class, 'list'])->name('pos.sales.list');
    Route::get('/pos/orders/{order}/invoice', [PosOrderController::class, 'invoice'])->name('pos.orders.invoice');
    // POS orders list (sales history)
    Route::get('/pos/orders', [PosOrderController::class, 'orders'])->name('pos.orders.index');

    // Route::post('/pos/orders/{order}/refund', [PosRefundController::class, 'refund'])->name('pos.orders.refund');
    // Route::get('/pos/report/daily', [PosReportController::class, 'daily'])->name('pos.report.daily');











});
