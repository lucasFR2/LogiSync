<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingInvoiceItem extends Model
{
    protected $fillable = [
        'incoming_invoice_id', 'product_id', 'product_code', 'description',
        'barcode', 'ncm', 'cfop', 'unit', 'quantity', 'unit_price', 'total_price',
        'iss_rate', 'pis_rate', 'cofins_rate', 'csll_rate', 'irpj_rate', 'cpp_rate', 'ipi_rate',
        'icms_rate', 'icms_cst', 'icms_orig', 'icms_st_rate', 'icms_st_mva', 'icms_st_cst',
        'ibs_rate', 'cbs_rate', 'is_rate', 'icms_red_bc', 'icms_mod_bc'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:2',
        'iss_rate' => 'decimal:2',
        'pis_rate' => 'decimal:2',
        'cofins_rate' => 'decimal:2',
        'csll_rate' => 'decimal:2',
        'irpj_rate' => 'decimal:2',
        'cpp_rate' => 'decimal:2',
        'ipi_rate' => 'decimal:2',
        'icms_rate' => 'decimal:2',
        'icms_orig' => 'integer',
        'icms_st_rate' => 'decimal:2',
        'icms_st_mva' => 'decimal:2',
        'ibs_rate' => 'decimal:2',
        'cbs_rate' => 'decimal:2',
        'is_rate' => 'decimal:2',
        'icms_red_bc' => 'decimal:2',
        'icms_mod_bc' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(IncomingInvoice::class, 'incoming_invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
