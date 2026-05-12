<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingInvoice extends Model
{
    protected $fillable = [
        'access_key', 'number', 'series', 'supplier_id',
        'supplier_name', 'supplier_cnpj', 'emission_date',
        'total_amount', 'manifestation_status', 'entry_status', 'xml_data'
    ];

    protected $casts = [
        'emission_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(IncomingInvoiceItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->manifestation_status) {
            'pending'         => 'Pendente',
            'ciencia'         => 'Ciência da Operação',
            'confirmada'      => 'Confirmada',
            'desconhecimento' => 'Desconhecimento',
            'nao_realizada'   => 'Não Realizada',
            default           => 'Pendente',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->manifestation_status) {
            'pending'         => 'gray',
            'ciencia'         => 'info',
            'confirmada'      => 'success',
            'desconhecimento' => 'danger',
            'nao_realizada'   => 'warning',
            default           => 'gray',
        };
    }

    public function getEntryStatusLabelAttribute(): string
    {
        return $this->entry_status === 'imported' ? 'Importada' : 'Aguardando Entrada';
    }

    public function getEntryStatusColorAttribute(): string
    {
        return $this->entry_status === 'imported' ? 'success' : 'warning';
    }
}
