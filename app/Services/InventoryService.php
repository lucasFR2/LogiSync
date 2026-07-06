<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\WarehouseLocation;
use App\Helpers\Logger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * InventoryService
 *
 * Centraliza toda a lógica de entrada, saída e atualização de estoque
 * com validação volumétrica obrigatória antes de qualquer movimentação.
 *
 * Regra volumétrica:
 *   - Cada posição (WarehouseLocation) é um cubo 10×10×10 = 1.000 u³.
 *   - Cada unidade de produto ocupa (width × height × depth) u³.
 *   - A soma de (volume_unitário × quantidade) de todos os produtos
 *     na posição nunca pode exceder 1.000 u³.
 */
class InventoryService
{
    // ────────────────────────────────────────────────────────────────────────
    // Entrada de Estoque
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Registra uma entrada de estoque, validando espaço físico antes de inserir.
     *
     * @param  Product  $product   Produto a entrar.
     * @param  int|float $quantity Quantidade física declarada no documento.
     * @param  array    $data      Dados adicionais (checked_quantity, supplier_id, notes…).
     *
     * @throws \Exception Se não houver espaço suficiente na posição do produto.
     */
    public function registerEntry(Product $product, $quantity, array $data = []): Inventory
    {
        return DB::transaction(function () use ($product, $quantity, $data) {
            $checkedQty = (int) ($data['checked_quantity'] ?? $quantity);

            // ── Mudança de localização solicitada ────────────────────────────
            $newLocId = isset($data['warehouse_location_id']) && $data['warehouse_location_id'] !== ''
                ? (int) $data['warehouse_location_id']
                : null;

            $oldLocId = $product->warehouse_location_id;

            if ($newLocId && $newLocId !== $oldLocId) {
                // Valida espaço físico na nova localização
                $newLocation = WarehouseLocation::with('products')->findOrFail($newLocId);
                $this->validateSpecificLocation($newLocation, $product, $checkedQty);

                // Migra o produto para a nova localização
                $product->update(['warehouse_location_id' => $newLocId]);
                $product->refresh();

                // Marca nova localização como ocupada
                WarehouseLocation::where('id', $newLocId)->update(['is_occupied' => true]);

                // Libera localização anterior se nenhum outro produto a usa
                if ($oldLocId) {
                    $stillOccupied = Product::where('warehouse_location_id', $oldLocId)
                        ->where('id', '!=', $product->id)
                        ->exists();
                    if (!$stillOccupied) {
                        WarehouseLocation::where('id', $oldLocId)->update(['is_occupied' => false]);
                    }
                }
            } else {
                // Localização original: valida espaço normalmente
                $this->validateLocationSpace($product, $checkedQty);
            }

            $entryDate = null;
            if (!empty($data['entry_date'])) {
                try {
                    $entryDate = Carbon::parse($data['entry_date'])->toDateTimeString();
                } catch (\Exception $e) {
                    $entryDate = null;
                }
            }

            $conferenceStatus = $checkedQty == $quantity ? 'confirmada' : 'divergente';

            $inventory = Inventory::create([
                'product_id'         => $product->id,
                'quantity'           => $quantity,
                'checked_quantity'   => $checkedQty,
                'remaining_quantity' => $checkedQty,
                'type'               => 'entrada',
                'status'             => 'confirmada',
                'supplier_id'        => $data['supplier_id'] ?? null,
                'lot_number'         => $data['lot_number'] ?? null,
                'expiry_date'        => $data['expiry_date'] ?? null,
                'notes'              => $data['notes'] ?? null,
                'conference_status'  => $conferenceStatus,
                'conference_notes'   => $data['conference_notes'] ?? null,
                'user_id'            => $data['user_id'] ?? Auth::id(),
                'entry_date'         => $entryDate,
            ]);

            // Increment product stock
            $product->increment('quantity', $checkedQty);

            return $inventory;
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Atualização de Entrada
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Atualiza uma entrada de estoque existente, recalculando a ocupação volumétrica.
     *
     * @param  Inventory  $inventory  Registro a ser atualizado.
     * @param  int|float  $newQty     Nova quantidade.
     * @param  array      $data       Dados adicionais (notes, entry_date, product_id).
     *
     * @throws \Exception Se a nova quantidade exceder o espaço disponível.
     */
    public function updateEntry(Inventory $inventory, $newQty, array $data = []): Inventory
    {
        return DB::transaction(function () use ($inventory, $newQty, $data) {
            $oldQty       = (int) $inventory->quantity;
            $newProductId = $data['product_id'] ?? $inventory->product_id;
            $newQty       = (int) $newQty;

            if ($inventory->product_id != $newProductId) {
                // Produto trocou: libera do antigo, valida e ocupa no novo
                $oldProduct = Product::find($inventory->product_id);
                $newProduct = Product::findOrFail($newProductId);

                // Valida espaço no novo produto (excluindo o inventário atual não faz sentido aqui
                // pois o produto é diferente — valida a posição do novo produto com nova qty)
                $this->validateLocationSpace($newProduct, $newQty);

                if ($oldProduct) {
                    $oldProduct->decrement('quantity', $oldQty);
                }
                $newProduct->increment('quantity', $newQty);

            } else {
                // Mesmo produto: calcula delta e valida se está aumentando
                $diff    = $newQty - $oldQty;
                $product = Product::findOrFail($inventory->product_id);

                if ($diff > 0) {
                    // Só valida espaço se está adicionando mais volume
                    // Para a validação, passamos o delta como "nova entrada"
                    $this->validateLocationSpace($product, $diff, excludeCurrentQty: false);
                }

                if ($diff > 0) {
                    $product->increment('quantity', $diff);
                } elseif ($diff < 0) {
                    $product->decrement('quantity', abs($diff));
                }
            }

            // Atualiza o registro
            $updateData = [
                'product_id' => $newProductId,
                'quantity'   => $newQty,
                'notes'      => $data['notes'] ?? $inventory->notes,
            ];

            if (!empty($data['entry_date'])) {
                try {
                    $dt = Carbon::parse($data['entry_date']);
                    $updateData['entry_date'] = $dt->toDateTimeString();
                    $inventory->timestamps = false;
                    $inventory->created_at = $dt;
                    $inventory->updated_at = $dt;
                } catch (\Exception $e) {
                    // ignora data inválida
                }
            }

            $inventory->fill($updateData)->save();

            return $inventory;
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Remoção de Entrada
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Remove uma entrada de estoque e reverte a quantidade do produto corretamente.
     *
     * @param  Inventory  $inventory  Registro a ser removido.
     */
    public function deleteEntry(Inventory $inventory): void
    {
        DB::transaction(function () use ($inventory) {
            $product = Product::find($inventory->product_id);

            // Reverte apenas entradas (saídas já foram deduzidas no momento do registro)
            if ($product && $inventory->type === 'entrada') {
                $revertQty = (int) ($inventory->checked_quantity ?? $inventory->quantity);
                $safeDecrement = min($revertQty, $product->quantity);
                if ($safeDecrement > 0) {
                    $product->decrement('quantity', $safeDecrement);
                }
            }

            $inventory->delete();
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Validação Volumétrica
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Valida se a quantidade informada do produto cabe na sua localização atual.
     *
     * @param  Product    $product           O produto a ser inserido/movido.
     * @param  int|float  $quantity          Quantidade a inserir.
     * @param  bool       $excludeCurrentQty Se true, exclui a quantidade atual do produto
     *                                       do cálculo de ocupação (usado em edições do mesmo produto).
     *
     * @throws \Exception Se não houver espaço suficiente.
     */
    public function validateLocationSpace(
        Product $product,
        $quantity,
        bool $excludeCurrentQty = false
    ): void {
        if (!$product->warehouse_location_id || $quantity <= 0) {
            return; // Sem localização vinculada → sem restrição física
        }

        $location = WarehouseLocation::with('products')->find($product->warehouse_location_id);
        if (!$location) {
            return;
        }

        // Ao excluir a qty atual (ex: edição do mesmo produto na mesma posição),
        // usamos o produto como exclusão no cálculo de usedVolume e
        // depois somamos o novo volume total do produto.
        if ($excludeCurrentQty) {
            $location->canFitProduct($product, $quantity, excludeProductId: $product->id);
        } else {
            $location->canFitProduct($product, $quantity);
        }
    }

    /**
     * Valida se um produto com dimensões e quantidade informados cabe numa localização específica.
     * Usado por ProductService ao mover produto para nova localização.
     *
     * @param  WarehouseLocation  $location         Localização destino.
     * @param  Product            $product          Produto a ser alocado.
     * @param  int|float          $quantity         Quantidade do produto.
     * @param  int|null           $excludeProductId Exclui este produto do cálculo (ao editar).
     *
     * @throws \Exception Se não houver espaço suficiente.
     */
    public function validateSpecificLocation(
        WarehouseLocation $location,
        Product $product,
        $quantity,
        ?int $excludeProductId = null
    ): void {
        if ($quantity <= 0) return;
        $location->canFitProduct($product, $quantity, $excludeProductId);
    }
}
