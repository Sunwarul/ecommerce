<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\TagController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Categories
Route::resource('categories', CategoryController::class);
Route::post('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
// Brands
Route::resource('brands', BrandController::class);
Route::post('/brands/bulk-destroy', [BrandController::class, 'bulkDestroy'])->name('brands.bulk-destroy');

// Tags
Route::resource('tags', TagController::class);
Route::post('/tags/bulk-destroy', [TagController::class, 'bulkDestroy'])->name('tags.bulk-destroy');


require __DIR__ . '/auth.php';
