@extends('layouts.app')

@section('title', 'Emissão NF-e')
@section('page-title', 'Emissão de Nota Fiscal')
@section('page-subtitle', 'Preencha os dados abaixo para gerar um novo documento fiscal')

@section('content')
<form method="POST" action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" id="invoice-form" class="anim-entrance">
    @csrf
    @if(isset($invoice)) @method('PUT') @endif
    <div style="display:flex; flex-direction:column; gap:2rem; padding-bottom: 5rem;">

        {{-- General Info Card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--blue-bg); color:var(--blue); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Informações Gerais da NF</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Número da NF</label>
                        <input type="text" value="{{ $invoice->number ?? $number }}" readonly class="form-control" style="background: var(--bg-hover); font-family: monospace; font-weight: 700; opacity: 0.8;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de Operação <span style="color:var(--red);">*</span></label>
                        <select name="type" required class="form-control">
                            <option value="saida" {{ (isset($invoice) && $invoice->type === 'saida') ? 'selected' : '' }}>↑ Saída (Faturamento)</option>
                            <option value="entrada" {{ (isset($invoice) && $invoice->type === 'entrada') ? 'selected' : '' }}>↓ Entrada (Compra)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data de Emissão</label>
                        <input type="date" name="issued_at" value="{{ isset($invoice) ? $invoice->issued_at->format('Y-m-d') : date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vencimento</label>
                        <input type="date" name="due_date" value="{{ isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}" class="form-control">
                    </div>
                </div>

                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Fornecedor (opcional)</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">— Selecione se aplicável —</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ (isset($invoice) && $invoice->supplier_id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Forma de Pagamento <span style="color:var(--red);">*</span></label>
                        <select name="payment_method" required class="form-control">
                            @foreach(['pix' => 'PIX (Instantâneo)', 'boleto' => 'Boleto Bancário', 'dinheiro' => 'Dinheiro / Espécie', 'cartao_credito' => 'Cartão de Crédito', 'cartao_debito' => 'Cartão de Débito'] as $val => $lab)
                                <option value="{{ $val }}" {{ (isset($invoice) && $invoice->payment_method === $val) ? 'selected' : '' }}>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recipient Card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--accent-subtle); color:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Dados do Destinatário</h3>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <select id="customer-select" class="form-control" style="width: 250px; font-size: 0.8rem; height: 38px; padding: 0 1rem;" onchange="fillCustomerData(this)">
                        <option value="">Buscar Cliente Cadastrado...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-name="{{ $c->name }}" 
                                    data-document="{{ $c->document }}" 
                                    data-email="{{ $c->email }}" 
                                    data-phone="{{ $c->phone }}" 
                                    data-address="{{ $c->address }}"
                                    data-city="{{ $c->city }}"
                                    data-state="{{ $c->state }}"
                                    data-zip="{{ $c->zip_code }}">
                                {{ $c->name }} ({{ $c->document }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nome / Razão Social <span class="required-mark">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ $invoice->recipient_name ?? '' }}" required class="form-input" placeholder="Nome do cliente">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CPF / CNPJ <span class="required-mark">*</span></label>
                        <input type="text" name="recipient_document" id="recipient_document" value="{{ $invoice->recipient_document ?? '' }}" required data-mask="cpf" class="form-input" placeholder="000.000.000-00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="recipient_email" id="recipient_email" value="{{ $invoice->recipient_email ?? '' }}" class="form-input" placeholder="cliente@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ $invoice->recipient_phone ?? '' }}" data-mask="phone" class="form-input" placeholder="(00) 00000-0000">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Endereço Completo</label>
                        <input type="text" name="recipient_address" id="recipient_address" value="{{ $invoice->recipient_address ?? '' }}" class="form-input" placeholder="Rua, Número, Bairro">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="recipient_zip" id="recipient_zip" value="{{ $invoice->recipient_zip ?? '' }}" data-mask="cep" class="form-input" placeholder="00000-000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="recipient_city" id="recipient_city" value="{{ $invoice->recipient_city ?? '' }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">UF</label>
                        <input type="text" name="recipient_state" id="recipient_state" value="{{ $invoice->recipient_state ?? '' }}" maxlength="2" class="form-input text-center" placeholder="UF">
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card" style="overflow: visible;">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--blue-bg); color:var(--blue); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Itens da Nota Fiscal</h3>
                </div>
                <button type="button" onclick="addItem()" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                    <i class="fa-solid fa-plus mr-1"></i> Adicionar Item
                </button>
            </div>

            <div class="table-wrap" style="border: none; box-shadow: none; border-radius: 0;">
                <table style="width: 100%; border-collapse: collapse; min-width: 1100px;" id="items-table">
                    <thead>
                        <tr>
                            <th style="min-width: 280px; padding: 1rem;">Produto / Descrição</th>
                            <th style="width: 120px; padding: 1rem;">NCM</th>
                            <th style="width: 100px; padding: 1rem;">CFOP</th>
                            <th style="width: 70px; padding: 1rem; text-align: center;">Unid.</th>
                            <th style="width: 90px; padding: 1rem; text-align: right;">Qtde</th>
                            <th style="width: 130px; padding: 1rem; text-align: right;">Preço Unit.</th>
                            <th style="width: 90px; padding: 1rem; text-align: right;">Desc. %</th>
                            <th style="width: 140px; padding: 1rem; text-align: right;">Total</th>
                            <th style="width: 60px; padding: 1rem;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- Injected by JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Footer Summary --}}
            <div style="padding: 2.5rem; background: var(--bg-hover); display: flex; justify-content: flex-end; border-top: 1px solid var(--border);">
                <div style="width: 400px; display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem; font-weight: 500;">
                        <span style="color: var(--text-secondary);">Subtotal Produtos:</span>
                        <span id="subtotal-display" style="color: var(--text-primary); font-weight: 700;">R$ 0,00</span>
                    </div>
                    <div class="form-group" style="flex-direction: row; align-items: center; justify-content: space-between; margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 0;">Desconto Total</label>
                        <input type="text" name="discount" id="discount-input" data-mask="currency" class="form-input" style="width: 160px; text-align: right;" oninput="calcTotals()" value="0">
                    </div>
                    <div class="form-group" style="flex-direction: row; align-items: center; justify-content: space-between; margin-bottom: 0;">
                        <label class="form-label" style="margin-bottom: 0;">Frete / Seguro</label>
                        <input type="text" name="shipping" id="shipping-input" data-mask="currency" class="form-input" style="width: 160px; text-align: right;" oninput="calcTotals()" value="0">
                    </div>
                    <div style="margin-top: 0.5rem; padding-top: 1.5rem; border-top: 2px dashed var(--border-strong); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.35rem; letter-spacing: -0.02em;">VALOR TOTAL:</span>
                        <span id="total-display" style="font-family: 'Outfit'; font-weight: 800; font-size: 2rem; color: var(--accent);">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes & Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Observações Complementares</label>
                    <textarea name="notes" rows="4" class="form-control" placeholder="Informações que sairão no corpo da NF-e...">{{ $invoice->notes ?? '' }}</textarea>
                </div>
                
                <div style="display:flex; justify-content: flex-end; gap: 1.25rem; margin-top: 2.5rem;">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="action" value="save" class="btn btn-secondary">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Salvar Rascunho
                    </button>
                    <button type="submit" name="action" value="emit" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1rem;">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Emitir NF-e agora
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Product Template --}}
<template id="product-select-template">
    <select class="product-select form-control" style="font-size: 0.85rem; height: 36px; padding: 0 0.5rem;">
        <option value="">— Selecione o Produto —</option>
        @foreach($products as $p)
            <option value="{{ $p->id }}" 
                    data-price="{{ $p->unit_price }}" 
                    data-unit="{{ $p->unit }}"
                    data-name="{{ $p->name }}"
                    data-stock="{{ $p->quantity }}">
                {{ $p->name }} [Est: {{ $p->quantity }}]
            </option>
        @endforeach
    </select>
