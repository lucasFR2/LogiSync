<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'remaining_quantity',
        'type',
        'reference',
        'notes',
        'status',
        'user_id',
        'supplier_id',
        'entry_date',
        'lot_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'entry_date' => 'datetime',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventory) {
            // Se for entrada e a quantidade restante não foi explicitamente definida,
            // define a quantidade restante inicial igual à quantidade da movimentação.
            if ($inventory->type === 'entrada' && !isset($inventory->remaining_quantity)) {
                $inventory->remaining_quantity = $inventory->quantity;
            }
        });
    }

    /**
     * Relationship: Uma entrada pertence a um produto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Uma entrada foi registrada por um usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Uma entrada pode ter um fornecedor associado
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Scopes para filtros comuns
     */
    public function scopeEntradas($query)
    {
        return $query->where('type', 'entrada');
    }

    public function scopeSaidas($query)
    {
        return $query->where('type', 'saida');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('status', 'confirmada');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
