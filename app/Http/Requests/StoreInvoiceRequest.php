<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type'               => 'required|in:entrada,saida',
            'recipient_name'     => 'required|string|max:255',
            'recipient_document' => 'nullable|string|max:30',
            'recipient_email'    => 'nullable|email|max:255',
            'recipient_phone'    => 'nullable|string|max:20',
            'recipient_address'  => 'nullable|string|max:255',
            'recipient_city'     => 'nullable|string|max:100',
            'recipient_state'    => 'nullable|string|max:2',
            'recipient_zip'      => 'nullable|string|max:10',
            'payment_method'     => 'required',
            'discount'           => 'nullable|numeric|min:0',
            'shipping'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
            'due_date'           => 'nullable|date',
            'issued_at'          => 'nullable|date',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.unit'        => 'nullable|string|max:10',
            'items.*.ncm'         => 'nullable|string|max:15',
            'items.*.cfop'        => 'nullable|string|max:10',
            'items.*.icms_rate'   => 'nullable|numeric|min:0',
            'items.*.ipi_rate'    => 'nullable|numeric|min:0',
            'items.*.pis_rate'    => 'nullable|numeric|min:0',
            'items.*.cofins_rate' => 'nullable|numeric|min:0',
        ];
    }
}
