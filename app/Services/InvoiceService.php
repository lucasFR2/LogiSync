<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceService
{
    public function processInvoice(array $data, ?Invoice $invoice = null, bool $isEmitting = false)
    {
        return DB::transaction(function () use ($data, $invoice, $isEmitting) {
            $subtotal = 0;
            $itemsData = [];

            // Pre-calculate invoice number for references
            $invoiceNumber = $invoice ? $invoice->number : ($data['number'] ?? Invoice::nextNumber());

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
                    
                    'icms_cst'     => $item['icms_cst'] ?? '00',
                    'icms_orig'    => (int) ($item['icms_orig'] ?? 0),
                    'icms_mod_bc'  => (int) ($item['icms_mod_bc'] ?? 3),
                    'icms_red_bc'  => (float) ($item['icms_red_bc'] ?? 0),
                    'icms_base'    => (float) ($item['icms_base'] ?? 0),
                    'icms_rate'    => (float) ($item['icms_rate'] ?? 0),
                    'icms_value'   => (float) ($item['icms_value'] ?? 0),

                    'icms_st_cst'  => $item['icms_st_cst'] ?? '10',
                    'icms_st_mva'  => (float) ($item['icms_st_mva'] ?? 0),
                    'icms_st_base' => (float) ($item['icms_st_base'] ?? 0),
                    'icms_st_rate' => (float) ($item['icms_st_rate'] ?? 0),
                    'icms_st_value'=> (float) ($item['icms_st_value'] ?? 0),

                    'ipi_cst'      => $item['ipi_cst'] ?? '50',
                    'ipi_enq'      => $item['ipi_enq'] ?? '999',
                    'ipi_base'     => (float) ($item['ipi_base'] ?? 0),
                    'ipi_rate'     => (float) ($item['ipi_rate'] ?? 0),
                    'ipi_value'    => (float) ($item['ipi_value'] ?? 0),

                    'pis_cst'      => $item['pis_cst'] ?? '01',
                    'pis_base'     => (float) ($item['pis_base'] ?? 0),
                    'pis_rate'     => (float) ($item['pis_rate'] ?? 0),
                    'pis_value'    => (float) ($item['pis_value'] ?? 0),

                    'cofins_cst'   => $item['cofins_cst'] ?? '01',
                    'cofins_base'  => (float) ($item['cofins_base'] ?? 0),
                    'cofins_rate'  => (float) ($item['cofins_rate'] ?? 0),
                    'cofins_value' => (float) ($item['cofins_value'] ?? 0),

                    'iss_cst'      => $item['iss_cst'] ?? '01',
                    'iss_base'     => (float) ($item['iss_base'] ?? 0),
                    'iss_rate'     => (float) ($item['iss_rate'] ?? 0),
                    'iss_value'    => (float) ($item['iss_value'] ?? 0),

                    'csll_cst'     => $item['csll_cst'] ?? '01',
                    'csll_base'    => (float) ($item['csll_base'] ?? 0),
                    'csll_rate'    => (float) ($item['csll_rate'] ?? 0),
                    'csll_value'   => (float) ($item['csll_value'] ?? 0),

                    'irpj_cst'     => $item['irpj_cst'] ?? '01',
                    'irpj_base'    => (float) ($item['irpj_base'] ?? 0),
                    'irpj_rate'    => (float) ($item['irpj_rate'] ?? 0),
                    'irpj_value'   => (float) ($item['irpj_value'] ?? 0),

                    'cpp_cst'      => $item['cpp_cst'] ?? '01',
                    'cpp_base'     => (float) ($item['cpp_base'] ?? 0),
                    'cpp_rate'     => (float) ($item['cpp_rate'] ?? 0),
                    'cpp_value'    => (float) ($item['cpp_value'] ?? 0),

                    'ibs_cst'      => $item['ibs_cst'] ?? '01',
                    'ibs_base'     => (float) ($item['ibs_base'] ?? 0),
                    'ibs_rate'     => (float) ($item['ibs_rate'] ?? 0),
                    'ibs_value'    => (float) ($item['ibs_value'] ?? 0),

                    'cbs_cst'      => $item['cbs_cst'] ?? '01',
                    'cbs_base'     => (float) ($item['cbs_base'] ?? 0),
                    'cbs_rate'     => (float) ($item['cbs_rate'] ?? 0),
                    'cbs_value'    => (float) ($item['cbs_value'] ?? 0),

                    'is_cst'       => $item['is_cst'] ?? '01',
                    'is_base'      => (float) ($item['is_base'] ?? 0),
                    'is_rate'      => (float) ($item['is_rate'] ?? 0),
                    'is_value'     => (float) ($item['is_value'] ?? 0),

                    'ii_base'      => (float) ($item['ii_base'] ?? 0),
                    'ii_rate'      => (float) ($item['ii_rate'] ?? 0),
                    'ii_value'     => (float) ($item['ii_value'] ?? 0),
                    'ii_desp'      => (float) ($item['ii_desp'] ?? 0),
                    'ii_iof'       => (float) ($item['ii_iof'] ?? 0),
                ];

                if ($isEmitting && !empty($item['product_id'])) {
                    // Saídas são baixadas somente em concludeInvoice (após conferência).
                    // Entradas e devoluções são processadas imediatamente na emissão.
                    if ($data['type'] === 'saida') {
                        Product::where('id', $item['product_id'])->update([
                            'unit_price'    => $price,
                            'selling_price' => $price,
                        ]);
                    } else {
                        $locId = !empty($item['warehouse_location_id'])
                            ? (int) $item['warehouse_location_id']
                            : null;
                        $this->handleStockMovement($item['product_id'], $qty, $data['type'], $invoiceNumber, $locId, $price);
                    }
                }
            }

            $sumIcmsSt = 0;
            $sumIpi = 0;
            $sumIi = 0;
            foreach ($itemsData as $itemRow) {
                $sumIcmsSt += (float) ($itemRow['icms_st_value'] ?? 0);
                $sumIpi += (float) ($itemRow['ipi_value'] ?? 0);
                $sumIi += (float) ($itemRow['ii_value'] ?? 0);
            }

            $discount   = (float) ($data['discount'] ?? 0);
            $shipping   = (float) ($data['shipping'] ?? 0);
            $grandTotal = $subtotal - $discount + $shipping + $sumIcmsSt + $sumIpi + $sumIi;

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
                $invoiceData['number'] = $invoiceNumber;
                $invoiceData['series'] = '001';
                $invoice = Invoice::create($invoiceData);
            }

            $invoice->items()->createMany($itemsData);

            // Se a NF de saída já estava conferida (ex: conferência feita enquanto era rascunho),
            // conclui imediatamente ao emitir. O lock pessimista dentro de concludeInvoice
            // garante que não haverá dupla baixa mesmo se for chamado novamente.
            $freshInvoice = $invoice->fresh();
            if ($isEmitting && $freshInvoice && $freshInvoice->type === 'saida' && $freshInvoice->conference_status === 'Conferida') {
                $this->concludeInvoice($invoice);
            }

            return $invoice;
        });
    }

    public function concludeInvoice(Invoice $invoice)
    {
        // Verificação rápida antes de abrir transação (otimização)
        if ($invoice->status === 'concluída') {
            return $invoice;
        }

        return DB::transaction(function () use ($invoice) {
            // Double-check com lock pessimista para evitar dupla baixa em requisições
            // concorrentes ou chamadas múltiplas dentro da mesma sessão.
            $locked = Invoice::where('id', $invoice->id)
                ->lockForUpdate()
                ->first();

            if (!$locked || $locked->status === 'concluída') {
                return $invoice;
            }

            $locked->update(['status' => 'concluída']);
            // Recarrega os itens a partir da instância travada
            $locked->load('items');

            foreach ($locked->items as $item) {
                if ($locked->type === 'saida' && !empty($item->product_id)) {
                    // Baixa de estoque via FIFO (garante estoque suficiente antes de deduzir)
                    FIFOStockService::allocateFIFOStock(
                        Product::findOrFail($item->product_id),
                        (int) $item->quantity,
                        $locked->number,
                        Auth::id()
                    );
                }
            }

            // Atualiza a instância original em memória para refletir o novo status
            $invoice->status = 'concluída';

            return $invoice;
        });
    }

    protected function handleStockMovement($productId, $qty, $type, $reference = '', ?int $warehouseLocationId = null, ?float $unitPrice = null)
    {
        $product = Product::findOrFail($productId);

        if ($type === 'saida') {
            // Baixa de estoque via FIFO. Cast explícito para int evita imprecisão de float.
            FIFOStockService::allocateFIFOStock($product, (int) $qty, $reference, Auth::id());
        } else {
            // Entrada padrão (NF de entrada ou devolução)

            // ── Migração de localização (se solicitada) ─────────────────────────
            if ($warehouseLocationId && $warehouseLocationId !== $product->warehouse_location_id) {
                $oldLocId = $product->warehouse_location_id;

                $product->update(['warehouse_location_id' => $warehouseLocationId]);
                $product->refresh();

                WarehouseLocation::where('id', $warehouseLocationId)->update(['is_occupied' => true]);

                if ($oldLocId) {
                    $stillOccupied = Product::where('warehouse_location_id', $oldLocId)
                        ->where('id', '!=', $product->id)
                        ->exists();
                    if (!$stillOccupied) {
                        WarehouseLocation::where('id', $oldLocId)->update(['is_occupied' => false]);
                    }
                }
            }

            $oldQty = $product->quantity;
            $oldCost = (float) $product->cost_price;
            $newQty = (int) $qty;
            $newCost = ($unitPrice !== null && $unitPrice > 0) ? $unitPrice : $oldCost;

            $averageCost = $oldQty > 0
                ? (($oldQty * $oldCost) + ($newQty * $newCost)) / ($oldQty + $newQty)
                : $newCost;

            $product->increment('quantity', (int) $qty);

            $product->update([
                'cost_price'     => $averageCost,
                'purchase_price' => $averageCost,
            ]);

            Inventory::create([
                'product_id'            => $product->id,
                'warehouse_location_id' => $warehouseLocationId ?: $product->warehouse_location_id,
                'quantity'              => (int) $qty,
                'remaining_quantity'    => (int) $qty,
                'unit_price'            => $newCost,
                'type'                  => 'entrada',
                'status'                => 'confirmada',
                'reference'             => $reference,
                'notes'                 => 'Entrada automática via NF ' . $reference,
                'user_id'               => Auth::id(),
            ]);
        }
    }
}
