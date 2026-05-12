@extends('layouts.app')

@section('title', 'Emissão NF-e')
@section('page-title', 'Emissão de Nota Fiscal')
@section('page-subtitle', 'Preencha os dados abaixo para gerar um novo documento fiscal')

@section('content')
<form method="POST" action="{{ route('invoices.store') }}" id="invoice-form" class="anim-entrance">
    @csrf
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
                        <input type="text" value="{{ $number }}" readonly class="form-control" style="background: var(--bg-hover); font-family: monospace; font-weight: 700; opacity: 0.8;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de Operação <span style="color:var(--red);">*</span></label>
                        <select name="type" required class="form-control">
                            <option value="saida">↑ Saída (Faturamento)</option>
                            <option value="entrada">↓ Entrada (Compra)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data de Emissão</label>
                        <input type="date" name="issued_at" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vencimento</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </div>

                <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Fornecedor (opcional)</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">— Selecione se aplicável —</option>
                            @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Forma de Pagamento <span style="color:var(--red);">*</span></label>
                        <select name="payment_method" required class="form-control">
                            <option value="pix">PIX (Instantâneo)</option>
                            <option value="boleto">Boleto Bancário</option>
                            <option value="dinheiro">Dinheiro / Espécie</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
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
                <div class="grid" style="grid-template-columns: 2fr 1fr 1.5fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" required class="form-control" placeholder="Nome do cliente">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CPF / CNPJ <span style="color:var(--red);">*</span></label>
                        <input type="text" name="recipient_document" id="recipient_document" required class="form-control" placeholder="000.000.000-00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="recipient_email" id="recipient_email" class="form-control" placeholder="cliente@email.com">
                    </div>
                </div>

                <div class="grid" style="grid-template-columns: 1fr 2fr 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="recipient_address" id="recipient_address" class="form-control" placeholder="Rua, Número, Bairro">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="recipient_city" id="recipient_city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado (UF)</label>
                        <input type="text" name="recipient_state" id="recipient_state" maxlength="2" class="form-control text-center">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="recipient_zip" id="recipient_zip" class="form-control">
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

            <div class="table-wrap">
                <table style="width: 100%; border-collapse: collapse;" id="items-table">
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
            <div style="padding: 2rem; background: var(--bg-hover); display: flex; justify-content: flex-end; border-top: 1px solid var(--border);">
                <div style="width: 350px; display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                        <span style="color: var(--text-secondary);">Subtotal Produtos:</span>
                        <span id="subtotal-display" style="color: var(--text-primary); font-weight: 700;">R$ 0,00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Desconto Total (R$):</span>
                        <input type="number" name="discount" id="discount-input" step="0.01" class="form-control" style="width: 120px; text-align: right; height: 36px; padding: 0.5rem;" oninput="calcTotals()" value="0">
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Frete / Seguro (R$):</span>
                        <input type="number" name="shipping" id="shipping-input" step="0.01" class="form-control" style="width: 120px; text-align: right; height: 36px; padding: 0.5rem;" oninput="calcTotals()" value="0">
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; padding-top: 1.5rem; border-top: 2px dashed var(--border-strong); align-items: center;">
                        <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.25rem;">VALOR TOTAL:</span>
                        <span id="total-display" style="font-family: 'Outfit'; font-weight: 800; font-size: 1.75rem; color: var(--blue);">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes & Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Observações Complementares</label>
                    <textarea name="notes" rows="4" class="form-control" placeholder="Informações que sairão no corpo da NF-e..."></textarea>
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

function calcTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const disc  = parseFloat(row.querySelector('.disc-input').value) || 0;
        const total = qty * price * (1 - disc / 100);
        row.querySelector('.total-display').textContent = fmtBR(total);
        subtotal += total;
    });
    
    const discountTotal = parseFloat(document.getElementById('discount-input').value) || 0;
    const shippingTotal = parseFloat(document.getElementById('shipping-input').value) || 0;
    const grandTotal    = subtotal - discountTotal + shippingTotal;
    
    document.getElementById('subtotal-display').textContent = fmtBR(subtotal);
    document.getElementById('total-display').textContent    = fmtBR(grandTotal);
}

function addItem(data = {}) {
    const i = itemIndex++;
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    const taxRow = document.createElement('tr');
    
    row.className = 'item-row border-t border-gray-100 dark:border-slate-800';
    taxRow.className = 'tax-row bg-slate-50/50 dark:bg-slate-800/30 text-[11px] border-b border-gray-100 dark:border-slate-800';

    const selectHtml = document.getElementById('product-select-template').innerHTML;
    
    row.innerHTML = `
        <td class="px-4 py-4">
            <input type="hidden" name="items[${i}][product_id]" class="product-id-input">
            ${selectHtml.replace(/class="product-select/g, `name="items[${i}][product_id_select]" class="product-select`)}
            <input type="text" name="items[${i}][description]" required placeholder="Descrição"
                class="desc-input mt-2 form-control" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;"
                value="${data.description || ''}">
        </td>
        <td class="px-4 py-4">
            <input type="text" name="items[${i}][ncm]" class="form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;" placeholder="0000.00.00" value="${data.ncm || '0000.00.00'}">
        </td>
        <td class="px-4 py-4">
            <input type="text" name="items[${i}][cfop]" class="form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;" placeholder="5.102" value="${data.cfop || '5.102'}">
        </td>
        <td class="px-4 py-4">
            <input type="text" name="items[${i}][unit]" class="unit-input form-control text-center" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;" value="${data.unit || 'un'}">
        </td>
        <td class="px-4 py-4">
            <input type="number" name="items[${i}][quantity]" step="0.001" required
                class="qty-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;"
                oninput="calcTotals()" value="${data.quantity || 1}">
        </td>
        <td class="px-4 py-4">
            <input type="number" name="items[${i}][unit_price]" step="0.01" required
                class="price-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;"
                oninput="calcTotals()" value="${data.unit_price || 0}">
        </td>
        <td class="px-4 py-4">
            <input type="number" name="items[${i}][discount]" step="0.01"
                class="disc-input form-control text-right" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem;"
                oninput="calcTotals()" value="${data.discount || 0}">
        </td>
        <td class="px-4 py-4 text-right font-bold total-display" style="color: var(--text-primary); font-size: 0.9rem;">R$ 0,00</td>
        <td class="px-4 py-4 text-center">
            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 transition">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    taxRow.innerHTML = `
        <td colspan="3" class="px-4 py-2 text-gray-500 italic">
            <i class="fa-solid fa-calculator mr-1"></i> Composição Tributária (%)
        </td>
        <td colspan="6" class="px-4 py-2 text-right">
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
addItem();

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
