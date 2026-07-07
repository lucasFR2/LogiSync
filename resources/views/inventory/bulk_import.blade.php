@extends('layouts.app')

@section('title', 'Importação em Lote de NF-e')
@section('page-title', 'Importação em Lote')
@section('page-subtitle', 'Mapeie os itens da Nota Fiscal ' . $manifestation->number . ' para produtos do sistema e confirme a entrada de estoque.')

@section('content')
<div class="container-fluid">
    <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:1.5rem; gap:1rem;">
        <a href="{{ route('manifestations.show', $manifestation) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </div>

    @include('partials.alerts')

    <form action="{{ route('inventory.bulkStore', $manifestation) }}" method="POST">
        @csrf
        
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin:0;"><i class="fa-solid fa-boxes-stacked" style="color:var(--accent);"></i> Produtos a Importar</h3>
            </div>
            


            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th style="width:30%;">Descrição no XML (Fornecedor)</th>
                            <th style="width:10%;">Qtd. XML</th>
                            <th style="width:35%;">Produto Correspondente no LogiSync <span style="color:var(--red);">*</span></th>
                            <th style="width:15%;">Custo Unit. (R$) <span style="color:var(--red);">*</span></th>
                            <th style="width:10%;">Qtd. Entrada <span style="color:var(--red);">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manifestation->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div style="font-weight:600;">{{ $item->description }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">
                                        NCM: {{ $item->ncm }} | V. Unit: R$ {{ number_format($item->unit_price, 4, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-gray" style="font-size:1rem;">{{ number_format($item->quantity, 2, ',', '') }} {{ $item->unit }}</span>
                                </td>
                                <td>
                                    @if($item->product_id)
                                        <div style="display:flex; align-items:center; gap:0.5rem; color:var(--green); font-weight:600;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>{{ $item->product->name }} (Já Importado)</span>
                                        </div>
                                    @else
                                        <!-- Product link area: hidden input + display field + actions -->
                                        @php
                                            $autoMatchedProduct = null;
                                            foreach($products as $prod) {
                                                if (
                                                    stripos($item->description, $prod->name) !== false
                                                    || (!empty($item->barcode) && $item->barcode === $prod->barcode)
                                                    || (!empty($item->product_code) && $item->product_code === $prod->sku)
                                                ) {
                                                    $autoMatchedProduct = $prod;
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <!-- Hidden input that holds the selected product_id -->
                                        <input type="hidden"
                                               name="items[{{ $item->id }}][product_id]"
                                               id="product-select-{{ $item->id }}"
                                               value="{{ $autoMatchedProduct ? $autoMatchedProduct->id : '' }}">

                                        <!-- Display row: readonly field + lupa + + button -->
                                        <div style="display:flex; gap:0.5rem; align-items:center;">
                                            <div id="product-display-{{ $item->id }}"
                                                 class="form-input bulk-product-display"
                                                 style="flex:1; cursor:pointer; display:flex; align-items:center; gap:0.5rem; min-height:42px; padding:0 0.75rem; color:{{ $autoMatchedProduct ? 'var(--text-primary)' : 'var(--text-muted)' }}; user-select:none;"
                                                 data-item-id="{{ $item->id }}"
                                                 title="Clique na lupa para buscar">
                                                @if($autoMatchedProduct)
                                                    <i class="fa-solid fa-circle-check" style="color:var(--green);"></i>
                                                    <span>{{ $autoMatchedProduct->name }}</span>
                                                @else
                                                    <i class="fa-solid fa-link-slash" style="color:var(--text-muted); font-size:0.8rem;"></i>
                                                    <span style="font-style:italic;">Nenhum produto vinculado — use a lupa</span>
                                                @endif
                                            </div>

                                            <!-- Lupa: abre busca de produto -->
                                            <button type="button"
                                                    class="btn btn-secondary btn-open-bulk-product-picker"
                                                    data-item-id="{{ $item->id }}"
                                                    style="padding:0 0.75rem; height:42px; flex-shrink:0;"
                                                    title="Buscar e vincular produto existente">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </button>

                                            <!-- + Botão: cadastrar novo produto -->
                                            <button type="button"
                                                    class="btn btn-new-bulk-product"
                                                    data-item-id="{{ $item->id }}"
                                                    style="padding:0 0.75rem; height:42px; flex-shrink:0; background:var(--green); color:#fff; border:none; border-radius:var(--r-md,0.5rem);"
                                                    title="Cadastrar novo produto para este item">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Expandable panel: cadastrar novo produto -->
                                        <div id="new_product_fields_{{ $item->id }}"
                                             style="display:none; flex-direction:column; gap:0.5rem; margin-top:0.75rem; padding:0.75rem; border:1px dashed var(--green); border-radius:var(--r-md,0.5rem); background:var(--green-bg);"> 
                                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.25rem;">
                                                <span style="font-size:0.8rem; font-weight:700; color:var(--green); text-transform:uppercase; letter-spacing:0.05em;">
                                                    <i class="fa-solid fa-plus-circle"></i> Novo Produto
                                                </span>
                                                <button type="button" class="btn-cancel-new-product icon-btn" data-item-id="{{ $item->id }}" style="width:24px;height:24px;font-size:0.75rem;" title="Cancelar">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <!-- Trigger: selecting "new" sets the hidden input to "new" -->
                                            <input type="hidden" name="items[{{ $item->id }}][is_new]" id="is-new-{{ $item->id }}" value="0">
                                            <div>
                                                <label style="font-size:0.75rem; color:var(--text-muted);">Nome do Produto</label>
                                                <input type="text" name="items[{{ $item->id }}][new_name]" class="form-input" style="padding:0.5rem; font-size:0.875rem; width:100%;" value="{{ $item->description }}">
                                            </div>
                                            <div style="display:flex; gap:0.5rem;">
                                                <div style="flex:1;">
                                                    <label style="font-size:0.75rem; color:var(--text-muted);">Categoria</label>
                                                    <div style="display:flex; gap:0.25rem;">
                                                        <select name="items[{{ $item->id }}][new_category]" class="form-select category-select" style="padding:0.5rem; font-size:0.875rem; width:100%;">
                                                            <option value="">-- Sem Categoria --</option>
                                                            @foreach($categories as $cat)
                                                                @if($cat->subcategories->count() > 0)
                                                                    <optgroup label="{{ $cat->name }}">
                                                                        @foreach($cat->subcategories as $sub)
                                                                            <option value="{{ $sub->name }}">{{ $sub->name }}</option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @else
                                                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <button type="button" class="btn btn-secondary" data-open-category-modal title="Nova categoria" style="padding:0 0.5rem; height:34px;">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div style="flex:1;">
                                                    <label style="font-size:0.75rem; color:var(--text-muted);">Cód. Barras</label>
                                                    <input type="text" name="items[{{ $item->id }}][new_barcode]" class="form-input" style="padding:0.5rem; font-size:0.875rem; width:100%;" value="{{ $item->barcode }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($item->product_id)
                                        <span style="font-weight:600;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                        <input type="hidden" name="items[{{ $item->id }}][cost_price]" value="{{ $item->unit_price }}">
                                    @else
                                        <input type="number" name="items[{{ $item->id }}][cost_price]" required min="0" step="0.01" value="{{ number_format($item->unit_price, 2, '.', '') }}" class="form-input text-right">
                                    @endif
                                </td>
                                <td>
                                    @if($item->product_id)
                                        <span class="badge badge-success" style="font-size:1.1rem;">{{ number_format($item->checked_quantity ?? $item->quantity, 2, ',', '') }} {{ $item->unit }}</span>
                                    @else
                                        <input type="number" name="items[{{ $item->id }}][quantity]" required min="0" step="0.001" value="{{ $item->checked_quantity ?? $item->quantity }}" class="form-input">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:1rem;">
            <button type="submit" class="btn btn-primary" style="padding:1rem 2rem; font-size:1.1rem;">
                <i class="fa-solid fa-check"></i> Processar Entrada no Estoque
            </button>
        </div>
    </form>
</div>
@include('partials.category_quick_create')
@include('partials.product_picker')
@endsection

@push('scripts')
<script>
// ── Bulk Import: produto selecionado via lupa ────────────────────────────────
// Quando o product_picker seleciona um produto, atualiza o campo do item ativo.
// O product_picker (partials/product_picker.blade.php) usa window.activeProductSelectTarget
// para saber qual <input hidden> deve receber o ID; mas aqui também precisamos
// atualizar o display visual.

(function () {
    // Sobrescreve o handler de "Selecionar" do product_picker para bulk_import
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-select-product');
        if (!btn) return;

        // O modal picker já define window.activeProductSelectTarget (o hidden input)
        const hidden = window.activeProductSelectTarget;
        if (!hidden || !hidden.id.startsWith('product-select-')) return;

        const itemId = hidden.id.replace('product-select-', '');
        const prodId   = btn.dataset.id;
        const prodName = btn.closest('tr')?.querySelector('td:first-child div')?.textContent?.trim() || 'Produto selecionado';

        // Atualiza o hidden input
        hidden.value = prodId;

        // Atualiza o display visual
        const display = document.getElementById('product-display-' + itemId);
        if (display) {
            display.style.color = 'var(--text-primary)';
            display.innerHTML = `<i class="fa-solid fa-circle-check" style="color:var(--green);"></i><span>${prodName}</span>`;
        }

        // Garante que o painel "novo produto" está fechado e is_new = 0
        const newPanel = document.getElementById('new_product_fields_' + itemId);
        if (newPanel) newPanel.style.display = 'none';
        const isNewInput = document.getElementById('is-new-' + itemId);
        if (isNewInput) isNewInput.value = '0';
    });

    // Botão + abre o painel de cadastro
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-new-bulk-product');
        if (!btn) return;
        const itemId = btn.dataset.itemId;

        const panel = document.getElementById('new_product_fields_' + itemId);
        if (!panel) return;
        panel.style.display = 'flex';

        // Limpa vínculo atual
        const hidden = document.getElementById('product-select-' + itemId);
        if (hidden) hidden.value = 'new';

        const isNewInput = document.getElementById('is-new-' + itemId);
        if (isNewInput) isNewInput.value = '1';

        const display = document.getElementById('product-display-' + itemId);
        if (display) {
            display.style.color = 'var(--green)';
            display.innerHTML = `<i class="fa-solid fa-plus-circle" style="color:var(--green);"></i><span style="color:var(--green); font-weight:600;">Cadastrar novo produto</span>`;
        }

        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // Botão X cancela o cadastro de novo produto
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-cancel-new-product');
        if (!btn) return;
        const itemId = btn.dataset.itemId;

        const panel = document.getElementById('new_product_fields_' + itemId);
        if (panel) panel.style.display = 'none';

        const hidden = document.getElementById('product-select-' + itemId);
        if (hidden) hidden.value = '';

        const isNewInput = document.getElementById('is-new-' + itemId);
        if (isNewInput) isNewInput.value = '0';

        const display = document.getElementById('product-display-' + itemId);
        if (display) {
            display.style.color = 'var(--text-muted)';
            display.innerHTML = `<i class="fa-solid fa-link-slash" style="color:var(--text-muted); font-size:0.8rem;"></i><span style="font-style:italic;">Nenhum produto vinculado — use a lupa</span>`;
        }
    });
})();
</script>
@endpush
})();
</script>
@endpush
