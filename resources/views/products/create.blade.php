@extends('layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')
@section('page-subtitle', 'Preencha os dados do produto para o catálogo')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .status-radio-label:has(input:checked) {
        background: var(--accent);
        color: white;
        box-shadow: var(--shadow-md);
    }
    .status-radio-label:not(:has(input:checked)):hover {
        background: rgba(0,0,0,0.05);
    }
    .price-calc-card {
        border: 1px solid var(--border);
        overflow: hidden;
        border-radius: var(--r-lg);
        box-shadow: var(--shadow-lg);
        margin-top: 1.5rem;
    }
    .price-pipeline-step {
        flex: 1;
        width: 100%;
    }
    @media (max-width: 850px) {
        .grid-mobile-1 { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div style="max-width:1100px; margin: 0 auto;">

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
            <span class="card-title"><i class="fa-solid fa-box"></i> Cadastro de Item</span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1.5rem;">
                @csrf

                {{-- Row 1: Basic Info & Logistics --}}
                <div class="grid grid-2 grid-mobile-1 gap-8">
                    {{-- Identity --}}
                    <div class="card p-6" style="border: 1px solid var(--border);">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-id-card-clip"></i> Identidade
                        </h3>
                        
                        <div class="form-group mb-5">
                            <label class="form-label">Nome Comercial <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Monitor Gamer UltraWide 34' Cor: Preta" required class="form-input">
                        </div>

                        <div class="grid grid-2 gap-4 mb-5">
                            <div class="form-group">
                                <label class="form-label">Cód. Barras (EAN)</label>
                                <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="789..." class="form-input" maxlength="20">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Referência / SKU</label>
                                <input type="text" name="sku" value="{{ old('sku', $nextSku) }}" class="form-input" maxlength="50" readonly style="background: var(--bg-hover); cursor: not-allowed;" title="Gerado automaticamente">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descrição Breve</label>
                            <textarea name="description" placeholder="Especificações técnicas rápidas..." rows="3" class="form-textarea" maxlength="500">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Logistics --}}
                    <div class="card p-6" style="border: 1px solid var(--border);">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-layer-group"></i> Classificação e Estoque
                        </h3>

                        <div class="grid grid-2 gap-4 mb-5">
                            <div class="form-group">
                                <label class="form-label">Categoria</label>
                                <div style="display:flex; gap:0.4rem;">
                                    <select name="category" class="form-select" style="flex:1;">
                                        <option value="">-- Selecione --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary" data-open-category-modal style="padding:0 .75rem;" title="Nova Categoria">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Unidade</label>
                                <select name="unit" class="form-select">
                                    <option value="un">UNIDADE (un)</option>
                                    <option value="pc">PEÇA (pc)</option>
                                    <option value="kg">QUILO (kg)</option>
                                    <option value="cx">CAIXA (cx)</option>
                                    <option value="m">METRO (m)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-5">
                            <label class="form-label">Endereço (WMS)</label>
                            <div style="display:flex; gap:0.4rem;">
                                <input type="hidden" name="warehouse_location_id" id="warehouse_location_id">
                                <input type="text" id="warehouse_location_display" readonly placeholder="Selecionar localização..." class="form-input" style="background:var(--bg-hover); cursor:pointer;">
                                <button type="button" id="btn-open-location-picker" data-open-location-picker class="btn btn-secondary" style="padding:0 .75rem;" title="Selecionar endereço no armazém">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-3 gap-3">
                            <div class="form-group">
                                <label class="form-label">Qtd. Inicial</label>
                                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="form-input" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estoque Mín.</label>
                                <input type="number" name="reorder_level" value="{{ old('reorder_level', 1) }}" class="form-input" min="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Estoque Máx.</label>
                                <input type="number" name="max_stock" value="{{ old('max_stock', 100) }}" class="form-input" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing Dashboard --}}
                <div class="price-calc-card">
                    <div style="background:var(--accent); color:white; padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <i class="fa-solid fa-calculator" style="font-size:1.25rem;"></i>
                            <h3 style="font-family:'Outfit'; margin:0; font-size:1.15rem; font-weight:700;">Precificação Inteligente</h3>
                        </div>
                        <div style="display:flex; gap:2rem;">
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; opacity:0.7; text-transform:uppercase;">Margem s/ Venda</div>
                                <div id="mrg_venda_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem;">0,00%</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; opacity:0.7; text-transform:uppercase;">Markup</div>
                                <div id="mrg_custo_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem;">0,00%</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:2rem; display:flex; flex-direction:column; gap:2rem;">
                        <div class="flex flex-mobile-col gap-6 items-start">
                            {{-- Investment --}}
                            <div class="price-pipeline-step">
                                <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">1</span>
                                    <span style="font-weight:700; font-size:0.8rem; text-transform:uppercase;">Compra</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="font-size:0.75rem;">Custo Unitário (R$)</label>
                                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" step="0.01" class="form-input price-calc" style="font-size:1.2rem; font-weight:700; border-color:var(--accent);">
                                </div>
                            </div>

                            <div class="hidden md:flex" style="margin-top:2.5rem; font-size:1.2rem; color:var(--border-strong);"><i class="fa-solid fa-plus"></i></div>

                            {{-- Taxes --}}
                            <div style="flex:1.5; width:100%; background:var(--bg-hover); padding:1rem; border-radius:var(--r-md);">
                                <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">2</span>
                                    <span style="font-weight:700; font-size:0.8rem; text-transform:uppercase;">Encargos / Impostos</span>
                                </div>
                                <div class="grid grid-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.7rem;">IPI (%)</label>
                                        <input type="number" name="ipi_percent" id="ipi_percent" value="{{ old('ipi_percent', 0) }}" class="form-input price-calc" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.7rem;">ICMS ST (%)</label>
                                        <input type="number" name="icms_st_percent" id="icms_st_percent" value="{{ old('icms_st_percent', 0) }}" class="form-input price-calc" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.7rem;">Frete (R$)</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" class="form-input price-calc" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.7rem; color:var(--red);">Desconto (%)</label>
                                        <input type="number" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', 0) }}" class="form-input price-calc" style="border-color:var(--red-bg);" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:flex" style="margin-top:2.5rem; font-size:1.2rem; color:var(--border-strong);"><i class="fa-solid fa-equals"></i></div>

                            {{-- Resulting Cost --}}
                            <div style="flex:1; width:100%; border:2px solid var(--accent-subtle); padding:1rem; border-radius:var(--r-md); background:white; text-align:center;">
                                <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary); justify-content:center;">
                                    <span style="background:var(--accent); color:white; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">3</span>
                                    <span style="font-weight:700; font-size:0.8rem; text-transform:uppercase;">Custo Real</span>
                                </div>
                                <div id="real_cost_display" style="font-size:1.5rem; font-weight:800; font-family:'Outfit'; color:var(--accent);">R$ 0,00</div>
                                <input type="hidden" name="cost_price" id="cost_price">
                                <div style="font-size:0.65rem; color:var(--text-muted); margin-top:0.25rem;">Custo Líquido Unitário</div>
                            </div>
                        </div>

                        <div style="padding:1.25rem; background:var(--bg-base); border-radius:var(--r-md); border:1px solid var(--border);">
                            <div class="grid grid-3 grid-mobile-1 gap-6">
                                <div class="form-group">
                                    <label class="form-label" style="font-weight:700; color:var(--blue);">Margem Lucro (%)</label>
                                    <input type="number" name="margin_percent" id="margin_percent" value="{{ old('margin_percent', 25) }}" class="form-input price-calc" style="font-size:1.1rem; font-weight:700; border-color:var(--blue);" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="font-weight:700; color:var(--green);">Preço Venda Final (R$)</label>
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" class="form-input price-calc" style="font-size:1.1rem; font-weight:800; border-color:var(--green); background:white;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="opacity:0.8;">Preço Atacado / Promo</label>
                                    <input type="number" step="0.01" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price') }}" class="form-input" placeholder="Opcional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row 3: Physical & Status --}}
                <div class="grid grid-2 grid-mobile-1 gap-8">
                    {{-- Physical Specs --}}
                    <div class="card p-6" style="border: 1px solid var(--border);">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-ruler-combined"></i> Medidas e Peso
                        </h3>
                        <div class="grid grid-4 gap-3">
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Peso (kg)</label>
                                <input type="number" name="weight" step="0.001" value="{{ old('weight', 0) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Alt (cm)</label>
                                <input type="number" name="height" step="0.1" value="{{ old('height', 0) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Larg (cm)</label>
                                <input type="number" name="width" step="0.1" value="{{ old('width', 0) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Prof (cm)</label>
                                <input type="number" name="depth" step="0.1" value="{{ old('depth', 0) }}" class="form-input">
                            </div>
                        </div>
                    </div>

                    {{-- Operations --}}
                    <div class="card p-6" style="border: 1px solid var(--border);">
                        <h3 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem;">
                            <i class="fa-solid fa-gears"></i> Fornecedor
                        </h3>
                        <div class="form-group mb-5">
                            <label class="form-label">Fornecedor Principal</label>
                            <div style="display:flex; gap:0.4rem;">
                                <select name="supplier_id" class="form-select" style="flex:1;">
                                    <option value="">-- Selecione --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-secondary" data-open-supplier-modal style="padding:0 .75rem;" title="Novo Fornecedor">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status Inicial do Produto</label>
                            <div style="display:flex; gap:1rem; padding:0.5rem; background:var(--bg-hover); border-radius:var(--r-md);">
                                <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.6rem; border-radius:var(--r-md); cursor:pointer; font-size:0.8rem; font-weight:600; transition:0.2s;" class="status-radio-label">
                                    <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }} style="display:none;">
                                    <i class="fa-solid fa-check"></i> Ativo
                                </label>
                                <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.6rem; border-radius:var(--r-md); cursor:pointer; font-size:0.8rem; font-weight:600; transition:0.2s;" class="status-radio-label">
                                    <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }} style="display:none;">
                                    <i class="fa-solid fa-xmark"></i> Inativo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:3rem; padding-right:3rem; font-weight:700;">
                        <i class="fa-solid fa-floppy-disk"></i> Cadastrar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.location_picker')
