<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Criar Produto
    // ────────────────────────────────────────────────────────────────────────

    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Garante dimensões mínimas (fallback se não enviadas pelo form)
            $data['width']  = max(0.1, (float) ($data['width']  ?? 1.0));
            $data['height'] = max(0.1, (float) ($data['height'] ?? 1.0));
            $data['depth']  = max(0.1, (float) ($data['depth']  ?? 1.0));

            // Valida espaço físico antes de criar
            if (!empty($data['warehouse_location_id']) && !empty($data['quantity']) && $data['quantity'] > 0) {
                $location = WarehouseLocation::with('products')->find($data['warehouse_location_id']);
                if ($location) {
                    // Constrói um produto temporário para calcular o volume
                    $tempProduct = new Product($data);
                    $location->canFitProduct($tempProduct, (int) $data['quantity']);
                }
            }

            $product = Product::create($data);

            // Log de criação
            \App\Models\ProductAuditLog::create([
                'product_id' => $product->id,
                'field_name' => 'Produto Criado',
                'old_value'  => null,
                'new_value'  => $product->name,
                'action'     => 'create',
                'user_id'    => Auth::id(),
                'changed_at' => now(),
            ]);

            // Marca localização como ocupada
            if ($product->warehouse_location_id) {
                WarehouseLocation::where('id', $product->warehouse_location_id)
                    ->update(['is_occupied' => true]);
            }

            // Registra estoque inicial via InventoryService (com validação volumétrica)
            if ($product->quantity > 0) {
                // Recarrega para ter dimensões corretas
                $product->refresh();
                $this->inventoryService->registerEntry($product, $product->quantity, [
                    'notes'   => 'Entrada inicial — Cadastro do produto',
                    'user_id' => Auth::id(),
                ]);
                // registerEntry já incrementou quantity — revertemos o duplicado
                // (o Product::create já salvou quantity; registerEntry incrementou de novo)
                $product->decrement('quantity', $product->quantity - (int) ($data['quantity'] ?? 0));
            }

            return $product->fresh();
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Atualizar Produto
    // ────────────────────────────────────────────────────────────────────────

    public function updateProduct(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {

            // Garante dimensões mínimas
            if (array_key_exists('width', $data))  $data['width']  = max(0.1, (float) $data['width']);
            if (array_key_exists('height', $data)) $data['height'] = max(0.1, (float) $data['height']);
            if (array_key_exists('depth', $data))  $data['depth']  = max(0.1, (float) $data['depth']);

            $newLocId = $data['warehouse_location_id'] ?? null;
            $oldLocId = $product->warehouse_location_id;

            // ── Mudança de localização ──────────────────────────────────────
            if ($newLocId && $newLocId != $oldLocId) {
                $newLocation = WarehouseLocation::with('products')->findOrFail($newLocId);

                // Usa as novas dimensões (se foram alteradas) para calcular volume
                $tempProduct = clone $product;
                $tempProduct->width  = $data['width']  ?? $product->width;
                $tempProduct->height = $data['height'] ?? $product->height;
                $tempProduct->depth  = $data['depth']  ?? $product->depth;
                $currentQty = (int) ($data['quantity'] ?? $product->quantity);

                // Valida espaço na nova localização (não exclui nenhum produto — novo local)
                $newLocation->canFitProduct($tempProduct, $currentQty);

                // Libera localização antiga
                if ($oldLocId) {
                    $stillOccupied = Product::where('warehouse_location_id', $oldLocId)
                        ->where('id', '!=', $product->id)
                        ->exists();
                    if (!$stillOccupied) {
                        WarehouseLocation::where('id', $oldLocId)->update(['is_occupied' => false]);
                    }
                }

                // Marca nova localização como ocupada
                WarehouseLocation::where('id', $newLocId)->update(['is_occupied' => true]);

            } elseif ($newLocId && $newLocId == $oldLocId) {
                // ── Mesma localização, verifica se dimensões/quantidade mudaram ──
                $newQty = (int) ($data['quantity'] ?? $product->quantity);

                if ($newQty != $product->quantity
                    || ($data['width']  ?? $product->width)  != $product->width
                    || ($data['height'] ?? $product->height) != $product->height
                    || ($data['depth']  ?? $product->depth)  != $product->depth
                ) {
                    $location = WarehouseLocation::with('products')->find($oldLocId);
                    if ($location) {
                        $tempProduct = clone $product;
                        $tempProduct->width  = $data['width']  ?? $product->width;
                        $tempProduct->height = $data['height'] ?? $product->height;
                        $tempProduct->depth  = $data['depth']  ?? $product->depth;

                        // Exclui o produto atual do cálculo de ocupação
                        $location->canFitProduct($tempProduct, $newQty, $product->id);
                    }
                }
            }

            // ── Rastreia alterações antes de salvar ─────────────────────────
            $this->trackProductChanges($product, $data);

            $product->update($data);

            return $product;
        });
    }

    // ────────────────────────────────────────────────────────────────────────
    // Auditoria
    // ────────────────────────────────────────────────────────────────────────

    private function trackProductChanges(Product $product, array $newData): void
    {
        $fieldsToTrack = [
            'name'                  => 'Nome',
            'barcode'               => 'Código de Barras',
            'category'              => 'Categoria',
            'unit'                  => 'Unidade',
            'description'           => 'Descrição',
            'purchase_price'        => 'Preço de Compra',
            'cost_price'            => 'Custo Unitário',
            'unit_price'            => 'Preço de Venda',
            'wholesale_price'       => 'Preço Atacado',
            'margin_percent'        => 'Margem de Lucro',
            'quantity'              => 'Quantidade',
            'max_stock'             => 'Estoque Máximo',
            'reorder_level'         => 'Nível de Ressuprimento',
            'weight'                => 'Peso',
            'width'                 => 'Largura',
            'height'                => 'Altura',
            'depth'                 => 'Comprimento/Profundidade',
            'supplier_id'           => 'Fornecedor',
            'warehouse_location_id' => 'Localização',
            'status'                => 'Status',
            'ipi_percent'           => 'IPI',
            'icms_st_percent'       => 'ICMS ST',
            'shipping_cost'         => 'Frete',
            'discount_percent'      => 'Desconto',
        ];

        foreach ($fieldsToTrack as $fieldName => $displayName) {
            if (array_key_exists($fieldName, $newData)) {
                $oldValue = $product->{$fieldName};
                $newValue = $newData[$fieldName];

                if ($oldValue == $newValue) continue;

                \App\Models\ProductAuditLog::create([
                    'product_id' => $product->id,
                    'field_name' => $displayName,
                    'old_value'  => (string) $oldValue,
                    'new_value'  => (string) $newValue,
                    'action'     => 'update',
                    'user_id'    => Auth::id(),
                    'changed_at' => now(),
                ]);
            }
        }
    }
}
