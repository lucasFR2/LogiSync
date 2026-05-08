<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAuditLog extends Model
{
    protected $fillable = [
        'product_id',
        'field_name',
        'old_value',
        'new_value',
        'action',
        'user_id',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Log pertence a um Produto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento: Log foi registrado por um Usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
