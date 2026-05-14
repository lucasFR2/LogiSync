@extends('layouts.app')

@section('title', 'Emissão NF-e')
@section('page-title', 'Emissão de Nota Fiscal')
@section('page-subtitle', 'Preencha os dados abaixo para gerar um novo documento fiscal')

@section('content')
<div class="max-w-7xl mx-auto">
    <form method="POST" action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" id="invoice-form" class="anim-entrance">
        @csrf
        @if(isset($invoice)) @method('PUT') @endif
        
        <div class="flex flex-col gap-6 lg:gap-10 pb-20">

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-[1.2fr_0.8fr] gap-6 lg:gap-10 items-start">
                {{-- Destinatário e Itens (Left Column) --}}
                <div class="flex flex-col gap-6 lg:gap-10">
                    {{-- Recipient Card --}}
                    <div class="card shadow-md">
                        <div class="card-header bg-surface" style="border-bottom: 2px solid var(--accent-subtle);">
                            <div class="flex items-center gap-2">
                                <div style="width:36px; height:36px; background:var(--accent-subtle); color:var(--accent); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                                    <i class="fa-solid fa-user-tag"></i>
                                </div>
                                <h3 class="m-0" style="font-family:'Outfit'; font-weight: 800; letter-spacing: -0.01em;">Dados do Destinatário</h3>
                            </div>
                            <div class="flex gap-2 relative">
                                <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem; z-index: 1;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <select id="customer-select" class="form-control" style="width: 100%; max-width: 280px; font-size: 0.85rem; height: 42px; padding: 0 1rem 0 2.2rem; border-radius: var(--r-md); background: var(--bg-base);" onchange="fillCustomerData(this)">
                                    <option value="">Vincular Cliente...</option>
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
                        <div class="card-body p-8 sm:p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="recipient_name" id="recipient_name" value="{{ $invoice->recipient_name ?? '' }}" required class="form-control" placeholder="Ex: Lucas Ferreira Ltda" style="height: 48px; font-weight: 500;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CPF / CNPJ <span style="color:var(--red);">*</span></label>
                                    <input type="text" name="recipient_document" id="recipient_document" value="{{ $invoice->recipient_document ?? '' }}" required class="form-control" placeholder="00.000.000/0001-00" style="height: 48px;">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div class="form-group">
                                    <label class="form-label">E-mail para Faturamento</label>
                                    <input type="email" name="recipient_email" id="recipient_email" value="{{ $invoice->recipient_email ?? '' }}" class="form-control" placeholder="financeiro@empresa.com">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Telefone de Contato</label>
                                    <input type="text" name="recipient_phone" id="recipient_phone" value="{{ $invoice->recipient_phone ?? '' }}" class="form-control" placeholder="(00) 00000-0000">
                                </div>
                            </div>

                            <div class="mt-8 pt-8" style="border-top: 1px solid var(--border);">
                                <h4 class="text-xs font-bold mb-6" style="text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">Endereço de Entrega</h4>
                                <div class="grid grid-cols-1 md:grid-cols-[1.2fr_0.8fr] gap-6">
                                    <div class="form-group">
                                        <label class="form-label">Logradouro / Número</label>
                                        <input type="text" name="recipient_address" id="recipient_address" value="{{ $invoice->recipient_address ?? '' }}" class="form-control" placeholder="Rua, Número, Bairro">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">CEP</label>
                                        <input type="text" name="recipient_zip" id="recipient_zip" value="{{ $invoice->recipient_zip ?? '' }}" class="form-control" placeholder="00000-000">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-[1.2fr_0.8fr] gap-6 mt-6">
                                    <div class="form-group">
                                        <label class="form-label">Cidade</label>
                                        <input type="text" name="recipient_city" id="recipient_city" value="{{ $invoice->recipient_city ?? '' }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Estado (UF)</label>
                                        <input type="text" name="recipient_state" id="recipient_state" value="{{ $invoice->recipient_state ?? '' }}" maxlength="2" class="form-control text-center" placeholder="UF">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Protocolo (Right Column) --}}
                <div class="flex flex-col gap-6 lg:gap-10">
                    {{-- General Info Card --}}
                    <div class="card" style="border: 1px solid var(--accent-subtle);">
                        <div class="card-header" style="background: var(--accent-subtle);">
                            <div class="flex items-center gap-2">
                                <div style="width:36px; height:36px; background:var(--accent); color:var(--accent-fg); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                                <h3 class="m-0" style="font-family:'Outfit'; font-weight: 800;">Protocolo da NF</h3>
                            </div>
                            <div class="badge badge-info text-xs p-1 px-3">SÉRIE 001</div>
                        </div>
                        <div class="card-body p-8 sm:p-6">
                            <div class="form-group">
                                <label class="form-label">Número da NF (Sequencial)</label>
                                <input type="text" value="{{ $invoice->number ?? $number }}" readonly class="form-control" style="background: var(--bg-hover); font-family: 'Outfit'; font-weight: 800; font-size: 1.5rem; color: var(--accent); border-style: dashed; text-align: center;">
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4 mt-6">
                                <div class="form-group">
                                    <label class="form-label">Tipo de Operação</label>
                                    <select name="type" required class="form-control" style="font-weight: 600;">
                                        <option value="saida" {{ (isset($invoice) && $invoice->type === 'saida') ? 'selected' : '' }}>↑ Saída (Venda/Remessa)</option>
                                        <option value="entrada" {{ (isset($invoice) && $invoice->type === 'entrada') ? 'selected' : '' }}>↓ Entrada (Compra/Devolução)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Data de Emissão</label>
                                    <input type="date" name="issued_at" value="{{ isset($invoice) ? $invoice->issued_at->format('Y-m-d') : date('Y-m-d') }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Vencimento Financeiro</label>
                                    <input type="date" name="due_date" value="{{ isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Forma de Recebimento</label>
                                    <select name="payment_method" required class="form-control">
                                        @foreach(['pix' => 'PIX (Instantâneo)', 'boleto' => 'Boleto Bancário', 'dinheiro' => 'Dinheiro / Espécie', 'cartao_credito' => 'Cartão de Crédito', 'cartao_debito' => 'Cartão de Débito'] as $val => $lab)
                                            <option value="{{ $val }}" {{ (isset($invoice) && $invoice->payment_method === $val) ? 'selected' : '' }}>{{ $lab }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Fornecedor Associado (Opcional)</label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="">Nenhum fornecedor vinculado</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ (isset($invoice) && $invoice->supplier_id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Card --}}
            <div class="card shadow-lg" style="overflow: visible; border: none;">
                <div class="card-header bg-surface flex-mobile-col" style="padding: 1.5rem 2.5rem;">
                    <div class="flex items-center gap-2">
                        <div style="width:36px; height:36px; background:var(--blue-bg); color:var(--blue); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="m-0" style="font-family:'Outfit'; font-weight: 800;">Itens e Mercadorias</h3>
                    </div>
                    <button type="button" onclick="addItem()" class="btn btn-primary w-full md:w-auto" style="padding: 0.65rem 1.5rem; border-radius: var(--r-md);">
                        <i class="fa-solid fa-plus-circle mr-1"></i> Incluir Novo Item
                    </button>
                </div>

                <div style="padding: 0 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <div class="table-wrap" style="border: none; box-shadow: none; border-radius: 0; min-width: 900px;">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.5rem;" id="items-table">
                            <thead>
                                <tr style="background: transparent;">
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; width: 35%;">Produto / Descrição</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: center; width: 80px;">NCM</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: center; width: 80px;">CFOP</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: center; width: 60px;">UN</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: right; width: 80px;">Qtde</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: right; width: 110px;">Vlr Unit.</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: right; width: 80px;">Desc %</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; text-align: right; width: 130px;">Vlr Total</th>
                                    <th style="padding: 1rem; border: none; font-size: 0.7rem; width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                {{-- Injected by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer Summary --}}
                <div class="p-8 sm:p-6 bg-hover flex flex-mobile-col lg:grid lg:grid-cols-[1.2fr_0.8fr] gap-6 lg:gap-16 items-start" style="border-top: 1px solid var(--border);">
                    <div class="form-group w-full">
                        <label class="form-label" style="opacity: 0.7;">Observações Adicionais (Impressas na NF)</label>
                        <textarea name="notes" rows="4" class="form-control" style="background: var(--bg-surface); font-size: 0.9rem;" placeholder="Ex: Informações sobre tributação, dados bancários para depósito, etc.">{{ $invoice->notes ?? '' }}</textarea>
                    </div>

                    <div class="flex flex-col gap-4 w-full">
                        <div class="flex justify-between p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary);">Subtotal dos Itens:</span>
                            <span id="subtotal-display" class="font-bold" style="color: var(--text-primary);">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between items-center p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary);">Desconto Financeiro (R$):</span>
                            <input type="number" name="discount" id="discount-input" step="0.01" class="form-control" style="width: 100px; text-align: right; height: 32px; padding: 0 0.5rem; font-size: 0.85rem;" oninput="calcTotals()" value="0">
                        </div>
                        <div class="flex justify-between items-center p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary);">Frete / Encargos (R$):</span>
                            <input type="number" name="shipping" id="shipping-input" step="0.01" class="form-control" style="width: 100px; text-align: right; height: 32px; padding: 0 0.5rem; font-size: 0.85rem;" oninput="calcTotals()" value="0">
                        </div>
                        <div class="flex justify-between mt-6 p-6 bg-accent rounded-md items-center shadow-md" style="background: var(--accent); border-radius: var(--r-md); box-shadow: 0 10px 20px -5px var(--accent-glow);">
                            <span class="font-bold text-lg" style="font-family: 'Outfit'; color: var(--accent-fg);">TOTAL NF-E:</span>
                            <span id="total-display" class="font-bold text-2xl" style="font-family: 'Outfit'; color: var(--accent-fg);">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Actions --}}
            <div class="bg-surface border p-6 flex flex-mobile-col justify-between items-center gap-4 sticky bottom-6 z-10 shadow-lg rounded-lg" style="position: sticky; bottom: 1.5rem; z-index: 10; box-shadow: 0 -10px 30px -10px rgba(0,0,0,0.1); border-radius: var(--r-lg); border: 1px solid var(--border);">
                <div class="hidden md:flex gap-4">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary px-8">Descartar</a>
                </div>
                <div class="flex flex-mobile-col gap-4 w-full md:w-auto">
                    <button type="submit" name="action" value="save" class="btn btn-secondary w-full md:w-auto px-8">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Rascunho
                    </button>
                    <button type="submit" name="action" value="emit" class="btn btn-primary w-full md:w-auto px-12">
                        <i class="fa-solid fa-check-double"></i> EMITIR NOTA AGORA
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

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
    
    row.className = 'item-row';
    row.style.background = 'var(--bg-surface)';
    taxRow.className = 'tax-row';
    taxRow.style.background = 'var(--bg-hover)';
    taxRow.style.fontSize = '11px';
    taxRow.style.borderBottom = '1px solid var(--border)';

    const selectHtml = document.getElementById('product-select-template').innerHTML;
    
    row.innerHTML = `
        <td style="padding: 1rem;">
            <input type="hidden" name="items[${i}][product_id]" class="product-id-input">
            ${selectHtml.replace(/class="product-select/g, `name="items[${i}][product_id_select]" class="product-select w-full`)}
            <input type="text" name="items[${i}][description]" required placeholder="Descrição personalizada..."
                class="desc-input mt-2 form-control" style="font-size: 0.8rem; height: 32px; padding: 0.25rem 0.5rem; width: 100%; border-style: dashed;"
                value="${data.description || ''}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][ncm]" class="form-control text-center" style="font-size: 0.8rem; height: 36px; padding: 0.25rem; width: 100%;" placeholder="0000.00.00" value="${data.ncm || '0000.00.00'}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][cfop]" class="form-control text-center" style="font-size: 0.8rem; height: 36px; padding: 0.25rem; width: 100%;" placeholder="5.102" value="${data.cfop || '5.102'}">
        </td>
        <td style="padding: 1rem;">
            <input type="text" name="items[${i}][unit]" class="unit-input form-control text-center" style="font-size: 0.8rem; height: 36px; padding: 0.25rem; width: 100%; background: var(--bg-hover);" value="${data.unit || 'un'}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][quantity]" step="0.001" required
                class="qty-input form-control text-right" style="font-size: 0.85rem; height: 36px; padding: 0.25rem; width: 100%; font-weight: 600;"
                oninput="calcTotals()" value="${data.quantity || 1}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][unit_price]" step="0.01" required
                class="price-input form-control text-right" style="font-size: 0.85rem; height: 36px; padding: 0.25rem; width: 100%;"
                oninput="calcTotals()" value="${data.unit_price || 0}">
        </td>
        <td style="padding: 1rem;">
            <input type="number" name="items[${i}][discount]" step="0.01"
                class="disc-input form-control text-right" style="font-size: 0.85rem; height: 36px; padding: 0.25rem; width: 100%; color: var(--red);"
                oninput="calcTotals()" value="${data.discount || 0}">
        </td>
        <td style="padding: 1rem; text-align: right; font-weight: 700; font-size: 1rem; color: var(--text-primary);" class="total-display">R$ 0,00</td>
        <td style="padding: 1rem; text-align: center;">
            <button type="button" onclick="removeItem(this)" style="color: var(--red); border: none; background: none; cursor: pointer; font-size: 1.1rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
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
