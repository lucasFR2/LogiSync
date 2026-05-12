<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceService
{
    public function processInvoice(array $data, ?Invoice $invoice = null, bool $isEmitting = false)
    {
        return DB::transaction(function () use ($data, $invoice, $isEmitting) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $disc  = (float) ($item['discount'] ?? 0);
                $qty   = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $total = $qty * $price * (1 - $disc / 100);
                $subtotal += $total;

                $itemsData[] = [
                    'product_id'   => $item['product_id'] ?? null,
                    'description'  => $item['description'],
                    'ncm'          => $item['ncm'] ?? '0000.00.00',
                    'cfop'         => $item['cfop'] ?? '5.102',
                    'unit'         => $item['unit'] ?? 'un',
                    'quantity'     => $qty,
                    'unit_price'   => $price,
                    'discount'     => $disc,
                    'total'        => round($total, 2),
                    'icms_base'    => round($total, 2),
                    'icms_rate'    => (float) ($item['icms_rate'] ?? 0),
                    'icms_value'   => round($total * (($item['icms_rate'] ?? 0) / 100), 2),
                    'ipi_rate'     => (float) ($item['ipi_rate'] ?? 0),
                    'ipi_value'    => round($total * (($item['ipi_rate'] ?? 0) / 100), 2),
                    'pis_rate'     => (float) ($item['pis_rate'] ?? 0),
                    'pis_value'    => round($total * (($item['pis_rate'] ?? 0) / 100), 2),
                    'cofins_rate'  => (float) ($item['cofins_rate'] ?? 0),
                    'cofins_value' => round($total * (($item['cofins_rate'] ?? 0) / 100), 2),
                ];

                if ($isEmitting && !empty($item['product_id'])) {
                    $this->handleStockMovement($item['product_id'], $qty, $data['type']);
                }
            }

            $discount   = (float) ($data['discount'] ?? 0);
            $shipping   = (float) ($data['shipping'] ?? 0);
            $grandTotal = $subtotal - $discount + $shipping;

            $invoiceData = array_merge($data, [
                'status'   => $isEmitting ? 'emitida' : 'rascunho',
                'subtotal' => round($subtotal, 2),
                'total'    => round($grandTotal, 2),
                'user_id'  => Auth::id(),
            ]);

            // If we are updating an existing invoice
            if ($invoice) {
                $invoice->update($invoiceData);
                $invoice->items()->delete();
            } else {
                $invoiceData['number'] = Invoice::nextNumber();
                $invoiceData['series'] = '001';
                $invoice = Invoice::create($invoiceData);
            }

            $invoice->items()->createMany($itemsData);

            return $invoice;
        });
    }

    protected function handleStockMovement($productId, $qty, $type)
    {
        $product = Product::findOrFail($productId);
        
        if ($type === 'saida') {
            if ($product->quantity < $qty) {
                throw new \Exception("Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->quantity}");
            }
            $product->decrement('quantity', $qty);
            $movType = 'saida';
        } else {
            $product->increment('quantity', $qty);
            $movType = 'entrada';
        }

        Inventory::create([
            'product_id' => $product->id,
            'quantity'   => $qty,
            'type'       => $movType,
            'status'     => 'confirmada',
            'notes'      => 'Movimentação automática via ' . ($type === 'saida' ? 'Saída' : 'Entrada') . ' de NF',
            'user_id'    => Auth::id(),
        ]);
    }
}
