@extends('layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')
@section('page-subtitle', 'Preencha os dados do produto')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div style="max-width:960px;">

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        </div>
    @endif

    <div class="card anim-fade-up">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-box"></i> Dados do Produto</span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf

                {{-- Informações Básicas --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-bottom:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-info-circle" style="margin-right:.5rem;color:var(--accent);"></i>Informações Básicas
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Nome do Produto <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Ex: Notebook Dell Inspiron 15" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código de Barras</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}"
                               placeholder="Ex: 1234567890123" pattern="[0-9]{1,13}" maxlength="13" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Descrição Detalhada</label>
                        <textarea name="description" placeholder="Descreva as características..." rows="3" class="form-textarea">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Preços e Composição --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-calculator" style="margin-right:.5rem;color:var(--accent);"></i>Composição de Preço
                    </h3>
                </div>

                <div class="card" style="background:var(--bg-hover); border:1px dashed var(--border); padding:1.5rem;">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1.25rem;">
                        <div class="form-group">
                            <label class="form-label">Preço de Compra (R$)</label>
                            <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Frete / Encargos (R$)</label>
                            <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Impostos (%)</label>
                            <input type="number" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Margem de Lucro (%)</label>
                            <input type="number" name="margin_percent" id="margin_percent" value="{{ old('margin_percent', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                    </div>
                    <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-weight:600; color:var(--text-secondary);">Preço Final Calculado:</span>
                        <span id="calculated_price_display" style="font-size:1.5rem; font-weight:800; color:var(--accent); font-family:'Outfit';">R$ 0,00</span>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Preço de Venda Final (R$) <span style="color:var(--red);">*</span></label>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}"
                               placeholder="0.00" step="0.01" min="0" required class="form-input">
                        <small style="color:var(--text-muted);">Este é o preço que será usado no sistema.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Especial / Promoção (R$)</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantidade Inicial em Estoque</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}"
                               placeholder="0" step="1" min="0" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nível de Ressuprimento <span style="color:var(--red);">*</span></label>
                        <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}"
                               placeholder="0" step="1" min="0" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estoque Máximo Sugerido</label>
                        <input type="number" name="max_stock" value="{{ old('max_stock', 1) }}"
                               placeholder="1" step="1" min="1" class="form-input">
                    </div>
                </div>

                {{-- Dimensões e Peso --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-ruler-combined" style="margin-right:.5rem;color:var(--purple);"></i>Dimensões e Peso
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" name="weight" value="{{ old('weight') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Altura (cm)</label>
                        <input type="number" name="height" value="{{ old('height') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Largura (cm)</label>
                        <input type="number" name="width" value="{{ old('width') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profundidade (cm)</label>
                        <input type="number" name="depth" value="{{ old('depth') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                </div>

                {{-- Categorização e Localização --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-folder-open" style="margin-right:.5rem;color:var(--orange);"></i>Categorização e Localização
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">-- Selecione uma categoria --</option>
                            @if(isset($categories) && $categories->count())
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Eletrônicos" {{ old('category') == 'Eletrônicos' ? 'selected' : '' }}>Eletrônicos</option>
                                <option value="Informática" {{ old('category') == 'Informática' ? 'selected' : '' }}>Informática</option>
                                <option value="Periféricos" {{ old('category') == 'Periféricos' ? 'selected' : '' }}>Periféricos</option>
                                <option value="Acessórios" {{ old('category') == 'Acessórios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="Software" {{ old('category') == 'Software' ? 'selected' : '' }}>Software</option>
                                <option value="Outros" {{ old('category') == 'Outros' ? 'selected' : '' }}>Outros</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unidade de Medida</label>
                        <select name="unit" id="unit" class="form-select">
                            <option value="un" {{ old('unit') == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                            <option value="caixa" {{ old('unit') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                            <option value="dúzia" {{ old('unit') == 'dúzia' ? 'selected' : '' }}>Dúzia</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Quilograma (kg)</option>
                            <option value="l" {{ old('unit') == 'l' ? 'selected' : '' }}>Litro (l)</option>
                            <option value="m" {{ old('unit') == 'm' ? 'selected' : '' }}>Metro (m)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Endereço no Estoque (WMS)</label>
                        <div style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="warehouse_location_id" id="warehouse_location_id" value="{{ old('warehouse_location_id') }}">
                            <input type="text" name="warehouse_location" id="warehouse_location_display" 
                                   value="{{ old('warehouse_location') }}"
                                   readonly placeholder="Selecione no mapa..." 
                                   class="form-input" style="background:var(--bg-hover); cursor:pointer;">
                            <button type="button" id="btn-open-location-picker" class="btn btn-secondary" style="padding:0 .75rem;">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </button>
                        </div>
                        <small style="color:var(--text-muted);">Clique para selecionar a posição exata.</small>
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Fornecedor Principal</label>
                        <div style="display:flex; gap:0.5rem;">
                            <select name="supplier_id" id="supplier_id" class="form-select" style="flex:1;">
                                <option value="">-- Selecione um fornecedor --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-secondary" data-open-supplier-modal title="Cadastrar fornecedor">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Status do Produto</label>
                        <div style="display:flex;gap:1.5rem;align-items:center;">
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }}>
                                Ativo
                            </label>
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }}>
                                Inativo
                            </label>
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="descontinuado" {{ old('status') == 'descontinuado' ? 'checked' : '' }}>
                                Descontinuado
                            </label>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--border);margin-top:.5rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('partials.location_picker')
@include('partials.supplier_quick_create')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price Calculation Logic
        const inputs = document.querySelectorAll('.price-calc');
        const purchaseInput = document.getElementById('purchase_price');
        const shippingInput = document.getElementById('shipping_cost');
        const taxInput = document.getElementById('tax_percent');
        const marginInput = document.getElementById('margin_percent');
        const finalInput = document.getElementById('unit_price');
        const display = document.getElementById('calculated_price_display');

        function calculate() {
            const purchase = parseFloat(purchaseInput.value) || 0;
            const shipping = parseFloat(shippingInput.value) || 0;
            const tax = parseFloat(taxInput.value) || 0;
            const margin = parseFloat(marginInput.value) || 0;

            const baseCost = purchase + shipping;
            const withTax = baseCost * (1 + (tax / 100));
            const finalPrice = withTax * (1 + (margin / 100));

            display.innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(finalPrice);
            
            if (finalPrice > 0) {
                finalInput.value = finalPrice.toFixed(2);
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculate);
        });

        calculate();
    });
</script>
@endpush
