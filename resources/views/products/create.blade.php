@extends('layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')
@section('page-subtitle', 'Preencha os dados do produto para o catálogo')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .status-radio-label:has(input:checked) {
        background: var(--accent);
        color: var(--accent-fg);
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
<div class="w-full">

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
                                            @if($cat->subcategories->count() > 0)
                                                <optgroup label="{{ $cat->name }}">
                                                    @foreach($cat->subcategories as $sub)
                                                        <option value="{{ $sub->name }}" {{ old('category') == $sub->name ? 'selected' : '' }}>
                                                            {{ $sub->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endif
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
                <div class="price-calc-card" style="background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--r-lg);">
                    <div style="background:var(--bg-hover); color:var(--text-primary); border-bottom:1px solid var(--border); padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <i class="fa-solid fa-calculator" style="font-size:1.25rem; color:var(--accent);"></i>
                            <h3 style="font-family:'Outfit'; margin:0; font-size:1.15rem; font-weight:700; color:var(--text-primary);">Precificação Inteligente</h3>
                        </div>
                        <div style="display:flex; gap:2rem;">
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:600;">Margem s/ Venda</div>
                                <div id="mrg_venda_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary);">0,00%</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:600;">Markup</div>
                                <div id="mrg_custo_badge" style="font-weight:800; font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary);">0,00%</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:2rem; display:flex; flex-direction:column; gap:2rem;">
                        <div class="flex flex-mobile-col gap-6 items-start">
                            {{-- Investment --}}
                            <div class="price-pipeline-step">
                                <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);">
                                    <span style="background:var(--accent); color:var(--accent-fg); width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">1</span>
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
                                    <span style="background:var(--accent); color:var(--accent-fg); width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">2</span>
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
                            <div style="flex:1; width:100%; border:2px solid var(--accent-subtle); padding:1rem; border-radius:var(--r-md); background:var(--bg-elevated); text-align:center;">
                                <div style="margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary); justify-content:center;">
                                    <span style="background:var(--accent); color:var(--accent-fg); width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:800;">3</span>
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
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" class="form-input price-calc" style="font-size:1.1rem; font-weight:800; border-color:var(--green);">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="opacity:0.8;">Preço Atacado / Promo</label>
                                    <input type="number" step="0.01" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price') }}" class="form-input" placeholder="Opcional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Fiscais 2026 --}}
                <div class="card p-6" style="border: 1px solid var(--border); background: var(--bg-surface);">
                    <h3 style="font-family:'Outfit'; font-size:1.15rem; color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.65rem; font-weight: 700;">
                        <i class="fa-solid fa-scale-balanced"></i> Informações Fiscais Padrão (Reforma 2026)
                    </h3>
                    
                    <div class="grid grid-3 grid-mobile-1 gap-6 mb-6">
                        <div class="form-group">
                            <label class="form-label">NCM (Nomenclatura Comum Mercosul)</label>
                            <input type="text" name="ncm" value="{{ old('ncm') }}" placeholder="Ex: 8528.52.20" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CFOP Padrão de Saída</label>
                            <input type="text" name="cfop_default" value="{{ old('cfop_default', '5.102') }}" placeholder="Ex: 5.102" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CEST (Substituição Tributária)</label>
                            <input type="text" name="cest" value="{{ old('cest') }}" placeholder="Ex: 21.002.00" class="form-input">
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <h4 style="font-family:'Outfit'; font-size:1rem; color:var(--text-secondary); margin-bottom:1rem; font-weight: 600;">Regra de ICMS & ICMS ST</h4>
                        <div class="grid grid-4 grid-mobile-1 gap-4">
                            <div class="form-group">
                                <label class="form-label">Origem do Produto</label>
                                <select name="icms_orig" class="form-select">
                                    <option value="0" {{ old('icms_orig') == 0 ? 'selected' : '' }}>0 - Nacional</option>
                                    <option value="1" {{ old('icms_orig') == 1 ? 'selected' : '' }}>1 - Estrangeira - Importação Direta</option>
                                    <option value="2" {{ old('icms_orig') == 2 ? 'selected' : '' }}>2 - Estrangeira - Adquirida no mercado interno</option>
                                    <option value="3" {{ old('icms_orig') == 3 ? 'selected' : '' }}>3 - Nacional - Conteúdo de Importação > 40%</option>
                                    <option value="4" {{ old('icms_orig') == 4 ? 'selected' : '' }}>4 - Nacional - Produção Básica</option>
                                    <option value="5" {{ old('icms_orig') == 5 ? 'selected' : '' }}>5 - Nacional - Conteúdo de Importação <= 40%</option>
                                    <option value="6" {{ old('icms_orig') == 6 ? 'selected' : '' }}>6 - Estrangeira - Importação Direta (Sem Similar)</option>
                                    <option value="7" {{ old('icms_orig') == 7 ? 'selected' : '' }}>7 - Estrangeira - Adquirida no mercado interno (Sem Similar)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">CST/CSOSN ICMS</label>
                                <select name="icms_cst" class="form-select">
                                    <option value="00" {{ old('icms_cst') == '00' ? 'selected' : '' }}>00 - Tributada integralmente</option>
                                    <option value="10" {{ old('icms_cst') == '10' ? 'selected' : '' }}>10 - Tributada e com cobrança de ST</option>
                                    <option value="20" {{ old('icms_cst') == '20' ? 'selected' : '' }}>20 - Com redução de BC</option>
                                    <option value="30" {{ old('icms_cst') == '30' ? 'selected' : '' }}>30 - Isenta ou não tributada e com cobrança de ST</option>
                                    <option value="40" {{ old('icms_cst') == '40' ? 'selected' : '' }}>40 - Isenta</option>
                                    <option value="41" {{ old('icms_cst') == '41' ? 'selected' : '' }}>41 - Não tributada</option>
                                    <option value="50" {{ old('icms_cst') == '50' ? 'selected' : '' }}>50 - Suspensão</option>
                                    <option value="51" {{ old('icms_cst') == '51' ? 'selected' : '' }}>51 - Diferimento</option>
                                    <option value="60" {{ old('icms_cst') == '60' ? 'selected' : '' }}>60 - ICMS cobrado anteriormente por ST</option>
                                    <option value="70" {{ old('icms_cst') == '70' ? 'selected' : '' }}>70 - Com redução de BC e cobrança de ST</option>
                                    <option value="90" {{ old('icms_cst') == '90' ? 'selected' : '' }}>90 - Outras</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota ICMS (%)</label>
                                <input type="number" step="0.01" name="icms_rate" value="{{ old('icms_rate', 0) }}" class="form-input" placeholder="Ex: 18">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota ICMS ST (%)</label>
                                <input type="number" step="0.01" name="icms_st_rate" value="{{ old('icms_st_rate', 0) }}" class="form-input" placeholder="Ex: 12">
                        </div>
                        <div class="grid grid-4 grid-mobile-1 gap-4 mt-4">
                            <div class="form-group">
                                <label class="form-label">Margem MVA ST (%)</label>
                                <input type="number" step="0.01" name="icms_st_mva" value="{{ old('icms_st_mva', 0) }}" class="form-input" placeholder="Ex: 40">
                            </div>
                            <div class="form-group">
                                <label class="form-label">CST ICMS ST</label>
                                <select name="icms_st_cst" class="form-select">
                                    <option value="10" {{ old('icms_st_cst') == '10' ? 'selected' : '' }}>10 - Tributada com cobrança de ST</option>
                                    <option value="30" {{ old('icms_st_cst') == '30' ? 'selected' : '' }}>30 - Isenta/Não Trib. com cobrança de ST</option>
                                    <option value="70" {{ old('icms_st_cst') == '70' ? 'selected' : '' }}>70 - Redução de BC com cobrança de ST</option>
                                    <option value="90" {{ old('icms_st_cst') == '90' ? 'selected' : '' }}>90 - Outros com cobrança de ST</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Modalidade Determinação BC</label>
                                <select name="icms_mod_bc" class="form-select">
                                    <option value="3" {{ old('icms_mod_bc') == 3 ? 'selected' : '' }}>3 - Valor da operação</option>
                                    <option value="0" {{ old('icms_mod_bc') == 0 ? 'selected' : '' }}>0 - Margem Valor Agregado (%)</option>
                                    <option value="1" {{ old('icms_mod_bc') == 1 ? 'selected' : '' }}>1 - Pauta (Valor)</option>
                                    <option value="2" {{ old('icms_mod_bc') == 2 ? 'selected' : '' }}>2 - Preço Tabelado Máx. (Valor)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Redução de BC (%)</label>
                                <input type="number" step="0.01" name="icms_red_bc" value="{{ old('icms_red_bc', 0) }}" class="form-input" placeholder="Ex: 20">
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                        <h4 style="font-family:'Outfit'; font-size:1rem; color:var(--text-secondary); margin-bottom:1rem; font-weight: 600;">PIS, COFINS, IPI & ISS</h4>
                        <div class="grid grid-4 grid-mobile-1 gap-4">
                            <div class="form-group">
                                <label class="form-label">Alíquota PIS (%)</label>
                                <input type="number" step="0.01" name="pis_rate" value="{{ old('pis_rate', 0) }}" class="form-input" placeholder="Ex: 1.65">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota COFINS (%)</label>
                                <input type="number" step="0.01" name="cofins_rate" value="{{ old('cofins_rate', 0) }}" class="form-input" placeholder="Ex: 7.6">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota IPI (%)</label>
                                <input type="number" step="0.01" name="ipi_rate" value="{{ old('ipi_rate', 0) }}" class="form-input" placeholder="Ex: 5">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota ISS (%)</label>
                                <input type="number" step="0.01" name="iss_rate" value="{{ old('iss_rate', 0) }}" class="form-input" placeholder="Ex: 3">
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                        <h4 style="font-family:'Outfit'; font-size:1rem; color:var(--text-secondary); margin-bottom:1rem; font-weight: 600;">Retenções Federais (CSLL, IRPJ, CPP)</h4>
                        <div class="grid grid-3 grid-mobile-1 gap-4">
                            <div class="form-group">
                                <label class="form-label">Alíquota CSLL (%)</label>
                                <input type="number" step="0.01" name="csll_rate" value="{{ old('csll_rate', 0) }}" class="form-input" placeholder="Ex: 9">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota IRPJ (%)</label>
                                <input type="number" step="0.01" name="irpj_rate" value="{{ old('irpj_rate', 0) }}" class="form-input" placeholder="Ex: 15">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota CPP (%)</label>
                                <input type="number" step="0.01" name="cpp_rate" value="{{ old('cpp_rate', 0) }}" class="form-input" placeholder="Ex: 20">
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                        <h4 style="font-family:'Outfit'; font-size:1rem; color:var(--text-secondary); margin-bottom:1rem; font-weight: 600;">Reforma Tributária 2026 (IBS, CBS & IS)</h4>
                        <div class="grid grid-3 grid-mobile-1 gap-4">
                            <div class="form-group">
                                <label class="form-label">Alíquota IBS (%)</label>
                                <input type="number" step="0.01" name="ibs_rate" value="{{ old('ibs_rate', 0) }}" class="form-input" placeholder="Ex: 0.1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota CBS (%)</label>
                                <input type="number" step="0.01" name="cbs_rate" value="{{ old('cbs_rate', 0) }}" class="form-input" placeholder="Ex: 0.9">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alíquota IS (Imposto Seletivo) (%)</label>
                                <input type="number" step="0.01" name="is_rate" value="{{ old('is_rate', 0) }}" class="form-input" placeholder="Ex: 1.5">
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
                                <label class="form-label" style="font-size:0.75rem;">Alt (u)</label>
                                <input type="number" name="height" id="dim_height" step="0.1" min="0.1" max="10" value="{{ old('height', 1) }}" class="form-input dim-input" placeholder="1-10">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Larg (u)</label>
                                <input type="number" name="width" id="dim_width" step="0.1" min="0.1" max="10" value="{{ old('width', 1) }}" class="form-input dim-input" placeholder="1-10">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Comp (u)</label>
                                <input type="number" name="depth" id="dim_depth" step="0.1" min="0.1" max="10" value="{{ old('depth', 1) }}" class="form-input dim-input" placeholder="1-10">
                            </div>
                        </div>
                        {{-- Volume Calc Card --}}
                        <div id="vol-calc-card" style="margin-top:0.75rem; padding:0.75rem 1rem; border-radius:var(--r-md); background:var(--blue-bg); border:1px solid rgba(59,130,246,0.3); display:flex; align-items:center; gap:1rem;">
                            <i class="fa-solid fa-cube" style="color:var(--blue); font-size:1.1rem;"></i>
                            <div style="flex:1;">
                                <div style="font-size:0.7rem; color:var(--blue); font-weight:700; text-transform:uppercase; letter-spacing:0.04em;">Volume Unitário</div>
                                <div style="font-size:1.1rem; font-weight:800; font-family:'Outfit'; color:var(--text-primary);">
                                    <span id="unit-vol-display">1,00</span> u³
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Posição 10×10×10</div>
                                <div style="font-size:0.8rem; font-weight:700; color:var(--green);">Capacidade: 1.000 u³</div>
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

        // Calculador de Volume Dimensional em tempo real
        const dimInputs = document.querySelectorAll('.dim-input');
        const unitVolDisplay = document.getElementById('unit-vol-display');

        function calcVolume() {
            const w = parseFloat(document.getElementById('dim_width')?.value)  || 1;
            const h = parseFloat(document.getElementById('dim_height')?.value) || 1;
            const d = parseFloat(document.getElementById('dim_depth')?.value)  || 1;
            const vol = (w * h * d).toFixed(4);
            if (unitVolDisplay) {
                unitVolDisplay.textContent = parseFloat(vol).toLocaleString('pt-BR', { maximumFractionDigits: 4 });
            }
        }
        dimInputs.forEach(i => i.addEventListener('input', calcVolume));
        calcVolume();
    });
