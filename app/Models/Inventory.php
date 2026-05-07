<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'reference',
        'notes',
        'status',
        'user_id',
        'entry_date',
        'lot_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'entry_date' => 'date',
        'expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
