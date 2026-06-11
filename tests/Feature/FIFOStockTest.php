<?php

use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use App\Services\FIFOStockService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('fifo allocation consumes oldest stock first and creates correct exit logs', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a product
    $product = Product::create([
        'name'          => 'Produto Teste FIFO',
        'sku'           => 'SKU-TEST-FIFO',
        'barcode'       => '1234567890123',
        'unit_price'    => 10.00,
        'quantity'      => 0, // starts empty
        'reorder_level' => 5,
        'status'        => 'ativo',
    ]);

    // Create 3 entries with different dates and lot numbers
    $entry1 = Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 10,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'lot_number'         => 'LOT-001',
        'entry_date'         => now()->subDays(10),
        'user_id'            => $user->id,
    ]);

    $entry2 = Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 15,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'lot_number'         => 'LOT-002',
        'entry_date'         => now()->subDays(5),
        'user_id'            => $user->id,
    ]);

    $entry3 = Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 8,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'lot_number'         => 'LOT-003',
        'entry_date'         => now()->subDays(1),
        'user_id'            => $user->id,
    ]);

    // Update global quantity to reflect entries sum (33)
    $product->update(['quantity' => 33]);

    // Allocate 12 units (should consume 10 from entry1, and 2 from entry2)
    FIFOStockService::allocateFIFOStock($product, 12, 'REF-TEST-123', $user->id);

    // Refresh models
    $product->refresh();
    $entry1->refresh();
    $entry2->refresh();
    $entry3->refresh();

    // Global quantity should be 33 - 12 = 21
    expect($product->quantity)->toBe(21);

    // Entry 1 (oldest) should be fully consumed
    expect($entry1->remaining_quantity)->toBe(0);

    // Entry 2 should be partially consumed (15 - 2 = 13 remaining)
    expect($entry2->remaining_quantity)->toBe(13);

    // Entry 3 should not be touched
    expect($entry3->remaining_quantity)->toBe(8);

    // Check that two saida records were created
    $saidas = Inventory::where('product_id', $product->id)
        ->where('type', 'saida')
        ->orderBy('id', 'asc')
        ->get();

    expect($saidas->count())->toBe(2);

    // First saida: 10 units from LOT-001
    expect($saidas[0]->quantity)->toBe(10);
    expect($saidas[0]->lot_number)->toBe('LOT-001');
    expect($saidas[0]->reference)->toBe('REF-TEST-123');

    // Second saida: 2 units from LOT-002
    expect($saidas[1]->quantity)->toBe(2);
    expect($saidas[1]->lot_number)->toBe('LOT-002');
    expect($saidas[1]->reference)->toBe('REF-TEST-123');
});

test('fifo allocation throws exception on insufficient stock', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $product = Product::create([
        'name'          => 'Produto Teste FIFO 2',
        'sku'           => 'SKU-TEST-FIFO2',
        'barcode'       => '1234567890124',
        'unit_price'    => 10.00,
        'quantity'      => 5,
        'reorder_level' => 5,
        'status'        => 'ativo',
    ]);

    Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 5,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'lot_number'         => 'LOT-004',
        'entry_date'         => now(),
        'user_id'            => $user->id,
    ]);

    expect(fn () => FIFOStockService::allocateFIFOStock($product, 10, 'REF-TEST-FAIL', $user->id))
        ->toThrow(\Exception::class, "Estoque insuficiente");
});