</script>
@endpush
                
                if(mrgCustoBadge) mrgCustoBadge.innerText = newMarginP.toFixed(2).replace('.', ',') + '%';
                if(mrgVendaBadge) {
                    const marginVenda = ((salePrice - realCost) / salePrice) * 100;
                    mrgVendaBadge.innerText = marginVenda.toFixed(2).replace('.', ',') + '%';
                }
            }
        });

        calculate();

        // ── WMS 3-Layer Validation ──────────────────────────────────────────
        const dimInputs    = document.querySelectorAll('.dim-input');
        const unitVolDisplay = document.getElementById('unit-vol-display');
        const qtyInput     = document.querySelector('input[name="quantity"]');
        const weightInput  = document.querySelector('input[name="weight"]');

        // Localização seleccionada com dados completos
        let loc = { id: '', w: 10, h: 10, d: 10, maxWeight: null };

        function setLayer(id, state, msg, pct) {
            // state: 'idle' | 'ok' | 'fail' | 'warn'
            const el   = document.getElementById('layer-' + id);
            const icon = document.getElementById('layer-' + id + '-icon');
            const msgEl= document.getElementById('layer-' + id + '-msg');
            const pctEl= document.getElementById('layer-' + id + '-pct');
            const barEl= document.getElementById('layer-' + id + '-bar');
            if (!el) return;
            el.className = 'wms-layer wms-' + state;
            if (icon) {
                const iconMap = { idle:'<i class="fa-solid fa-ruler-combined"></i>', ok:'<i class="fa-solid fa-check"></i>', fail:'<i class="fa-solid fa-ban"></i>', warn:'<i class="fa-solid fa-triangle-exclamation"></i>' };
                icon.innerHTML = iconMap[state] || iconMap.idle;
            }
            if (msgEl && msg !== undefined) msgEl.textContent = msg;
            if (pctEl && pct !== undefined) pctEl.textContent = pct + '%';
            if (barEl && pct !== undefined) {
                const color = pct >= 100 ? 'var(--red)' : (pct >= 75 ? 'var(--orange)' : (id === 'weight' ? 'var(--blue)' : 'var(--green)'));
                barEl.style.width = Math.min(pct, 100) + '%';
                barEl.style.background = color;
            }
        }

        function runValidation() {
            const pw = parseFloat(document.getElementById('dim_width')?.value)  || 1;
            const ph = parseFloat(document.getElementById('dim_height')?.value) || 1;
            const pd = parseFloat(document.getElementById('dim_depth')?.value)  || 1;
            const qty = parseFloat(qtyInput?.value) || 0;
            const pWeight = parseFloat(weightInput?.value) || 0;

            // Unit volume
            const unitVol = pw * ph * pd;
            if (unitVolDisplay) {
                unitVolDisplay.textContent = unitVol.toLocaleString('pt-BR', { maximumFractionDigits: 4 });
            }

            // Update header labels
            const capLabel = document.getElementById('loc-capacity-label');
            const capVal   = document.getElementById('loc-capacity-val');
            if (capLabel) capLabel.textContent = `Posição ${loc.w.toFixed(1)}×${loc.h.toFixed(1)}×${loc.d.toFixed(1)}`;
            if (capVal) {
                const locVol = loc.w * loc.h * loc.d;
                capVal.textContent = `Capacidade: ${locVol.toLocaleString('pt-BR', { maximumFractionDigits: 1 })} u³`;
                capVal.style.color = 'var(--green)';
            }

            if (!loc.id) {
                // No location selected — show idle state
                setLayer('dim',    'idle', 'Selecione um endereço para verificar.');
                setLayer('vol',    'idle', 'Proporção do endereço ocupada por este produto.', 0);
                setLayer('weight', 'idle', '—', 0);
                return;
            }

            // ── Layer 1: Dimensional fit (optimal rotation) ─────────────────
            const prodDims = [pw, ph, pd].sort((a, b) => b - a);  // desc
            const locDims  = [loc.w, loc.h, loc.d].sort((a, b) => b - a);
            let dimFail = false, failIdx = -1;
            for (let i = 0; i < 3; i++) {
                if (prodDims[i] > locDims[i]) { dimFail = true; failIdx = i; break; }
            }
            if (dimFail) {
                setLayer('dim', 'fail',
                    `Produto (${prodDims.map(v=>v.toFixed(1)).join('×')} u) não cabe em nenhuma orientação. ` +
                    `Dimensão ${prodDims[failIdx].toFixed(1)} u excede ${locDims[failIdx].toFixed(1)} u da posição.`
                );
            } else {
                setLayer('dim', 'ok',
                    `Produto (${prodDims.map(v=>v.toFixed(1)).join('×')} u) cabe na posição (${locDims.map(v=>v.toFixed(1)).join('×')} u).`
                );
            }

            // ── Layer 2: Volume ─────────────────────────────────────────────
            const locVol   = loc.w * loc.h * loc.d;
            const totalVol = unitVol * qty;
            const volPct   = locVol > 0 ? Math.round((totalVol / locVol) * 100) : 0;
            if (volPct > 100) {
                setLayer('vol', 'fail',
                    `Excede capacidade: ${totalVol.toFixed(2)} u³ necessário, apenas ${locVol.toFixed(2)} u³ disponível.`,
                    volPct
                );
                if (capVal) capVal.style.color = 'var(--red)';
            } else if (volPct >= 75) {
                setLayer('vol', 'warn',
                    `Atenção: ${volPct}% da capacidade será utilizada (${totalVol.toFixed(2)} de ${locVol.toFixed(2)} u³).`,
                    volPct
                );
            } else {
                setLayer('vol', 'ok',
                    `${volPct}% da capacidade (${totalVol.toFixed(2)} u³ de ${locVol.toFixed(2)} u³).`,
                    volPct
                );
            }

            // ── Layer 3: Weight ─────────────────────────────────────────────
            const weightLayer = document.getElementById('layer-weight');
            if (loc.maxWeight && loc.maxWeight > 0) {
                if (weightLayer) weightLayer.style.display = 'flex';
                const totalWeight = pWeight * qty;
                const weightPct   = Math.round((totalWeight / loc.maxWeight) * 100);
                if (weightPct > 100) {
                    setLayer('weight', 'fail',
                        `Excede limite: ${totalWeight.toFixed(2)} kg necessário, máximo é ${loc.maxWeight.toFixed(2)} kg.`,
                        weightPct
                    );
                } else if (weightPct >= 75) {
                    setLayer('weight', 'warn',
                        `${weightPct}% da carga máxima (${totalWeight.toFixed(2)} de ${loc.maxWeight.toFixed(2)} kg).`,
                        weightPct
                    );
                } else {
                    setLayer('weight', 'ok',
                        `${totalWeight.toFixed(2)} kg de ${loc.maxWeight.toFixed(2)} kg (${weightPct}%).`,
                        weightPct
                    );
                }
            } else {
                if (weightLayer) weightLayer.style.display = 'none';
            }
        }

        dimInputs.forEach(i => i.addEventListener('input', runValidation));
        if (qtyInput)    qtyInput.addEventListener('input', runValidation);
        if (weightInput) weightInput.addEventListener('input', runValidation);

        document.addEventListener('locationSelected', function(e) {
            loc.id        = e.detail.id;
            loc.w         = parseFloat(e.detail.width)      || 10;
            loc.h         = parseFloat(e.detail.height)     || 10;
            loc.d         = parseFloat(e.detail.depth)      || 10;
            loc.maxWeight = e.detail.max_weight ? parseFloat(e.detail.max_weight) : null;
            runValidation();
        });

        document.addEventListener('locationCleared', function() {
            loc = { id: '', w: 10, h: 10, d: 10, maxWeight: null };
            runValidation();
        });

        // Block submit if any layer is failing
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!loc.id) return; // sem localização → sem restrição client-side
                const failLayers = ['dim', 'vol', 'weight'].filter(id => {
                    const el = document.getElementById('layer-' + id);
                    return el && el.classList.contains('wms-fail');
                });
                if (failLayers.length > 0) {
                    e.preventDefault();
                    const msgs = failLayers.map(id => {
                        const msgEl = document.getElementById('layer-' + id + '-msg');
                        return msgEl ? msgEl.textContent : id;
                    });
                    alert('Não é possível salvar:\n\n• ' + msgs.join('\n• '));
                }
            });
        }

        runValidation();
    });
</script>
@endpush