</template>

@push('scripts')
<script>
let itemIndex = 0;

function fmtBR(val) {
    return 'R$ ' + parseFloat(val || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function parseVal(val) {
    if (typeof val === 'object') val = val.value;
    if (!val) return 0;
    return parseFloat(String(val).replace(/[R$\s%._]/g, '').replace(',', '.')) || 0;
}

function calcTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseVal(row.querySelector('.qty-input'));
        const price = parseVal(row.querySelector('.price-input'));
        const disc  = parseVal(row.querySelector('.disc-input'));
        const total = qty * price * (1 - disc / 100);
        row.querySelector('.total-display').textContent = fmtBR(total);
        subtotal += total;
    });
    
    const discountTotal = parseVal(document.getElementById('discount-input'));
    const shippingTotal = parseVal(document.getElementById('shipping-input'));
    const grandTotal    = subtotal - discountTotal + shippingTotal;
    
    document.getElementById('subtotal-display').textContent = fmtBR(subtotal);
    document.getElementById('total-display').textContent    = fmtBR(grandTotal);
}

function addItem(data = {}) {
    const i = itemIndex++;
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    const taxRow = document.createElement('tr');
    
    row.className = 'item-row';
    row.style.borderTop = '1px solid var(--border)';
    taxRow.className = 'tax-row';
    taxRow.style.background = 'var(--bg-hover)';
    taxRow.style.fontSize = '11px';
    taxRow.style.borderBottom = '1px solid var(--border)';

    const selectHtml = document.getElementById('product-select-template').innerHTML;
    
    row.innerHTML = `
        <td style="padding: 1rem;">
            <input type="hidden" name="items[${i}][product_id]" class="product-id-input">
            ${selectHtml.replace(/class="product-select/g, `name="items[${i}][product_id_select]" class="product-select`)}
            <input type="text" name="items[${i}][description]" required placeholder="Descrição"
                class="desc-input mt-4 form-control" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;"
                value="${data.description || ''}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][ncm]" class="form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;" placeholder="0000.00.00" value="${data.ncm || '0000.00.00'}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][cfop]" class="form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;" placeholder="5.102" value="${data.cfop || '5.102'}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][unit]" class="unit-input form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;" value="${data.unit || 'un'}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][quantity]" step="0.001" required
                class="qty-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;"
                oninput="calcTotals()" value="${data.quantity || 1}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][unit_price]" step="0.01" required
                class="price-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;"
                oninput="calcTotals()" value="${data.unit_price || 0}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][discount]" step="0.01"
                class="disc-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%;"
                oninput="calcTotals()" value="${data.discount || 0}">
        </td>
        <td style="padding: 1rem; text-align: right; font-weight: 700; font-size: 0.9rem; color: var(--text-primary);" class="total-display">R$ 0,00</td>
        <td style="padding: 1rem; text-align: center;">
            <button type="button" onclick="removeItem(this)" style="color: var(--red); border: none; background: none; cursor: pointer; font-size: 1rem; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    taxRow.innerHTML = `
        <td colspan="3" style="padding: 0.5rem 1rem; color: var(--text-muted); font-style: italic;">
            <i class="fa-solid fa-calculator mr-1"></i> Composição Tributária (%)
        </td>
        <td colspan="6" style="padding: 0.5rem 1rem; text-align: right;">
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">ICMS:</span>
                    <input type="number" name="items[${i}][icms_rate]" step="0.01" class="form-control text-right" style="width: 65px; font-size: 11px; padding: 4px; height: auto;" value="18" oninput="calcTotals()">
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">IPI:</span>
                    <input type="number" name="items[${i}][ipi_rate]" step="0.01" class="form-control text-right" style="width: 65px; font-size: 11px; padding: 4px; height: auto;" value="0" oninput="calcTotals()">
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">PIS:</span>
                    <input type="number" name="items[${i}][pis_rate]" step="0.01" class="form-control text-right" style="width: 65px; font-size: 11px; padding: 4px; height: auto;" value="1.65" oninput="calcTotals()">
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">COF.:</span>
                    <input type="number" name="items[${i}][cofins_rate]" step="0.01" class="form-control text-right" style="width: 65px; font-size: 11px; padding: 4px; height: auto;" value="7.6" oninput="calcTotals()">
                </div>
            </div>
        </td>
    `;

    tbody.appendChild(row);
    tbody.appendChild(taxRow);

    // Bind product select
    const sel = row.querySelector('select');
    if (sel) sel.addEventListener('change', function() { fillProductData(this); });

    // Initialize masks for the new row
    if (window.initMasks) {
        window.initMasks(row);
        window.initMasks(taxRow);
    }

    calcTotals();
}

function removeItem(btn) {
    const row = btn.closest('tr');
    const taxRow = row.nextElementSibling;
    if (taxRow && taxRow.classList.contains('tax-row')) taxRow.remove();
    row.remove();
    calcTotals();
}

function fillProductData(sel) {
    const row = sel.closest('tr');
    const opt = sel.options[sel.selectedIndex];
    
    if (!opt || !opt.value) {
        row.querySelector('.product-id-input').value = '';
        return;
    }
    
    row.querySelector('.product-id-input').value = opt.value;
    row.querySelector('.desc-input').value = opt.dataset.name || '';
    row.querySelector('.price-input').value = opt.dataset.price || 0;
    row.querySelector('.unit-input').value = opt.dataset.unit || 'un';
    
    calcTotals();
}

function fillCustomerData(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    
    document.getElementById('recipient_name').value = opt.dataset.name || '';
    document.getElementById('recipient_document').value = opt.dataset.document || '';
    document.getElementById('recipient_email').value = opt.dataset.email || '';
    document.getElementById('recipient_phone').value = opt.dataset.phone || '';
    document.getElementById('recipient_address').value = opt.dataset.address || '';
    document.getElementById('recipient_city').value = opt.dataset.city || '';
    document.getElementById('recipient_state').value = opt.dataset.state || '';
    document.getElementById('recipient_zip').value = opt.dataset.zip || '';
}

// Initialize
@if(isset($invoice))
    @foreach($invoice->items as $item)
        addItem({
            product_id: "{{ $item->product_id }}",
            description: "{{ $item->description }}",
            ncm: "{{ $item->ncm }}",
            cfop: "{{ $item->cfop }}",
            unit: "{{ $item->unit }}",
            quantity: {{ $item->quantity }},
            unit_price: {{ $item->unit_price }},
            discount: {{ $item->discount }},
            icms_rate: {{ $item->icms_rate }},
            ipi_rate: {{ $item->ipi_rate }},
            pis_rate: {{ $item->pis_rate }},
            cofins_rate: {{ $item->cofins_rate }}
        });
    @endforeach
@else
    addItem();
@endif

@if(request()->has('simulate'))
setTimeout(() => {
    const custSelect = document.getElementById('customer-select');
    if (custSelect.options.length > 1) {
        custSelect.selectedIndex = 1;
        fillCustomerData(custSelect);
    }
    document.querySelector('[name="notes"]').value = "Simulação de saída de mercadoria via LogiSync WMS.";
}, 500);
@endif
</script>
@endpush
@endsection
