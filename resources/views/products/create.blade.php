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

                {{-- Row 1: Basic Info & Categorization --}}
                <div class="grid grid-2 gap-8">
                    {{-- Left Card: Identity --}}
                    <div class="card p-6" style="border-style: solid;">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-id-card-clip"></i> Identidade do Produto
                        </h3>
                        
                        <div class="form-group mb-5">
                            <label class="form-label">Nome Comercial <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Monitor Gamer UltraWide 34'" required class="form-input">
                        </div>

                        <div class="grid grid-2 gap-4 mb-5">
                            <div class="form-group">
                                <label class="form-label">Cód. Barras (EAN)</label>
                                <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="789..." class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Referência / SKU</label>
                                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="SKU-001" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descrição Breve</label>
                            <textarea name="description" placeholder="Especificações técnicas rápidas..." rows="3" class="form-textarea">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Right Card: Logistics & Category --}}
                    <div class="card p-6" style="border-style: solid;">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-layer-group"></i> Classificação e Logística
                        </h3>

                        <div class="form-group mb-5">
                            <label class="form-label">Categoria do Produto</label>
                            <div style="display:flex; gap:0.5rem;">
                                <select name="category" class="form-select" style="flex:1;">
                                    <option value="">-- Selecione --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-secondary" data-open-category-modal style="padding:0 .75rem;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-2 gap-4 mb-5">
                            <div class="form-group">
                                <label class="form-label">Unidade Medida</label>
                                <select name="unit" class="form-select">
                                    <option value="un">UNIDADE</option>
                                    <option value="pc">PEÇA</option>
                                    <option value="kg">QUILO</option>
                                    <option value="cx">CAIXA</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Endereço (WMS)</label>
                                <div style="display:flex; gap:0.4rem;">
                                    <input type="hidden" name="warehouse_location_id" id="warehouse_location_id">
                                    <input type="text" id="warehouse_location_display" readonly placeholder="Mapa..." class="form-input" style="background:var(--bg-hover); font-size:0.85rem; cursor:pointer;">
                                    <button type="button" id="btn-open-location-picker" class="btn btn-secondary" style="padding:0 .5rem;">
                                        <i class="fa-solid fa-map"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-3 gap-3">
                            <div class="form-group">
                                <label class="form-label">Qtd. Inicial</label>
                                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estoque Mín.</label>
                                <input type="number" name="reorder_level" value="{{ old('reorder_level', 1) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estoque Máx.</label>
                                <input type="number" name="max_stock" value="{{ old('max_stock', 100) }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price Composition Dashboard --}}
                <div class="card" style="margin-top:1.5rem; border:1px solid var(--border); overflow:hidden; border-radius:var(--r-lg); box-shadow:var(--shadow-lg);">
                    <div style="background:var(--accent); color:white; padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <i class="fa-solid fa-calculator" style="font-size:1.25rem;"></i>
                            <h3 style="font-family:'Outfit'; margin:0; font-size:1.15rem; font-weight:700;">Formação de Preço Inteligente</h3>
                        </div>
                        <div style="display:flex; gap:2rem;">
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; opacity:0.7; text-transform:uppercase;">Margem s/ Venda</div>
                                <div id="mrg_venda_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem;">0.00%</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; opacity:0.7; text-transform:uppercase;">Markup</div>
                                <div id="mrg_custo_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem;">0.00%</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:2rem; display:flex; flex-direction:column; gap:2.5rem;">
                        {{-- Pipeline de Preço --}}
                        <div class="flex flex-mobile-col gap-6 items-start" style="position:relative;">
                            
                            {{-- Step 1: Purchase --}}
                            <div style="flex:1; width:100%;">
                                <div style="margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">1</span>
                                    <span style="font-weight:700; font-size:0.85rem; text-transform:uppercase;">Investimento</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="font-size:0.8rem;">Custo de Compra (R$)</label>
                                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" step="0.01" class="form-input price-calc" style="font-size:1.25rem; font-weight:700; border-color:var(--accent-glow);">
                                </div>
                            </div>

                            <div class="hidden md:flex" style="margin-top:2.5rem; font-size:1.5rem; color:var(--border-strong);"><i class="fa-solid fa-plus"></i></div>

                            {{-- Step 2: Taxes & Costs --}}
                            <div style="flex:1.5; width:100%; background:var(--bg-hover); padding:1.25rem; border-radius:var(--r-md);">
                                <div style="margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">2</span>
                                    <span style="font-weight:700; font-size:0.85rem; text-transform:uppercase;">Impostos e Encargos</span>
                                </div>
                                <div class="grid grid-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">IPI (%)</label>
                                        <input type="number" name="ipi_percent" id="ipi_percent" value="{{ old('ipi_percent', 0) }}" class="form-input price-calc">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">ICMS ST (%)</label>
                                        <input type="number" name="icms_st_percent" id="icms_st_percent" value="{{ old('icms_st_percent', 0) }}" class="form-input price-calc">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">Frete (R$)</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" class="form-input price-calc">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem; color:var(--red);">Desconto (%)</label>
                                        <input type="number" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', 0) }}" class="form-input price-calc" style="border-color:var(--red-bg);">
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:flex" style="margin-top:2.5rem; font-size:1.5rem; color:var(--border-strong);"><i class="fa-solid fa-equals"></i></div>

                            {{-- Step 3: Result --}}
                            <div style="flex:1; width:100%; border:2px solid var(--accent-subtle); padding:1.25rem; border-radius:var(--r-md); background:white;">
                                <div style="margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">3</span>
                                    <span style="font-weight:700; font-size:0.85rem; text-transform:uppercase;">Custo Real</span>
                                </div>
                                <div style="text-align:center;">
                                    <div id="real_cost_display" style="font-size:2rem; font-weight:800; font-family:'Outfit'; color:var(--accent);">R$ 0,00</div>
                                    <input type="hidden" name="cost_price" id="cost_price">
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.5rem;">Custo Líquido Unitário</div>
                                </div>
                            </div>
                        </div>

                        {{-- Final Sale Definition --}}
                        <div style="padding:1.5rem; background:var(--bg-base); border-radius:var(--r-md); border:1px solid var(--border); display:flex; flex-direction:column; gap:1.5rem;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h4 style="font-family:'Outfit'; margin:0; display:flex; align-items:center; gap:0.5rem;">
                                    <i class="fa-solid fa-tags" style="color:var(--accent);"></i> Definição de Venda
                                </h4>
                            </div>

                            <div class="grid grid-3 gap-6">
                                <div class="form-group">
                                    <label class="form-label" style="font-weight:700; color:var(--blue);">Margem Lucro (%)</label>
                                    <input type="number" name="margin_percent" id="margin_percent" value="{{ old('margin_percent', 25) }}" class="form-input price-calc" style="font-size:1.1rem; font-weight:700; border-color:var(--blue);">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="font-weight:700; color:var(--green);">Preço Venda Final (R$)</label>
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" class="form-input price-calc" style="font-size:1.1rem; font-weight:800; border-color:var(--green); background:white;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="opacity:0.7;">Preço Atacado / Promo</label>
                                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" class="form-input" placeholder="Opcional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dimensions & Details --}}
                <div style="margin-top:1.5rem;">
                    <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-bottom:1.5rem;">
                        <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                            <i class="fa-solid fa-ruler-combined" style="margin-right:.5rem;color:var(--purple);"></i>Especificações Físicas
                        </h3>
                    </div>
                    <div class="grid grid-4 gap-4">
                        <div class="form-group">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" name="weight" step="0.001" value="{{ old('weight', 0) }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Altura (cm)</label>
                            <input type="number" name="height" step="0.1" value="{{ old('height', 0) }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Largura (cm)</label>
                            <input type="number" name="width" step="0.1" value="{{ old('width', 0) }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Profundidade (cm)</label>
                            <input type="number" name="depth" step="0.1" value="{{ old('depth', 0) }}" class="form-input">
                        </div>
                    </div>
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
                        <div style="display:flex; gap:0.5rem;">
                            <select name="category" id="category" class="form-select" style="flex:1;">
                                <option value="">-- Selecione uma categoria --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-secondary" data-open-category-modal title="Nova categoria">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
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
@include('partials.category_quick_create')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.price-calc');
        const purchaseInput = document.getElementById('purchase_price');
        const ipiPercentInput = document.getElementById('ipi_percent');
        const icmsStPercentInput = document.getElementById('icms_st_percent');
        const otherTaxesPercentInput = document.getElementById('other_taxes_percent');
        const shippingInput = document.getElementById('shipping_cost');
        const otherCostsInput = document.getElementById('other_costs');
        const discountPercentInput = document.getElementById('discount_percent');
        const marginPercentInput = document.getElementById('margin_percent');
        
        const finalPriceInput = document.getElementById('unit_price');
        const installmentPriceInput = document.getElementById('selling_price');
        const wholesalePriceInput = document.getElementById('wholesale_price');
        
        const realCostDisplay = document.getElementById('real_cost_display');
        const realCostHidden = document.getElementById('cost_price');
        
        const ipiReais = document.getElementById('ipi_reais');
        const icmsStReais = document.getElementById('icms_st_reais');
        const discountReais = document.getElementById('discount_reais');
        
        const mrgCustoBadge = document.getElementById('mrg_custo_badge');
        const mrgVendaBadge = document.getElementById('mrg_venda_badge');

        function fmt(val) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
        }

        function parseVal(el) {
            let val = el.value || '0';
            // Remove R$, %, dots (thousands) and replace comma with dot
            val = val.replace(/[R$\s%._]/g, '').replace(',', '.');
            return parseFloat(val) || 0;
        }

        function calculate() {
            const purchase = parseFloat(purchaseInput.value) || 0;
            const ipiP = parseFloat(ipiPercentInput.value) || 0;
            const icmsStP = parseFloat(icmsStPercentInput.value) || 0;
            const otherTaxesP = parseFloat(otherTaxesPercentInput.value) || 0;
            const shipping = parseFloat(shippingInput.value) || 0;
            const otherCosts = parseFloat(otherCostsInput.value) || 0;
            const discountP = parseFloat(discountPercentInput.value) || 0;
            const marginP = parseFloat(marginPercentInput.value) || 0;

            // Intermediate values
            const ipiR = (purchase * ipiP) / 100;
            const icmsStR = (purchase * icmsStP) / 100;
            const otherTaxesR = (purchase * otherTaxesP) / 100;
            const discountR = (purchase * discountP) / 100;

            // Display intermediate
            if(ipiReais) ipiReais.value = ipiR.toFixed(2);
            if(icmsStReais) icmsStReais.value = icmsStR.toFixed(2);
            if(discountReais) discountReais.value = discountR.toFixed(2);

            // Real Cost calculation
            const realCost = (purchase + ipiR + icmsStR + otherTaxesR + shipping + otherCosts) - discountR;
            realCostDisplay.innerText = fmt(realCost);
            realCostHidden.value = realCost.toFixed(2);

            // Sale Price calculation
            const salePrice = realCost * (1 + (marginP / 100));
            
            // Only update inputs if they are changed via calculation flow
            if (window.lastChangedByCalc === true || typeof window.lastChangedByCalc === 'undefined') {
                 finalPriceInput.value = salePrice.toFixed(2);
                 // Default values for other prices
                 installmentPriceInput.value = (salePrice * 1.05).toFixed(2);
                 wholesalePriceInput.value = (salePrice * 0.90).toFixed(2);
            }

            // Margin Badges
            if(mrgCustoBadge) mrgCustoBadge.innerText = marginP.toFixed(2) + '%';
            if (salePrice > 0 && mrgVendaBadge) {
                const marginVenda = ((salePrice - realCost) / salePrice) * 100;
                mrgVendaBadge.innerText = marginVenda.toFixed(2) + '%';
            } else if(mrgVendaBadge) {
                mrgVendaBadge.innerText = '0,00%';
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                window.lastChangedByCalc = true;
                calculate();
            });
        });

        // If user manually changes the final price, recalculate margin
        finalPriceInput.addEventListener('input', () => {
            window.lastChangedByCalc = false;
            const salePrice = parseFloat(finalPriceInput.value) || 0;
            const realCost = parseFloat(realCostHidden.value) || 0;
            
            if (realCost > 0) {
                const newMarginP = ((salePrice / realCost) - 1) * 100;
                marginPercentInput.value = newMarginP.toFixed(2);
                
                if(mrgCustoBadge) mrgCustoBadge.innerText = newMarginP.toFixed(2) + '%';
                if(mrgVendaBadge) {
                    const marginVenda = ((salePrice - realCost) / salePrice) * 100;
                    mrgVendaBadge.innerText = marginVenda.toFixed(2) + '%';
                }
            }
        });

        calculate();
    });
</script>
@endpush
