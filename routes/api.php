<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\ShoppingController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\ProductStockController;
use App\Http\Controllers\Api\WarrantyGuaranteeController;


Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::apiResources([
        'categories' => CategoryController::class,
        'tags' => TagController::class,
        'coupons' => CouponController::class,
        'products' => ProductController::class,
        'taxes' => TaxController::class,
        'brands' => BrandController::class,
        'subCategories' => SubCategoryController::class,
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
        'colors' => ColorController::class,
    ]);
});
