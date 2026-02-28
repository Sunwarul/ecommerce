<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductStockController;
use App\Http\Controllers\Api\ShoppingController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\WarrantyGuaranteeController;
use App\Utils\CrudRouter;
use Illuminate\Support\Facades\Route;

// Authentication using Sanctum JWT strategy
// Route::post('login', [LoginController::class, 'login'])->name('login');
// Route::post('register', [RegistrationController::class, 'register'])->name('register');

Route::middleware(['auth:sanctum', 'verified', 'api'])->name('api.')->group(function () {
    CrudRouter::setApiFor('categories', CategoryController::class);

    Route::apiResources([
        'tags' => TagController::class,
        'tasks' => TaskController::class,
        'todos' => TodoController::class,
        'coupons' => CouponController::class,
        'products' => ProductController::class,
        'taxes' => TaxController::class,
        'brands' => BrandController::class,
        'sub-categories' => SubCategoryController::class,
        'customers' => CustomerController::class,
        'suppliers' => SupplierController::class,
        'productStocks' => ProductStockController::class,
        'warehouses' => WarehouseController::class,
        'units' => UnitController::class,
        'inventories' => InventoryController::class,
        'warranties' => WarrantyGuaranteeController::class,
        'shoppings' => ShoppingController::class,
        'carts' => CartController::class,
        'discounts' => DiscountController::class,
        'payments' => PaymentsController::class,
    ]);
});
