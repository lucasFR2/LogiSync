<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'description', 'ncm', 'cfop',
        'unit', 'quantity', 'unit_price', 'discount', 'total',
        'icms_cst', 'icms_orig', 'icms_mod_bc', 'icms_red_bc',
        'icms_base', 'icms_rate', 'icms_value',
        'icms_st_cst', 'icms_st_mva', 'icms_st_base', 'icms_st_rate', 'icms_st_value',
        'ipi_cst', 'ipi_enq', 'ipi_base', 'ipi_rate', 'ipi_value',
        'pis_cst', 'pis_base', 'pis_rate', 'pis_value',
        'cofins_cst', 'cofins_base', 'cofins_rate', 'cofins_value',
        'iss_cst', 'iss_base', 'iss_rate', 'iss_value',
        'csll_cst', 'csll_base', 'csll_rate', 'csll_value',
        'irpj_cst', 'irpj_base', 'irpj_rate', 'irpj_value',
        'cpp_cst', 'cpp_base', 'cpp_rate', 'cpp_value',
        'ibs_cst', 'ibs_base', 'ibs_rate', 'ibs_value',
        'cbs_cst', 'cbs_base', 'cbs_rate', 'cbs_value',
        'is_cst', 'is_base', 'is_rate', 'is_value',
        'ii_base', 'ii_rate', 'ii_value', 'ii_desp', 'ii_iof',
    ];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'unit_price'    => 'decimal:2',
        'discount'      => 'decimal:2',
        'total'         => 'decimal:2',
        'icms_orig'     => 'integer',
        'icms_mod_bc'   => 'integer',
        'icms_red_bc'   => 'decimal:2',
        'icms_base'     => 'decimal:2',
        'icms_rate'     => 'decimal:2',
        'icms_value'    => 'decimal:2',
        'icms_st_mva'   => 'decimal:2',
        'icms_st_base'  => 'decimal:2',
        'icms_st_rate'  => 'decimal:2',
        'icms_st_value' => 'decimal:2',
        'ipi_base'      => 'decimal:2',
        'ipi_rate'      => 'decimal:2',
        'ipi_value'     => 'decimal:2',
        'pis_base'      => 'decimal:2',
        'pis_rate'      => 'decimal:2',
        'pis_value'     => 'decimal:2',
        'cofins_base'   => 'decimal:2',
        'cofins_rate'   => 'decimal:2',
        'cofins_value'  => 'decimal:2',
        'iss_base'      => 'decimal:2',
        'iss_rate'      => 'decimal:2',
        'iss_value'     => 'decimal:2',
        'csll_base'     => 'decimal:2',
        'csll_rate'     => 'decimal:2',
        'csll_value'    => 'decimal:2',
        'irpj_base'     => 'decimal:2',
        'irpj_rate'     => 'decimal:2',
        'irpj_value'    => 'decimal:2',
        'cpp_base'      => 'decimal:2',
        'cpp_rate'      => 'decimal:2',
        'cpp_value'     => 'decimal:2',
        'ibs_base'      => 'decimal:2',
        'ibs_rate'      => 'decimal:2',
        'ibs_value'     => 'decimal:2',
        'cbs_base'      => 'decimal:2',
        'cbs_rate'      => 'decimal:2',
        'cbs_value'     => 'decimal:2',
        'is_base'       => 'decimal:2',
        'is_rate'       => 'decimal:2',
        'is_value'      => 'decimal:2',
        'ii_base'       => 'decimal:2',
        'ii_rate'       => 'decimal:2',
        'ii_value'      => 'decimal:2',
        'ii_desp'       => 'decimal:2',
        'ii_iof'        => 'decimal:2',
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
