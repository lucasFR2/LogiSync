<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
    protected $fillable = [
        'aisle',
        'column',
        'level',
        'full_code',
        'is_occupied',
        'allow_shared',
    ];

    protected $casts = [
        'is_occupied'  => 'boolean',
        'allow_shared' => 'boolean',
    ];

    /**
     * Products assigned to this location.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'warehouse_location_id');
    }

    /**
     * Check if another product can use this location.
     */
    public function canAssignTo(?int $excludeProductId = null): bool
    {
        if (!$this->is_occupied) return true;
        if ($this->allow_shared) return true;

        // If the only occupant is the product being edited, allow it
        $occupants = $this->products()->when($excludeProductId, fn($q) => $q->where('id', '!=', $excludeProductId))->count();
        return $occupants === 0;
    }
}
