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
use App\Http\Controllers\Admin\WarrantyGuaranteeController;
use App\Http\Controllers\StockMovementController;

require_once __DIR__ . '/auth.php';

Route::get('/', WelcomePageController::class)->name('welcome');

// AUTH & VERIFIED

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    CrudRouter::setFor('products', ProductController::class);
    Route::get('/admin/products/{product}/edit-data', [ProductController::class, 'editData'])
        ->name('products.edit-data');
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])
        ->name('products.restore');
    Route::post('/products/bulk-restore', [ProductController::class, 'bulkRestore'])
        ->name('products.bulk-restore');
    Route::delete('/products/bulk-destroy', [ProductController::class, 'bulkDestroy'])
        ->name('products.bulk-delete');
    Route::post('/products/bulk-force-delete', [ProductController::class, 'bulkForceDelete'])
        ->name('products.bulk-force-delete');


    CrudRouter::setFor('taxes', App\Http\Controllers\TaxController::class);
    CrudRouter::setFor('categories', CategoryController::class);
    CrudRouter::setFor('tags', TagController::class);
    CrudRouter::setFor('brands', BrandController::class);
    CrudRouter::setFor('sub-categories', SubCategoryController::class);
    CrudRouter::setFor('payment-methods', PaymentMethodController::class);
    CrudRouter::setFor('todos', TodoController::class);
    CrudRouter::setFor('tasks', TaskController::class);
    CrudRouter::setFor('users', UserController::class);
    Route::post('/switch-branch', [UserController::class, 'switch'])
        ->name('branch.switch');
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
    CrudRouter::setFor('warranty-guarantees', WarrantyGuaranteeController::class);

    Route::get('/settings', [SettingController::class, 'general'])->name('settings.general');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');


    // stock 
    Route::get('admin/products/{product}/stock-move', [StockMovementController::class, 'create'])
        ->name('admin.stock.move.form');

    Route::post('admin/stock/move', [StockMovementController::class, 'store'])
        ->name('admin.stock.move');



    // =======================
    // POS
    // =======================
    Route::get('/pos', [PosOrderController::class, 'index'])
        ->name('pos.index');
    Route::get('/pos/customers/search', [PosOrderController::class, 'customerSearch'])
        ->name('pos.customers.search');

    // -----------------------
    // POS Session
    // -----------------------
    Route::get('/pos/session/current', [PosSessionController::class, 'current'])
        ->name('pos.session.current');

    Route::post('/pos/session/open', [PosSessionController::class, 'open'])
        ->name('pos.session.open');

    Route::post('/pos/session/close', [PosSessionController::class, 'close'])
        ->name('pos.session.close');

    // -----------------------
    // POS Orders
    // -----------------------
    Route::post('/pos/orders', [PosOrderController::class, 'store'])
        ->name('pos.orders.store');

    Route::get('/pos/orders/{order}/edit', [PosOrderController::class, 'edit'])
        ->name('pos.orders.edit');

    Route::put('/pos/orders/{order}', [PosOrderController::class, 'update'])
        ->name('pos.orders.update');

    // invoice MUST be before index
    Route::get('/pos/orders/{order}/invoice', [PosOrderController::class, 'invoice'])
        ->name('pos.orders.invoice');

    // orders list (sales history)
    Route::get('/pos/orders', [PosOrderController::class, 'orders'])
        ->name('pos.orders.index');

    Route::post('/pos/orders/{order}/void', [PosOrderController::class, 'void'])
        ->name('pos.orders.void');
    Route::post('/pos/orders/{order}/complete', [PosOrderController::class, 'completeDraft'])
        ->name('pos.orders.complete');
    Route::post('/pos/orders/{order}/payments', [PosOrderController::class, 'addPayment'])
        ->name('pos.orders.payments.store');

    Route::delete('/pos/orders/{id}', [PosOrderController::class, 'destroy'])
        ->name('pos.orders.destroy');
    Route::post('/pos/orders/{id}/restore', [PosOrderController::class, 'restore'])
        ->name('pos.orders.restore');
    Route::delete('/pos/orders/{id}/force', [PosOrderController::class, 'forceDelete'])
        ->name('pos.orders.force-delete');
    Route::post('/pos/orders/bulk-action', [PosOrderController::class, 'bulkAction'])
        ->name('pos.orders.bulk-action');

    Route::post('/pos/stock/adjust', [\App\Http\Controllers\Admin\PosStockController::class, 'adjust'])
        ->name('pos.stock.adjust');




    // Route::post('/pos/orders/{order}/refund', [PosRefundController::class, 'refund'])->name('pos.orders.refund');
    // Route::get('/pos/report/daily', [PosReportController::class, 'daily'])->name('pos.report.daily');








    CrudRouter::setFor('product-attributes', App\Http\Controllers\ProductAttributeController::class);
    CrudRouter::setFor('product-attribute-values', App\Http\Controllers\ProductAttributeValueController::class);
});
