<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'cost_price',
        'unit_price',
        'selling_price',
        'purchase_price',
        'tax_percent',
        'shipping_cost',
        'margin_percent',
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
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'max_stock' => 'integer',
        'reorder_level' => 'integer',
    ];

    /**
<<<<<<< Updated upstream
     * Relationship: Um produto tem muitas entradas/movimentações de estoque
=======
     * Relação: produto pertence a uma localização no armazém
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_location_id');
    }

    /**
     * Relação: produto possui muitas entradas (inventories)
>>>>>>> Stashed changes
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
}
