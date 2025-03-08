<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\TagController;
use Modules\Product\Http\Controllers\BrandController;
use Modules\Product\Http\Controllers\ColorController;
use Modules\Product\Http\Controllers\CategoryController;
use Modules\Product\Http\Controllers\SubCategoryController;

# AUTH & VERIFIED

Route::middleware(['auth', 'verified'])->group(function () {
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/export/excel', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('/categories/destroy/bulk', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');

    // Sub-Categories
    Route::resource('sub-categories', SubCategoryController::class);
    Route::get('/sub-categories/export/excel', [SubCategoryController::class, 'export'])->name('sub_categories.export');

    // Brands
    Route::resource('brands', BrandController::class);
    Route::post('/brands/destroy/bulk', [BrandController::class, 'bulkDestroy'])->name('brands.bulk-destroy');
    Route::get('/brands/export/excel', [BrandController::class, 'export'])->name('brands.export');

    // Tags
    Route::resource('tags', TagController::class);
    Route::post('/tags/destroy/bulk', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');
    Route::get('/tags/export/excel', [TagController::class, 'export'])->name('tags.export');

    // Colors
    Route::resource('colors', ColorController::class);
    Route::post('/colors/destroy/bulk', [ColorController::class, 'bulkDestroy'])->name('colors.bulk-destroy');
});
