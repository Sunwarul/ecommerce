<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomePageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubCategoryController;

# GUEST
Route::get('/', WelcomePageController::class)->name('welcome');

# AUTH (VERIFIED)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
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
