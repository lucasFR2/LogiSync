<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FIFOStockService
{
    /**
     * Aloca e consome o estoque de acordo com a regra de FIFO (First-In, First-Out).
     *
     * @param Product $product O produto que sofrerá a baixa.
     * @param int $quantity A quantidade total a ser baixada.
     * @param string $reference A referência da movimentação (ex: NF, Pedido).
     * @param int|null $userId O ID do usuário que realizou a ação.
     * @throws \Exception Se o estoque for insuficiente.
     */
    public static function allocateFIFOStock(Product $product, int $quantity, string $reference, ?int $userId = null)
    {
        if ($quantity <= 0) {
            return;
        }

        $userId = $userId ?? Auth::id();

        DB::transaction(function () use ($product, $quantity, $reference, $userId) {
            // Recarrega o produto para garantir dados atualizados e trava a linha para concorrência
            $productObj = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();

            if ($productObj->quantity < $quantity) {
                throw new \Exception("Estoque insuficiente para o produto '{$productObj->name}'. Disponível: {$productObj->quantity}, Solicitado: {$quantity}");
            }

            // Busca as entradas confirmadas com saldo disponível
            $entries = Inventory::where('product_id', $productObj->id)
                ->where('type', 'entrada')
                ->where('status', 'confirmada')
                ->where('remaining_quantity', '>', 0)
                ->orderBy('entry_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $totalAvailable = $entries->sum('remaining_quantity');
            if ($totalAvailable < $quantity) {
                // Se a soma do FIFO estiver inconsistente com a quantidade global do produto,
                // lançamos exceção ou ajustamos. Em WMS real, impede a saída por inconsistência.
                throw new \Exception("Inconsistência de saldo FIFO para o produto '{$productObj->name}'. Total em lotes FIFO: {$totalAvailable}, Solicitado: {$quantity}");
            }

            $qtyToAllocate = $quantity;

            foreach ($entries as $entry) {
                if ($qtyToAllocate <= 0) {
                    break;
                }

                $allocatedFromThisEntry = 0;

                if ($entry->remaining_quantity >= $qtyToAllocate) {
                    $allocatedFromThisEntry = $qtyToAllocate;
                    $entry->remaining_quantity -= $qtyToAllocate;
                    $qtyToAllocate = 0;
                } else {
                    $allocatedFromThisEntry = $entry->remaining_quantity;
                    $qtyToAllocate -= $entry->remaining_quantity;
                    $entry->remaining_quantity = 0;
                }

                $entry->save();

                // Cria o registro de saída para esta porção do lote/entrada (rastreabilidade completa)
                $lotInfo = $entry->lot_number ? "Lote #{$entry->lot_number}" : "Entrada sem lote";
                $expiryInfo = $entry->expiry_date ? " (Validade: " . $entry->expiry_date->format('d/m/Y') . ")" : "";
                
                Inventory::create([
                    'product_id'         => $productObj->id,
                    'quantity'           => $allocatedFromThisEntry,
                    'remaining_quantity' => 0, // Saída não tem saldo disponível
                    'type'               => 'saida',
                    'status'             => 'confirmada',
                    'reference'          => $reference,
                    'notes'              => "Baixa FIFO automatizada do {$lotInfo}{$expiryInfo}. Referência: {$reference}",
                    'user_id'            => $userId,
                    'supplier_id'        => $entry->supplier_id,
                    'entry_date'         => now(),
                    'lot_number'         => $entry->lot_number,
                    'expiry_date'        => $entry->expiry_date,
                ]);
            }

            // Decrementa a quantidade global do produto
            $productObj->decrement('quantity', $quantity);
        });
    }
}
