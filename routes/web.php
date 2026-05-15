<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');

    // Employee management (restricted)
    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);

        Route::resource('users', App\Http\Controllers\UserController::class)->names('employees');
    });
    
    // Product routes
    // Product & Inventory routes
    Route::resource('products', ProductController::class);
    Route::get('/inventory', [ProductController::class, 'inventories'])->name('inventory.index');
    Route::get('/inventory/create', [ProductController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory', [ProductController::class, 'storeInventory'])->name('inventory.store');
    
    // Bulk Inventory from NF-e
    Route::get('/inventory/bulk-create/{manifestation}', [App\Http\Controllers\ProductController::class, 'bulkCreate'])->name('inventory.bulkCreate');
    Route::post('/inventory/bulk-store/{manifestation}', [App\Http\Controllers\ProductController::class, 'bulkStore'])->name('inventory.bulkStore');
    Route::post('/products/{product}/add-inventory', [ProductController::class, 'addInventory'])->name('products.add-inventory');
    Route::post('products/store-category', [ProductController::class, 'storeCategory'])->name('products.store-category');
    Route::get('locations/search', [ProductController::class, 'searchLocations'])->name('locations.search');

    // Category routes (quick-store MUST come before the resource to avoid {category} collision)
    Route::post('categories/quick-store', [CategoryController::class, 'storeQuick'])->name('categories.quick-store');
    Route::resource('categories', CategoryController::class);
    
    
    // Supplier routes
    Route::resource('suppliers', SupplierController::class);
    Route::get('/suppliers/list', [SupplierController::class, 'list'])->name('suppliers.list');

    // Customer routes
    Route::resource('customers', CustomerController::class);

    // Invoices / Faturamento
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    // Manifestação do Destinatário
    Route::prefix('manifestations')->name('manifestations.')->group(function () {
        Route::get('/', [App\Http\Controllers\ManifestationController::class, 'index'])->name('index');
        Route::get('/generate-xml', [App\Http\Controllers\ManifestationController::class, 'generateXml'])->name('generateXml');
        Route::post('/upload-xml', [App\Http\Controllers\ManifestationController::class, 'uploadXml'])->name('uploadXml');
        Route::get('/{manifestation}', [App\Http\Controllers\ManifestationController::class, 'show'])->name('show');
        Route::post('/{manifestation}/manifest', [App\Http\Controllers\ManifestationController::class, 'manifest'])->name('manifest');
        Route::get('/{manifestation}/danfe', [App\Http\Controllers\ManifestationController::class, 'danfe'])->name('danfe');
    });

    // Admin-only / Special Logs
    Route::middleware('permission:logs.view')->group(function () {
        Route::get('/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
    });

    Route::middleware('permission:roles.manage')->group(function () {
        // Roles management
        Route::resource('roles', App\Http\Controllers\RoleController::class)->except(['show', 'create', 'edit']);

        Route::get('/admin', fn() => 'Área Admin')->name('admin');
    });
});

Route::get('/', fn() => redirect()->route('login'));