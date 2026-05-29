<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingInvoiceItem extends Model
{
    protected $fillable = [
        'incoming_invoice_id', 'product_id', 'product_code', 'description',
        'barcode', 'ncm', 'cfop', 'unit', 'quantity', 'unit_price', 'total_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:2',
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
