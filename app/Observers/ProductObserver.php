<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductAuditLog;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Registrar criação do produto
        ProductAuditLog::create([
            'product_id' => $product->id,
            'field_name' => 'product_created',
            'old_value' => null,
            'new_value' => $product->name,
            'action' => 'create',
            'user_id' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Product "updating" event.
     */
    public function updating(Product $product): void
    {
        // Capturar mudanças ANTES de salvar
        $original = $product->getOriginal();
        $changes = $product->getDirty();
        
        // Remover timestamps automáticos
        unset($changes['updated_at']);
        
        // Se não houver mudanças de negócio, não registrar
        if (empty($changes)) {
            return;
        }
        
        // Registrar cada campo alterado
        foreach ($changes as $field => $newValue) {
            $oldValue = $original[$field] ?? null;
            
            ProductAuditLog::create([
                'product_id' => $product->id,
                'field_name' => $this->getFieldLabel($field),
                'old_value' => $this->formatValue($field, $oldValue),
                'new_value' => $this->formatValue($field, $newValue),
                'action' => 'update',
                'user_id' => auth()->id(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Placeholder para listeners posteriores
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ProductAuditLog::create([
            'product_id' => $product->id,
            'field_name' => 'product_deleted',
            'old_value' => $product->name,
            'new_value' => null,
            'action' => 'delete',
            'user_id' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }

    /**
     * Formatar o rótulo do campo para exibição
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'Nome',
            'barcode' => 'Código de Barras',
            'description' => 'Descrição',
            'cost_price' => 'Preço de Custo',
            'unit_price' => 'Preço Unitário',
            'selling_price' => 'Preço de Venda',
            'quantity' => 'Quantidade',
            'max_stock' => 'Estoque Máximo',
            'reorder_level' => 'Nível de Reordenação',
            'package_quantity' => 'Quantidade por Pacote',
            'weight' => 'Peso',
            'height' => 'Altura',
            'width' => 'Largura',
            'depth' => 'Profundidade',
            'category' => 'Categoria',
            'unit' => 'Unidade',
            'warehouse_location' => 'Localização no Armazém',
            'supplier_id' => 'Fornecedor',
            'status' => 'Status',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Formatar valor para exibição
     */
    private function formatValue(string $field, $value): string
    {
        if ($value === null) {
            return '—';
        }

        // Preços com 2 casas decimais
        if (in_array($field, ['cost_price', 'unit_price', 'selling_price'])) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }

        // Status em maiúsculas
        if ($field === 'status') {
            return ucfirst($value);
        }

        return (string)$value;
    }
}
