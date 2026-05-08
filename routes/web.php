<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;   // Adicione isso
use App\Http\Controllers\InventoryController; // Adicione isso
use App\Http\Controllers\SupplierController;  // Adicione isso
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin',      fn() => 'Área Admin')->middleware('role:admin')->name('admin');

    // ADICIONE ESTAS LINHAS ABAIXO:
    Route::resource('products', ProductController::class);
    // Rota para registrar entrada via modal/JS
    Route::post('products/{product}/add-inventory', [ProductController::class, 'addInventory'])->name('products.addInventory');
    // Rota para adicionar nova categoria via AJAX
    Route::post('products/store-category', [ProductController::class, 'storeCategory'])->name('products.store-category');
    Route::resource('inventory', InventoryController::class);
    Route::resource('suppliers', SupplierController::class);
});

Route::get('/', fn() => redirect()->route('login'));