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
                            <th style="width:40%;">Produto Correspondente no LogiSync <span style="color:var(--red);">*</span></th>
                            <th style="width:15%;">Qtd. Entrada <span style="color:var(--red);">*</span></th>
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
                                    <!-- Select product to map -->
                                    <div style="display:flex; gap:0.5rem; align-items:center;">
                                        <select name="items[{{ $item->id }}][product_id]" id="product-select-{{ $item->id }}" required class="form-select bulk-product-select" style="flex:1;" onchange="toggleNewProductFields({{ $item->id }}, this.value)">
                                            <option value="">-- Selecione o Produto --</option>
                                            <option value="new" style="font-weight:bold; color:var(--accent);">+ Cadastrar Automaticamente (Novo Produto)</option>
                                            @foreach($products as $prod)
                                                <!-- Simple auto-match simulation: If the XML description contains the product name -->
                                                @php
                                                    $autoMatch = stripos($item->description, $prod->name) !== false;
                                                @endphp
                                                <option value="{{ $prod->id }}" {{ $autoMatch ? 'selected' : '' }}>
                                                    {{ $prod->name }} (Estoque: {{ $prod->quantity }} {{ $prod->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-secondary btn-open-bulk-product-picker" data-item-id="{{ $item->id }}" style="padding:0 0.75rem; height:42px;" title="Buscar produto (lupa)">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>

                                    <!-- Hidden fields for new product -->
                                    <div id="new_product_fields_{{ $item->id }}" style="display:none; flex-direction:column; gap:0.5rem; margin-top:0.75rem; padding-top:0.75rem; border-top:1px dashed var(--border);">
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
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $item->id }}][quantity]" required min="0" step="0.001" value="{{ $item->checked_quantity ?? $item->quantity }}" class="form-input">
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
function toggleNewProductFields(itemId, val) {
    const fields = document.getElementById('new_product_fields_' + itemId);
    if (val === 'new') {
        fields.style.display = 'flex';
    } else {
        fields.style.display = 'none';
    }
}
</script>
@endpush
