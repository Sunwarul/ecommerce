<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PosOrderController;
use App\Http\Controllers\Admin\PosSessionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TodoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarrantyGuaranteeController;
use App\Http\Controllers\BaseUnitController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\Frontend\WelcomePageController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Sales\EmiController;
use App\Http\Controllers\Sales\InstallmentController;
use App\Http\Controllers\Sales\InstallmentPlanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Sms\SmsController;
use App\Http\Controllers\Sms\SmsSettingController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use App\Utils\CrudRouter;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('locale', [LocaleController::class, 'get'])->name('locale.get');
Route::post('locale', [LocaleController::class, 'set'])->name('locale.set');
Route::get('set-locale/{locale}', [LocaleController::class, 'setFromUrl'])->name('locale.setFromUrl')->where('locale', 'en|bn|ar|es|hi');

Route::get('/', WelcomePageController::class)->name('welcome');

// AUTH & VERIFIED

Route::middleware(['auth', 'verified', 'route.permission'])->prefix('admin')->group(function () {
    // =======================
    // Products - Custom routes first (before CrudRouter)
    // =======================
    // static paths first so "bulk-destroy" etc. are never captured as a {product} id
    Route::post('/products/bulk-restore', [ProductController::class, 'bulkRestore'])->name('products.bulk-restore');
    Route::delete('/products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-delete');
    Route::post('/products/bulk-force-delete', [ProductController::class, 'bulkForceDelete'])->name('products.bulk-force-delete');
    Route::get('/products/action/export-excel', [ProductController::class, 'export'])->name('products.export');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::get('/products/{product}/edit-data', [ProductController::class, 'editData'])->name('products.edit-data');
    Route::put('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.update-stock');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

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
    Route::post('/pos/orders/transfer', [PosOrderController::class, 'transfer'])
        ->name('pos.orders.transfer');
    Route::put('/pos/orders/{order}/date', [PosOrderController::class, 'updateOrderDate'])
        ->name('pos.orders.update-date');

    Route::post('/pos/stock/adjust', [\App\Http\Controllers\Admin\PosStockController::class, 'adjust'])
        ->name('pos.stock.adjust');

    // Route::post('/pos/orders/{order}/refund', [PosRefundController::class, 'refund'])->name('pos.orders.refund');
    // Route::get('/pos/report/daily', [PosReportController::class, 'daily'])->name('pos.report.daily');

    CrudRouter::setFor('product-attributes', App\Http\Controllers\ProductAttributeController::class);
    CrudRouter::setFor('product-attribute-values', App\Http\Controllers\ProductAttributeValueController::class);

    // =======================
    // Reports
    // =======================
    Route::get('/reports/sales', [SalesReportController::class, 'index'])
        ->middleware('can:Report Index')->name('reports.sales');

    // =======================
    // Installments (hire purchase)
    // =======================
    Route::post('/installments/preview', [InstallmentController::class, 'preview'])->name('installments.preview');
    Route::middleware('can:Installment Index')->group(function () {
        Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
        Route::get('/installments/{installment}', [InstallmentController::class, 'show'])->whereNumber('installment')->name('installments.show');
    });
    Route::post('/installments', [InstallmentController::class, 'store'])
        ->middleware('can:Installment Create')->name('installments.store');
    Route::put('/installments/{installment}', [InstallmentController::class, 'update'])
        ->middleware('can:Installment Create')->name('installments.update');
    Route::post('/installments/{installment}/collect', [InstallmentController::class, 'collect'])
        ->middleware('can:Installment Collect')->name('installments.collect');
    Route::post('/installments/schedules/{schedule}/waive-late-fee', [InstallmentController::class, 'waiveLateFee'])
        ->middleware('can:Installment Collect')->name('installments.waive-late-fee');
    Route::put('/installments/{installment}/status', [InstallmentController::class, 'updateStatus'])
        ->middleware('can:Installment Cancel')->name('installments.status');

    Route::middleware('can:Installment Plan Manage')->group(function () {
        Route::get('/installment-plans', [InstallmentPlanController::class, 'index'])->name('installment-plans.index');
        Route::post('/installment-plans', [InstallmentPlanController::class, 'store'])->name('installment-plans.store');
        Route::put('/installment-plans/{plan}', [InstallmentPlanController::class, 'update'])->name('installment-plans.update');
        Route::delete('/installment-plans/{plan}', [InstallmentPlanController::class, 'destroy'])->name('installment-plans.destroy');
    });

    // =======================
    // Bank EMI
    // =======================
    Route::post('/emi/quote', [EmiController::class, 'quote'])->name('emi.quote');
    Route::get('/emi', [EmiController::class, 'index'])->middleware('can:EMI Index')->name('emi.index');
    Route::middleware('can:EMI Manage')->group(function () {
        Route::post('/emi/settle', [EmiController::class, 'settle'])->name('emi.settle');
        Route::post('/emi/{transaction}/unsettle', [EmiController::class, 'unsettle'])->name('emi.unsettle');
        Route::post('/emi/banks', [EmiController::class, 'storeBank'])->name('emi.banks.store');
        Route::put('/emi/banks/{bank}', [EmiController::class, 'updateBank'])->name('emi.banks.update');
        Route::delete('/emi/banks/{bank}', [EmiController::class, 'destroyBank'])->name('emi.banks.destroy');
    });

    // =======================
    // SMS
    // =======================
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::middleware('can:SMS Index')->group(function () {
            Route::get('/', [SmsController::class, 'dashboard'])->name('dashboard');
            Route::get('/logs', [SmsController::class, 'logs'])->name('logs');
        });
        Route::middleware('can:SMS Send')->group(function () {
            Route::get('/send', [SmsController::class, 'compose'])->name('compose');
            Route::post('/send', [SmsController::class, 'send'])->name('send')->middleware('throttle:sms');
            Route::get('/customers', [SmsController::class, 'customers'])->name('customers');
            Route::post('/resend', [SmsController::class, 'resend'])->name('resend')->middleware('throttle:sms');
            Route::post('/retry-stuck', [SmsController::class, 'retryStuck'])->name('retry-stuck');
        });
        Route::middleware('can:SMS Settings')->group(function () {
            Route::get('/settings', [SmsSettingController::class, 'index'])->name('settings');
            Route::put('/settings', [SmsSettingController::class, 'update'])->name('settings.update');
            Route::post('/templates', [SmsSettingController::class, 'storeTemplate'])->name('templates.store');
            Route::put('/templates/{template}', [SmsSettingController::class, 'updateTemplate'])->name('templates.update');
            Route::delete('/templates/{template}', [SmsSettingController::class, 'destroyTemplate'])->name('templates.destroy');
            Route::post('/gateways', [SmsSettingController::class, 'storeGateway'])->name('gateways.store');
            Route::put('/gateways/{gateway}', [SmsSettingController::class, 'updateGateway'])->name('gateways.update');
            Route::delete('/gateways/{gateway}', [SmsSettingController::class, 'destroyGateway'])->name('gateways.destroy');
            Route::post('/gateways/{gateway}/balance', [SmsSettingController::class, 'balance'])->name('gateways.balance');
            Route::post('/gateways/{gateway}/test', [SmsSettingController::class, 'test'])->name('gateways.test')->middleware('throttle:sms');
        });
    });
});
