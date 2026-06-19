<?php

// Boot Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

$invoice = Invoice::with(['items.product'])->first();
if (!$invoice) {
    echo "No invoices found in database! Let's create a dummy one.\n";
    // Create dummy invoice for testing
    $invoice = Invoice::create([
        'number' => '000123',
        'series' => '1',
        'type' => 'saida',
        'status' => 'emitida',
        'issuer_name' => 'EMPRESA EMITENTE LTDA',
        'issuer_cnpj' => '12.345.678/0001-99',
        'issuer_address' => 'Rua do Emitente, 123 - Centro',
        'issuer_city' => 'São Paulo',
        'issuer_state' => 'SP',
        'issuer_zip' => '01001-000',
        'recipient_name' => 'CLIENTE DESTINATARIO S/A',
        'recipient_document' => '98.765.432/0001-21',
        'recipient_address' => 'Avenida do Cliente, 456 - Industrial',
        'recipient_city' => 'Belo Horizonte',
        'recipient_state' => 'MG',
        'recipient_zip' => '30123-456',
        'recipient_phone' => '(31) 3456-7890',
        'subtotal' => 1250.00,
        'discount' => 50.00,
        'shipping' => 100.00,
        'total' => 1300.00,
        'payment_method' => 'boleto',
        'notes' => 'Observacoes de teste para a nota fiscal.',
        'issued_at' => now(),
    ]);

    $invoice->items()->create([
        'description' => 'PRODUTO TESTE PREMIUM A1',
        'ncm' => '84713012',
        'unit' => 'un',
        'quantity' => 10,
        'unit_price' => 125.00,
        'total' => 1250.00,
        'icms_cst' => '00',
        'icms_rate' => 18.00,
        'icms_value' => 225.00,
        'icms_base' => 1250.00,
        'ipi_rate' => 5.00,
        'ipi_value' => 62.50,
    ]);
}

echo "Rendering PDF for Invoice #{$invoice->number}...\n";
$pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
          ->setPaper('a4', 'portrait');

$outputPath = __DIR__ . '/invoice_test.pdf';
$pdf->save($outputPath);
echo "PDF saved successfully to {$outputPath}!\n";
