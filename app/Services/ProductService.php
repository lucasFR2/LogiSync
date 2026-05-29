<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Check for exclusive location conflict
            if (!empty($data['warehouse_location_id'])) {
                $location = \App\Models\WarehouseLocation::find($data['warehouse_location_id']);
                if ($location && $location->is_occupied) {
                    throw new \Exception("Esta posição já está ocupada.");
                }
            }

            $product = Product::create($data);

            // Log creation
            \App\Models\ProductAuditLog::create([
                'product_id' => $product->id,
                'field_name' => 'Produto Criado',
                'old_value' => null,
                'new_value' => $product->name,
                'action' => 'create',
                'user_id' => Auth::id(),
                'changed_at' => now(),
            ]);

            if ($product->warehouse_location_id) {
                \App\Models\WarehouseLocation::where('id', $product->warehouse_location_id)
                    ->update(['is_occupied' => true]);
            }

            if ($product->quantity > 0) {
                Inventory::create([
                    'product_id' => $product->id,
                    'quantity'   => $product->quantity,
                    'type'       => 'entrada',
                    'status'     => 'confirmada',
                    'notes'      => 'Entrada inicial - Cadastro do produto',
                    'user_id'    => Auth::id(),
                ]);
            }

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data)
    {
        return DB::transaction(function () use ($product, $data) {
            $newLocId = $data['warehouse_location_id'] ?? null;
            $oldLocId = $product->warehouse_location_id;

            if ($newLocId && $newLocId != $oldLocId) {
                $location = \App\Models\WarehouseLocation::find($newLocId);
                if ($location && $location->is_occupied) {
                    $occupant = Product::where('warehouse_location_id', $location->id)->where('id', '!=', $product->id)->first();
                    if ($occupant) {
                        throw new \Exception("Localização ocupada pelo produto: " . $occupant->name);
                    }
                }
            }

            // Release old location
            if ($oldLocId && $oldLocId != $newLocId) {
                \App\Models\WarehouseLocation::where('id', $oldLocId)
                    ->update(['is_occupied' => false]);
            }

            // Track changes before updating
            $this->trackProductChanges($product, $data);

            $product->update($data);

            // Mark new location as occupied
            if ($newLocId && $newLocId != $oldLocId) {
                \App\Models\WarehouseLocation::where('id', $newLocId)
                    ->update(['is_occupied' => true]);
            }

            return $product;
        });
    }

    private function trackProductChanges(Product $product, array $newData)
    {
        // Campos que devem ser rastreados
        $fieldsToTrack = [
            'name' => 'Nome',
            'barcode' => 'Código de Barras',
            'category' => 'Categoria',
            'unit' => 'Unidade',
            'description' => 'Descrição',
            'purchase_price' => 'Preço de Compra',
            'cost_price' => 'Custo Unitário',
            'unit_price' => 'Preço de Venda',
            'wholesale_price' => 'Preço Atacado',
            'margin_percent' => 'Margem de Lucro',
            'quantity' => 'Quantidade',
            'max_stock' => 'Estoque Máximo',
            'reorder_level' => 'Nível de Ressuprimento',
            'weight' => 'Peso',
            'height' => 'Altura',
            'width' => 'Largura',
            'depth' => 'Profundidade',
            'supplier_id' => 'Fornecedor',
            'warehouse_location_id' => 'Localização',
            'status' => 'Status',
            'ipi_percent' => 'IPI',
            'icms_st_percent' => 'ICMS ST',
            'shipping_cost' => 'Frete',
            'discount_percent' => 'Desconto',
        ];

        foreach ($fieldsToTrack as $fieldName => $displayName) {
            if (array_key_exists($fieldName, $newData)) {
                $oldValue = $product->{$fieldName};
                $newValue = $newData[$fieldName];

                // Pula se não houve mudança
                if ($oldValue == $newValue) {
                    continue;
                }

                // Cria registro de auditoria
                \App\Models\ProductAuditLog::create([
                    'product_id' => $product->id,
                    'field_name' => $displayName,
                    'old_value' => (string) $oldValue,
                    'new_value' => (string) $newValue,
                    'action' => 'update',
                    'user_id' => Auth::id(),
                    'changed_at' => now(),
                ]);
            }
        }
    }
}
