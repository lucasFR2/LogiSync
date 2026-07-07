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
            'items.*.warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.unit'        => 'nullable|string|max:10',
            'items.*.ncm'         => 'nullable|string|max:15',
            'items.*.cfop'        => 'nullable|string|max:10',
            
            // ICMS
            'items.*.icms_cst'     => 'nullable|string|max:10',
            'items.*.icms_orig'    => 'nullable|integer',
            'items.*.icms_mod_bc'  => 'nullable|integer',
            'items.*.icms_red_bc'  => 'nullable|numeric|min:0',
            'items.*.icms_base'    => 'nullable|numeric|min:0',
            'items.*.icms_rate'    => 'nullable|numeric|min:0',
            'items.*.icms_value'   => 'nullable|numeric|min:0',

            // ICMS ST
            'items.*.icms_st_cst'  => 'nullable|string|max:10',
            'items.*.icms_st_mva'  => 'nullable|numeric|min:0',
            'items.*.icms_st_base' => 'nullable|numeric|min:0',
            'items.*.icms_st_rate' => 'nullable|numeric|min:0',
            'items.*.icms_st_value'=> 'nullable|numeric|min:0',

            // IPI
            'items.*.ipi_cst'      => 'nullable|string|max:10',
            'items.*.ipi_enq'      => 'nullable|string|max:10',
            'items.*.ipi_base'     => 'nullable|numeric|min:0',
            'items.*.ipi_rate'     => 'nullable|numeric|min:0',
            'items.*.ipi_value'    => 'nullable|numeric|min:0',

            // PIS
            'items.*.pis_cst'      => 'nullable|string|max:10',
            'items.*.pis_base'     => 'nullable|numeric|min:0',
            'items.*.pis_rate'     => 'nullable|numeric|min:0',
            'items.*.pis_value'    => 'nullable|numeric|min:0',

            // COFINS
            'items.*.cofins_cst'   => 'nullable|string|max:10',
            'items.*.cofins_base'  => 'nullable|numeric|min:0',
            'items.*.cofins_rate'  => 'nullable|numeric|min:0',
            'items.*.cofins_value' => 'nullable|numeric|min:0',

            // ISS
            'items.*.iss_cst'      => 'nullable|string|max:10',
            'items.*.iss_base'     => 'nullable|numeric|min:0',
            'items.*.iss_rate'     => 'nullable|numeric|min:0',
            'items.*.iss_value'    => 'nullable|numeric|min:0',

            // CSLL
            'items.*.csll_cst'     => 'nullable|string|max:10',
            'items.*.csll_base'    => 'nullable|numeric|min:0',
            'items.*.csll_rate'    => 'nullable|numeric|min:0',
            'items.*.csll_value'   => 'nullable|numeric|min:0',

            // IRPJ
            'items.*.irpj_cst'     => 'nullable|string|max:10',
            'items.*.irpj_base'    => 'nullable|numeric|min:0',
            'items.*.irpj_rate'    => 'nullable|numeric|min:0',
            'items.*.irpj_value'   => 'nullable|numeric|min:0',

            // CPP
            'items.*.cpp_cst'      => 'nullable|string|max:10',
            'items.*.cpp_base'     => 'nullable|numeric|min:0',
            'items.*.cpp_rate'     => 'nullable|numeric|min:0',
            'items.*.cpp_value'    => 'nullable|numeric|min:0',

            // Reforma 2026
            'items.*.ibs_cst'      => 'nullable|string|max:10',
            'items.*.ibs_base'     => 'nullable|numeric|min:0',
            'items.*.ibs_rate'     => 'nullable|numeric|min:0',
            'items.*.ibs_value'    => 'nullable|numeric|min:0',

            'items.*.cbs_cst'      => 'nullable|string|max:10',
            'items.*.cbs_base'     => 'nullable|numeric|min:0',
            'items.*.cbs_rate'     => 'nullable|numeric|min:0',
            'items.*.cbs_value'    => 'nullable|numeric|min:0',

            'items.*.is_cst'       => 'nullable|string|max:10',
            'items.*.is_base'      => 'nullable|numeric|min:0',
            'items.*.is_rate'      => 'nullable|numeric|min:0',
            'items.*.is_value'     => 'nullable|numeric|min:0',

            // Importação
            'items.*.ii_base'      => 'nullable|numeric|min:0',
            'items.*.ii_rate'      => 'nullable|numeric|min:0',
            'items.*.ii_value'     => 'nullable|numeric|min:0',
            'items.*.ii_desp'      => 'nullable|numeric|min:0',
            'items.*.ii_iof'       => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('type') === 'saida' && $this->input('action') === 'emit') {
                $items = $this->input('items', []);
                foreach ($items as $index => $item) {
                    if (!empty($item['product_id'])) {
                        $product = \App\Models\Product::find($item['product_id']);
                        if ($product) {
                            $requestedQty = (float) ($item['quantity'] ?? 0);
                            if ($requestedQty > $product->quantity) {
                                $validator->errors()->add(
                                    "items.{$index}.quantity",
                                    "Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->quantity}, Solicitado: {$requestedQty}."
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}
