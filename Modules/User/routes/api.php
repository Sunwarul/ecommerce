<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\Auth\LoginController;
use Modules\User\Http\Controllers\Api\Auth\RegistrationController;

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('register', [RegistrationController::class, 'register'])->name('register');
