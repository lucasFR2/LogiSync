@extends('layouts.app')

@section('title', 'Editar: ' . $product->name)
@section('page-title', 'Editar Produto')
@section('page-subtitle', 'Atualize as informações técnicas e comerciais do item')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="anim-entrance" style="max-width: 1100px; margin: 0 auto;">

    {{-- Error Handling --}}
    @if($errors->any())
        <div class="alert badge-danger" style="margin-bottom:2rem; padding:1.25rem; border-radius:var(--r-md); display:flex; align-items:flex-start; gap:1rem;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top:3px;"></i>
            <div>
                <div style="font-weight:700; margin-bottom:0.25rem;">Ops! Verifique os erros abaixo:</div>
                <ul style="margin:0; padding-left:1.25rem; font-size:0.9rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Formulário de Edição</h3>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                <i class="fa-solid fa-arrow-left" style="font-size:0.8rem;"></i> Voltar para Lista
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-2" style="gap:2.5rem;">
                    
                    {{-- Left Column: Basic Info --}}
                    <div style="display:flex; flex-direction:column; gap:1.75rem;">
                        <div>
                            <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-circle-info"></i> Informações Gerais
                            </h4>
                            
                            <div class="form-group mb-4">
                                <label class="form-label">Nome Comercial do Produto</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="form-input" placeholder="Ex: Monitor Gamer 27' Curvo">
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Código de Barras (EAN-13)</label>
                                <div style="position:relative;">
                                    <i class="fa-solid fa-barcode" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="form-input" style="padding-left:2.75rem;" placeholder="7890000000000">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Descrição para o Catálogo</label>
                                <textarea name="description" rows="4" class="form-textarea" placeholder="Descreva as especificações técnicas...">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>

                        <div>
                            <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-layer-group"></i> Categorização
                            </h4>
                            <div class="grid grid-2" style="gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Categoria</label>
                                    <select name="category" class="form-select">
                                        <option value="Eletrônicos" {{ $product->category == 'Eletrônicos' ? 'selected' : '' }}>Eletrônicos</option>
                                        <option value="Informática" {{ $product->category == 'Informática' ? 'selected' : '' }}>Informática</option>
                                        <option value="Periféricos" {{ $product->category == 'Periféricos' ? 'selected' : '' }}>Periféricos</option>
                                        <option value="Acessórios" {{ $product->category == 'Acessórios' ? 'selected' : '' }}>Acessórios</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Unidade</label>
                                    <select name="unit" class="form-select">
                                        <option value="un" {{ $product->unit == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                                        <option value="cx" {{ $product->unit == 'cx' ? 'selected' : '' }}>Caixa (cx)</option>
                                        <option value="kg" {{ $product->unit == 'kg' ? 'selected' : '' }}>Quilo (kg)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Commercial & Logistics --}}
                    <div style="display:flex; flex-direction:column; gap:1.75rem;">
                        <div>
                            <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-dollar-sign"></i> Precificação
                            </h4>
                            
                            <div class="card" style="background:var(--bg-hover); border:1px dashed var(--border); padding:1rem; margin-bottom:1.5rem;">
                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">Compra (R$)</label>
                                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" class="form-input price-calc" style="padding:0.5rem;">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">Frete (R$)</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', $product->shipping_cost) }}" step="0.01" class="form-input price-calc" style="padding:0.5rem;">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">Imp. (%)</label>
                                        <input type="number" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', $product->tax_percent) }}" step="0.01" class="form-input price-calc" style="padding:0.5rem;">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" style="font-size:0.75rem;">Margem (%)</label>
                                        <input type="number" name="margin_percent" id="margin_percent" value="{{ old('margin_percent', $product->margin_percent) }}" step="0.01" class="form-input price-calc" style="padding:0.5rem;">
                                    </div>
                                </div>
                                <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid var(--border); text-align:right;">
                                    <span style="font-size:0.8rem; color:var(--text-muted);">Sugerido: </span>
                                    <span id="calculated_price_display" style="font-weight:800; color:var(--accent);">R$ 0,00</span>
                                </div>
                            </div>

                            <div class="grid grid-2" style="gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Venda (R$)</label>
                                    <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ old('unit_price', $product->unit_price) }}" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Promo (R$)</label>
                                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-input">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-boxes-stacked"></i> Controle de Estoque
                            </h4>
                            <div class="grid grid-2" style="gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Qtd. Atual</label>
                                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" class="form-input" style="font-weight:700; color:var(--accent);">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nível Crítico</label>
                                    <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" class="form-input">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--accent); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-location-dot"></i> Logística
                            </h4>
                             <div class="form-group mb-4">
                                <label class="form-label">Endereço no Estoque (WMS)</label>
                                <div style="display:flex; gap:0.5rem;">
                                    <input type="hidden" name="warehouse_location_id" id="warehouse_location_id" value="{{ old('warehouse_location_id', $product->warehouse_location_id) }}">
                                    <input type="text" name="warehouse_location" id="warehouse_location_display" 
                                           value="{{ old('warehouse_location', $product->location?->full_code ?? $product->warehouse_location) }}"
                                           readonly placeholder="Selecione no mapa..." 
                                           class="form-input" style="background:var(--bg-hover); cursor:pointer;">
                                    <button type="button" id="btn-open-location-picker" class="btn btn-secondary" style="padding:0 .75rem;">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status de Operação</label>
                                <div style="display:flex; gap:1rem; padding:0.5rem; background:var(--bg-hover); border-radius:var(--r-md);">
                                    <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.75rem; border-radius:var(--r-md); cursor:pointer; font-size:0.875rem; font-weight:600; transition:0.2s;" class="status-radio-label">
                                        <input type="radio" name="status" value="ativo" {{ $product->status == 'ativo' ? 'checked' : '' }} style="display:none;">
                                        Ativo
                                    </label>
                                    <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.75rem; border-radius:var(--r-md); cursor:pointer; font-size:0.875rem; font-weight:600; transition:0.2s;" class="status-radio-label">
                                        <input type="radio" name="status" value="inativo" {{ $product->status == 'inativo' ? 'checked' : '' }} style="display:none;">
                                        Inativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:3.5rem; padding-top:2rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:1rem;">
                    <button type="reset" class="btn btn-secondary" style="padding:1rem 2rem;">Descartar Alterações</button>
                    <button type="submit" class="btn btn-primary" style="padding:1rem 2.5rem; box-shadow:var(--shadow-lg);">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .status-radio-label:has(input:checked) {
        background: var(--accent);
        color: var(--accent-fg);
        box-shadow: var(--shadow-md);
    }
    .status-radio-label:not(:has(input:checked)):hover {
        background: rgba(0,0,0,0.05);
    }
    @media (max-width: 850px) {
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endsection

@include('partials.location_picker')
@include('partials.supplier_quick_create')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price Calculation Logic
        const priceInputs = document.querySelectorAll('.price-calc');
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
        }

        priceInputs.forEach(input => {
            input.addEventListener('input', () => {
                calculate();
                const purchase = parseFloat(purchaseInput.value) || 0;
                const shipping = parseFloat(shippingInput.value) || 0;
                const tax = parseFloat(taxInput.value) || 0;
                const margin = parseFloat(marginInput.value) || 0;
                const baseCost = purchase + shipping;
                const withTax = baseCost * (1 + (tax / 100));
                const finalPrice = withTax * (1 + (margin / 100));
                
                if (finalPrice > 0) {
                    finalInput.value = finalPrice.toFixed(2);
                }
            });
        });

        calculate();
    });
</script>
@endpush
