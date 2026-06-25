<?php

use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Role;
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

test('outbound invoice does not reduce stock upon emission, but does so upon conclusion', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a product with some initial stock
    $product = Product::create([
        'name'          => 'Produto Outbound Test',
        'sku'           => 'SKU-OUT-TEST',
        'barcode'       => '1111111111111',
        'unit_price'    => 20.00,
        'quantity'      => 0, // starts empty
        'reorder_level' => 5,
        'status'        => 'ativo',
    ]);

    // Add 20 units of stock
    Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 20,
        'remaining_quantity' => 20,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'entry_date'         => now(),
        'user_id'            => $user->id,
    ]);
    $product->update(['quantity' => 20]);

    // Process an exit invoice
    $invoiceService = app(\App\Services\InvoiceService::class);
    $data = [
        'number'            => 'NF-2026-00001',
        'series'            => '001',
        'type'              => 'saida',
        'recipient_name'    => 'Cliente Teste',
        'recipient_document'=> '000.000.000-00',
        'items'             => [
            [
                'product_id'  => $product->id,
                'description' => $product->name,
                'quantity'    => 5,
                'unit_price'  => 20.00,
                'discount'    => 0,
            ]
        ]
    ];

    // Emit the invoice
    $invoice = $invoiceService->processInvoice($data, null, true);

    $product->refresh();
    // Stock should still be 20 because it was not concluded yet
    expect($product->quantity)->toBe(20);
    expect($invoice->status)->toBe('emitida');

    // Now conclude the invoice
    $invoiceService->concludeInvoice($invoice);

    $product->refresh();
    // Stock should now be 15
    expect($product->quantity)->toBe(15);
    expect($invoice->status)->toBe('concluída');
});

test('manual conference conclude triggers stock reduction for emitted outbound invoice', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'description' => 'Administrator']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($user);

    $product = Product::create([
        'name'          => 'Produto Conferencia Manual Test',
        'sku'           => 'SKU-CONF-MAN',
        'barcode'       => '2222222222222',
        'unit_price'    => 30.00,
        'quantity'      => 0,
        'reorder_level' => 5,
        'status'        => 'ativo',
    ]);

    Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 15,
        'remaining_quantity' => 15,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'entry_date'         => now(),
        'user_id'            => $user->id,
    ]);
    $product->update(['quantity' => 15]);

    $invoiceService = app(\App\Services\InvoiceService::class);
    $data = [
        'number'            => 'NF-2026-00002',
        'series'            => '001',
        'type'              => 'saida',
        'recipient_name'    => 'Cliente Teste 2',
        'recipient_document'=> '000.000.000-00',
        'items'             => [
            [
                'product_id'  => $product->id,
                'description' => $product->name,
                'quantity'    => 4,
                'unit_price'  => 30.00,
                'discount'    => 0,
            ]
        ]
    ];

    // Emit invoice (stock is not reduced yet)
    $invoice = $invoiceService->processInvoice($data, null, true);
    expect($product->fresh()->quantity)->toBe(15);

    // Call manual conference endpoint to set status to Conferida
    $response = $this->post(route('invoices.confer', $invoice), [
        'conference_status' => 'Conferida',
        'conference_notes'  => 'Manualmente conferido sem erros',
    ]);

    $response->assertRedirect(route('invoices.show', $invoice));
    $response->assertSessionHas('success');

    // Stock should now be reduced to 11 and invoice status set to concluída
    expect($product->fresh()->quantity)->toBe(11);
    expect($invoice->fresh()->status)->toBe('concluída');
});

test('emitting a pre-conferenced draft outbound invoice triggers stock reduction', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'description' => 'Administrator']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($user);

    $product = Product::create([
        'name'          => 'Produto Pre Conferenced Test',
        'sku'           => 'SKU-PRE-CONF',
        'barcode'       => '3333333333333',
        'unit_price'    => 40.00,
        'quantity'      => 0,
        'reorder_level' => 5,
        'status'        => 'ativo',
    ]);

    Inventory::create([
        'product_id'         => $product->id,
        'quantity'           => 10,
        'remaining_quantity' => 10,
        'type'               => 'entrada',
        'status'             => 'confirmada',
        'entry_date'         => now(),
        'user_id'            => $user->id,
    ]);
    $product->update(['quantity' => 10]);

    $invoiceService = app(\App\Services\InvoiceService::class);
    $data = [
        'number'            => 'NF-2026-00003',
        'series'            => '001',
        'type'              => 'saida',
        'recipient_name'    => 'Cliente Teste 3',
        'recipient_document'=> '000.000.000-00',
        'items'             => [
            [
                'product_id'  => $product->id,
                'description' => $product->name,
                'quantity'    => 3,
                'unit_price'  => 40.00,
                'discount'    => 0,
            ]
        ]
    ];

    // Create invoice as draft (isEmitting = false)
    $invoice = $invoiceService->processInvoice($data, null, false);
    expect($product->fresh()->quantity)->toBe(10);
    expect($invoice->status)->toBe('rascunho');

    // Conclude manual conference on the draft
    $this->post(route('invoices.confer', $invoice), [
        'conference_status' => 'Conferida',
        'conference_notes'  => 'Conferido enquanto rascunho',
    ]);

    // Stock should not be reduced yet because it is still a draft
    expect($product->fresh()->quantity)->toBe(10);
    expect($invoice->fresh()->status)->toBe('rascunho');

    // Now emit the invoice (which is pre-conferenced)
    $data['action'] = 'emit'; // simulates the button clicked
    $invoiceService->processInvoice($data, $invoice, true);

    // Stock should be reduced to 7 and invoice status set to concluída
    expect($product->fresh()->quantity)->toBe(7);
    expect($invoice->fresh()->status)->toBe('concluída');
});

