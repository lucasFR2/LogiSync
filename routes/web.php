<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
<<<<<<< Updated upstream
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
=======
use App\Http\Controllers\ProductController;   // Adicione isso
use App\Http\Controllers\InventoryController; // Adicione isso
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
>>>>>>> Stashed changes
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
    
    // Product routes
    Route::resource('products', ProductController::class);
<<<<<<< Updated upstream
    Route::get('/inventory', [ProductController::class, 'inventories'])->name('inventory.index');
    Route::post('/products/{product}/add-inventory', [ProductController::class, 'addInventory'])->name('products.add-inventory');
    
    // Supplier routes
    Route::resource('suppliers', SupplierController::class);
    Route::get('/suppliers/list', [SupplierController::class, 'list'])->name('suppliers.list');

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('/invoices/{invoice}/pdf',      [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    Route::get('/admin', fn() => 'Área Admin')->middleware('role:admin')->name('admin');
=======
    // Rota para registrar entrada via modal/JS
    Route::post('products/{product}/add-inventory', [ProductController::class, 'addInventory'])->name('products.addInventory');
    // Rota para adicionar nova categoria via AJAX
    Route::post('products/store-category', [ProductController::class, 'storeCategory'])->name('products.store-category');
    Route::get('locations/search', [ProductController::class, 'searchLocations'])->name('locations.search');
    Route::resource('inventory', InventoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
>>>>>>> Stashed changes
});

Route::get('/', fn() => redirect()->route('login'));
