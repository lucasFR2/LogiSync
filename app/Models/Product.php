<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'supplier',
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

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
