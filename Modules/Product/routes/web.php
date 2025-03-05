<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\TagController;
use Modules\Product\Http\Controllers\BrandController;
use Modules\Product\Http\Controllers\ColorController;
use Modules\Product\Http\Controllers\CategoryController;
use Modules\Product\Http\Controllers\SubCategoryController;

# AUTH (VERIFIED)
Route::middleware(['auth', 'verified'])->group(function () {
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
    // Sub-Categories
    Route::resource('sub-categories', SubCategoryController::class);
    // Brands
    Route::resource('brands', BrandController::class);
    Route::post('/brands/bulk-destroy', [BrandController::class, 'bulkDestroy'])->name('brands.bulk-destroy');
    // Tags
    Route::resource('tags', TagController::class);
    Route::post('/tags/bulk-destroy', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');
    // Colors
    Route::resource('colors', ColorController::class);
    Route::post('/colors/bulk-destroy', [ColorController::class, 'bulkDestroy'])->name('colors.bulk-destroy');
});
