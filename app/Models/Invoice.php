<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'number', 'series', 'type', 'status',
        'issuer_name', 'issuer_cnpj', 'issuer_address', 'issuer_city', 'issuer_state', 'issuer_zip',
        'recipient_name', 'recipient_document', 'recipient_email', 'recipient_phone',
        'recipient_address', 'recipient_city', 'recipient_state', 'recipient_zip',
        'subtotal', 'discount', 'shipping', 'total',
        'payment_method', 'notes', 'due_date', 'issued_at',
        'user_id', 'supplier_id',
    ];

    protected $casts = [
        'subtotal'  => 'decimal:2',
        'discount'  => 'decimal:2',
        'shipping'  => 'decimal:2',
        'total'     => 'decimal:2',
        'due_date'  => 'date',
        'issued_at' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Gera o próximo número de NF sequencial
     */
    public static function nextNumber(): string
    {
        $last = self::orderByDesc('id')->first();
        $seq  = $last ? ((int) substr($last->number, -5) + 1) : 1;
        return 'NF-' . date('Y') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'rascunho'  => 'Rascunho',
            'emitida'   => 'Emitida',
            'cancelada' => 'Cancelada',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'rascunho'  => 'yellow',
            'emitida'   => 'green',
            'cancelada' => 'red',
            default     => 'gray',
        };
    }

    public function paymentLabel(): string
    {
        return match ($this->payment_method) {
            'dinheiro'        => 'Dinheiro',
            'pix'             => 'PIX',
            'boleto'          => 'Boleto Bancário',
            'cartao_credito'  => 'Cartão de Crédito',
            'cartao_debito'   => 'Cartão de Débito',
            'transferencia'   => 'Transferência Bancária',
            default           => $this->payment_method,
        };
    }
}
