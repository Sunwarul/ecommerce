<?php


use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\TagController;
use Modules\Product\Http\Controllers\Api\TaxController;
use Modules\Product\Http\Controllers\Api\CartController;
use Modules\Product\Http\Controllers\Api\UnitController;
use Modules\Product\Http\Controllers\Api\BrandController;
use Modules\Product\Http\Controllers\Api\ColorController;
use Modules\Product\Http\Controllers\Api\CouponController;
use Modules\Product\Http\Controllers\Api\ProductController;
use Modules\Product\Http\Controllers\Api\CategoryController;
use Modules\Product\Http\Controllers\Api\CustomerController;
use Modules\Product\Http\Controllers\Api\DiscountController;
use Modules\Product\Http\Controllers\Api\PaymentsController;
use Modules\Product\Http\Controllers\Api\ShoppingController;
use Modules\Product\Http\Controllers\Api\SupplierController;
use Modules\Product\Http\Controllers\Api\InventoryController;
use Modules\Product\Http\Controllers\Api\WarehouseController;
use Modules\Product\Http\Controllers\Api\SubCategoryController;
use Modules\Product\Http\Controllers\Api\ProductStockController;
use Modules\Product\Http\Controllers\Api\WarrantyGuaranteeController;


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