@include('partials.supplier_quick_create')
@include('partials.category_quick_create')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.price-calc');
        const purchaseInput = document.getElementById('purchase_price');
        const ipiPercentInput = document.getElementById('ipi_percent');
        const icmsStPercentInput = document.getElementById('icms_st_percent');
        const shippingInput = document.getElementById('shipping_cost');
        const discountPercentInput = document.getElementById('discount_percent');
        const marginPercentInput = document.getElementById('margin_percent');
        
        const finalPriceInput = document.getElementById('unit_price');
        const realCostDisplay = document.getElementById('real_cost_display');
        const realCostHidden = document.getElementById('cost_price');
        
        const mrgCustoBadge = document.getElementById('mrg_custo_badge');
        const mrgVendaBadge = document.getElementById('mrg_venda_badge');

        function fmt(val) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
        }

        function calculate() {
            const purchase = parseFloat(purchaseInput.value) || 0;
            const ipiP = parseFloat(ipiPercentInput.value) || 0;
            const icmsStP = parseFloat(icmsStPercentInput.value) || 0;
            const shipping = parseFloat(shippingInput.value) || 0;
            const discountP = parseFloat(discountPercentInput.value) || 0;
            const marginP = parseFloat(marginPercentInput.value) || 0;

            const ipiR = (purchase * ipiP) / 100;
            const icmsStR = (purchase * icmsStP) / 100;
            const discountR = (purchase * discountP) / 100;

            const realCost = (purchase + ipiR + icmsStR + shipping) - discountR;
            realCostDisplay.innerText = fmt(realCost);
            realCostHidden.value = realCost.toFixed(2);

            const salePrice = realCost * (1 + (marginP / 100));
            
            if (window.lastChangedByCalc !== false) {
                 finalPriceInput.value = salePrice.toFixed(2);
            }

            if(mrgCustoBadge) mrgCustoBadge.innerText = marginP.toFixed(2).replace('.', ',') + '%';
            if (salePrice > 0 && mrgVendaBadge) {
                const marginVenda = ((salePrice - realCost) / salePrice) * 100;
                mrgVendaBadge.innerText = marginVenda.toFixed(2).replace('.', ',') + '%';
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

        finalPriceInput.addEventListener('input', () => {
            window.lastChangedByCalc = false;
            const salePrice = parseFloat(finalPriceInput.value) || 0;
            const realCost = parseFloat(realCostHidden.value) || 0;
            
            if (realCost > 0) {
                const newMarginP = ((salePrice / realCost) - 1) * 100;
                marginPercentInput.value = newMarginP.toFixed(2);
                
                if(mrgCustoBadge) mrgCustoBadge.innerText = newMarginP.toFixed(2).replace('.', ',') + '%';
                if(mrgVendaBadge) {
                    const marginVenda = ((salePrice - realCost) / salePrice) * 100;
                    mrgVendaBadge.innerText = marginVenda.toFixed(2).replace('.', ',') + '%';
                }
            }
        });

        calculate();
    });
</script>
@endpush
