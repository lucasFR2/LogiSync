<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CarrierController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard',  [DashboardController::class, 'index'])->name('dashboard');

    // Employee management (restricted)
    Route::middleware('permission:usuarios.gerenciar')->group(function () {
        Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);

        Route::resource('users', App\Http\Controllers\UserController::class)->names('employees');
    });
    
    // ============ PRODUTOS E ESTOQUE ============
    Route::middleware('permission:produtos.visualizar')->group(function () {
        Route::get('/products/labels/select', [ProductController::class, 'labelSelection'])->name('products.labels.select');
        Route::get('/products/labels', [ProductController::class, 'printLabels'])->name('products.labels');
        Route::resource('products', ProductController::class);
    });

    Route::middleware('permission:estoque.visualizar')->group(function () {
        Route::get('/inventory', [ProductController::class, 'inventories'])->name('inventory.index');
    });

    Route::middleware('permission:estoque.entradas')->group(function () {
        Route::get('/inventory/create', [ProductController::class, 'createInventory'])->name('inventory.create');
        Route::post('/inventory', [ProductController::class, 'storeInventory'])->name('inventory.store');
        Route::get('/inventory/bulk-create/{manifestation}', [App\Http\Controllers\ProductController::class, 'bulkCreate'])->name('inventory.bulkCreate');
        Route::post('/inventory/bulk-store/{manifestation}', [App\Http\Controllers\ProductController::class, 'bulkStore'])->name('inventory.bulkStore');
        Route::post('/products/{product}/add-inventory', [ProductController::class, 'addInventory'])->name('products.add-inventory');
    });

    Route::middleware('permission:categorias.gerenciar')->group(function () {
        Route::post('products/store-category', [ProductController::class, 'storeCategory'])->name('products.store-category');
    });

    Route::middleware('permission:estoque.visualizar')->group(function () {
        Route::get('locations/search', [ProductController::class, 'searchLocations'])->name('locations.search');
        Route::get('locations', [App\Http\Controllers\WarehouseLocationController::class, 'index'])->name('locations.index');
    });

    Route::middleware('permission:estoque.entradas')->group(function () {
        Route::post('locations', [App\Http\Controllers\WarehouseLocationController::class, 'store'])->name('locations.store');
        Route::put('locations/{location}', [App\Http\Controllers\WarehouseLocationController::class, 'update'])->name('locations.update');
        Route::delete('locations/{location}', [App\Http\Controllers\WarehouseLocationController::class, 'destroy'])->name('locations.destroy');
        Route::post('locations/generate', [App\Http\Controllers\WarehouseLocationController::class, 'generate'])->name('locations.generate');
    });

    // ============ CATEGORIAS ============
    Route::middleware('permission:categorias.gerenciar')->group(function () {
        Route::post('categories/quick-store', [CategoryController::class, 'storeQuick'])->name('categories.quick-store');
        Route::resource('categories', CategoryController::class);
    });
    
    // ============ FORNECEDORES ============
    Route::middleware('permission:fornecedores.gerenciar')->group(function () {
        Route::resource('suppliers', SupplierController::class);
        Route::get('/suppliers/list', [SupplierController::class, 'list'])->name('suppliers.list');
    });

    // ============ CLIENTES ============
    Route::middleware('permission:clientes.gerenciar')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // ============ TRANSPORTADORAS ============
    Route::middleware('permission:transportadoras.gerenciar')->group(function () {
        Route::resource('carriers', CarrierController::class);
        Route::get('/carriers/list', [CarrierController::class, 'list'])->name('carriers.list');
    });

    // ============ NOTAS FISCAIS ============
    Route::middleware('permission:notas_fiscais.emitir')->group(function () {
        Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    });

    Route::middleware('permission:notas_fiscais.visualizar')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::get('invoices/{invoice}/romaneio', [InvoiceController::class, 'romaneio'])->name('invoices.romaneio');
        Route::post('invoices/{invoice}/confer', [InvoiceController::class, 'confer'])->name('invoices.confer');
        Route::get('invoices/{invoice}/confer-workflow', [InvoiceController::class, 'conferWorkflow'])->name('invoices.confer-workflow');
        Route::post('invoices/{invoice}/confer-save', [InvoiceController::class, 'conferSave'])->name('invoices.confer-save');
    });

    Route::middleware('permission:notas_fiscais.editar')->group(function () {
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('invoices/{invoice}/conclude', [InvoiceController::class, 'conclude'])->name('invoices.conclude');
    });

    // ============ MANIFESTAÇÕES ============
    Route::prefix('manifestations')->name('manifestations.')->middleware('permission:manifestacoes.gerenciar')->group(function () {
        Route::get('/', [App\Http\Controllers\ManifestationController::class, 'index'])->name('index');
        Route::get('/generate-xml', [App\Http\Controllers\ManifestationController::class, 'generateXml'])->name('generateXml');
        Route::post('/upload-xml', [App\Http\Controllers\ManifestationController::class, 'uploadXml'])->name('uploadXml');
        Route::get('/{manifestation}', [App\Http\Controllers\ManifestationController::class, 'show'])->name('show');
        Route::post('/{manifestation}/manifest', [App\Http\Controllers\ManifestationController::class, 'manifest'])->name('manifest');
        Route::get('/{manifestation}/danfe', [App\Http\Controllers\ManifestationController::class, 'danfe'])->name('danfe');
        Route::get('/{manifestation}/confer-workflow', [App\Http\Controllers\ManifestationController::class, 'conferWorkflow'])->name('confer-workflow');
        Route::post('/{manifestation}/confer-save', [App\Http\Controllers\ManifestationController::class, 'conferSave'])->name('confer-save');
    });

    // ============ LOGS ============
    Route::middleware('permission:logs.visualizar')->group(function () {
        Route::get('/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
    });

    // ============ RELATÓRIOS ============
    Route::middleware('permission:relatorios.visualizar')->group(function () {
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    });

    // ============ ADMINISTRAÇÃO ============
    Route::middleware('permission:cargos.gerenciar')->group(function () {
        Route::resource('roles', App\Http\Controllers\RoleController::class)->except(['show']);
        Route::get('/admin', fn() => 'Área Admin')->name('admin');
    });

});

Route::get('/', fn() => redirect()->route('login'));