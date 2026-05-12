<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'description', 'ncm', 'cfop',
        'unit', 'quantity', 'unit_price', 'discount', 'total',
        'icms_base', 'icms_rate', 'icms_value',
        'ipi_rate', 'ipi_value',
        'pis_rate', 'pis_value',
        'cofins_rate', 'cofins_value',
    ];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'unit_price'    => 'decimal:2',
        'discount'      => 'decimal:2',
        'total'         => 'decimal:2',
        'icms_base'     => 'decimal:2',
        'icms_rate'     => 'decimal:2',
        'icms_value'    => 'decimal:2',
        'ipi_rate'      => 'decimal:2',
        'ipi_value'     => 'decimal:2',
        'pis_rate'      => 'decimal:2',
        'pis_value'     => 'decimal:2',
        'cofins_rate'   => 'decimal:2',
        'cofins_value'  => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
