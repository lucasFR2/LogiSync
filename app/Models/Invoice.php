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
        'user_id', 'supplier_id', 'conference_status', 'conferred_by', 'conferred_at', 'conference_notes',
        // Transportadora
        'carrier_id', 'carrier_name', 'carrier_cnpj', 'carrier_state_reg',
        'carrier_address', 'carrier_city', 'carrier_state',
        'cargo_type', 'freight_account', 'vehicle_plate', 'vehicle_uf',
        // Volumes
        'vol_quantity', 'vol_species', 'vol_brand', 'vol_number',
        'vol_gross_weight', 'vol_net_weight',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'discount'          => 'decimal:2',
        'shipping'          => 'decimal:2',
        'total'             => 'decimal:2',
        'due_date'          => 'date',
        'issued_at'         => 'date',
        'conferred_at'      => 'datetime',
        'vol_gross_weight'  => 'decimal:3',
        'vol_net_weight'    => 'decimal:3',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conferred_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function freightAccountLabel(): string
    {
        return match ($this->freight_account) {
            '0'     => '0 - Emitente',
            '1'     => '1 - Destinatário',
            '2'     => '2 - Terceiros',
            '3'     => '3 - Remetente',
            '9'     => '9 - Sem Frete',
            default => $this->freight_account ?? '0 - Emitente',
        };
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
            'concluída' => 'Concluída',
            'cancelada' => 'Cancelada',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'rascunho'  => 'yellow',
            'emitida'   => 'blue',
            'concluída' => 'green',
            'cancelada' => 'red',
            default     => 'gray',
        };
    }

    public function conferenceStatusLabel(): string
    {
        return match ($this->conference_status) {
            'Pendente'   => 'Pendente',
            'Conferida'  => 'Conferida',
            'Divergente' => 'Divergente',
            default      => $this->conference_status ?? 'Pendente',
        };
    }

    public function conferenceStatusColor(): string
    {
        return match ($this->conference_status) {
            'Pendente'   => 'orange',
            'Conferida'  => 'green',
            'Divergente' => 'red',
            default      => 'orange',
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
