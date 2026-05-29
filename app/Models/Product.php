<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'unit_price',
        'selling_price',
        'wholesale_price',
        'purchase_price',
        'tax_percent',
        'ipi_percent',
        'icms_st_percent',
        'other_taxes_percent',
        'shipping_cost',
        'other_costs',
        'margin_percent',
        'discount_percent',
        'quantity',
        'max_stock',
        'reorder_level',
        'package_quantity',
        'weight',
        'height',
        'width',
        'depth',
        'category',
        'unit',
        'warehouse_location',
        'warehouse_location_id',
        'supplier_id',
        'status',
        // Campos de localização física
        'warehouse_location_code',
        'aisle',
        'shelf',
        'level',
        'box',
        'location_notes',
        'location_updated_at',
        // Campos Fiscais
        'ncm',
        'cfop_default',
        'cest',
        'iss_rate',
        'pis_rate',
        'cofins_rate',
        'csll_rate',
        'irpj_rate',
        'cpp_rate',
        'ipi_rate',
        'icms_rate',
        'icms_cst',
        'icms_orig',
        'icms_st_rate',
        'icms_st_mva',
        'icms_st_cst',
        'ibs_rate',
        'cbs_rate',
        'is_rate',
        'icms_red_bc',
        'icms_mod_bc',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'max_stock' => 'integer',
        'reorder_level' => 'integer',
        'iss_rate' => 'decimal:2',
        'pis_rate' => 'decimal:2',
        'cofins_rate' => 'decimal:2',
        'csll_rate' => 'decimal:2',
        'irpj_rate' => 'decimal:2',
        'cpp_rate' => 'decimal:2',
        'ipi_rate' => 'decimal:2',
        'icms_rate' => 'decimal:2',
        'icms_orig' => 'integer',
        'icms_st_rate' => 'decimal:2',
        'icms_st_mva' => 'decimal:2',
        'ibs_rate' => 'decimal:2',
        'cbs_rate' => 'decimal:2',
        'is_rate' => 'decimal:2',
        'icms_red_bc' => 'decimal:2',
        'icms_mod_bc' => 'integer',
    ];

    /**
     * Relação: produto pertence a uma localização no armazém
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_id');
    }

    /**
     * Relationship: Um produto tem muitas entradas/movimentações de estoque
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Relationship: Um produto pertence a um fornecedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship: Um produto tem muitos logs de auditoria
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(ProductAuditLog::class)->latest('changed_at');
    }

    /**
     * Scopes para status de estoque
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeBaixoEstoque($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    public function scopeAcimaDoNivel($query)
    {
        return $query->whereColumn('quantity', '>', 'reorder_level');
    }

    /**
     * Recuperar localização formatada do produto
     * Exemplo: "A-01-03" ou "Corredor A / Prateleira 01 / Nível 3"
     */
    public function getFormattedLocation(): ?string
    {
        if ($this->warehouse_location_code) {
            return $this->warehouse_location_code;
        }

        $parts = [];
        if ($this->aisle) $parts[] = "Corredor {$this->aisle}";
        if ($this->shelf) $parts[] = "Prateleira {$this->shelf}";
        if ($this->level) $parts[] = "Nível {$this->level}";
        if ($this->box) $parts[] = "Box {$this->box}";

        return !empty($parts) ? implode(' / ', $parts) : null;
    }

    /**
     * Recuperar componentes da localização como array
     */
    public function getLocationComponents(): array
    {
        return [
            'code' => $this->warehouse_location_code,
            'aisle' => $this->aisle,
            'shelf' => $this->shelf,
            'level' => $this->level,
            'box' => $this->box,
            'notes' => $this->location_notes,
            'updated_at' => $this->location_updated_at,
        ];
    }

    /**
     * Atualizar localização do produto com tracking de mudança
     */
    public function updateLocation(array $data): bool
    {
        $this->update([
            'warehouse_location_code' => $data['warehouse_location_code'] ?? $this->warehouse_location_code,
            'aisle' => $data['aisle'] ?? $this->aisle,
            'shelf' => $data['shelf'] ?? $this->shelf,
            'level' => $data['level'] ?? $this->level,
            'box' => $data['box'] ?? $this->box,
            'location_notes' => $data['location_notes'] ?? $this->location_notes,
            'location_updated_at' => now(),
        ]);

        return true;
    }
}
