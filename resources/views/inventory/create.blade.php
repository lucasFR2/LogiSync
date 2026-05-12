@extends('layouts.app')

@section('title', 'Nova Entrada de Estoque')
@section('page-title', 'Nova Entrada de Estoque')
@section('page-subtitle', 'Registre a entrada de mercadorias no sistema')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div style="max-width:860px;">

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.5rem;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top:3px;"></i>
            <div>
                <div style="font-weight:700; margin-bottom:0.25rem;">Verifique os erros abaixo:</div>
                <ul style="margin:0; padding-left:1.25rem; font-size:0.9rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf

        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- SECTION 1: Product Selection --}}
            <div class="card anim-entrance">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                        <h3 style="margin:0;">1. Selecionar Produto</h3>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Produto <span style="color:var(--red);">*</span></label>
                        <select name="product_id" id="productSelect" required class="form-select" style="width:100%;">
                            <option value="">— Selecionar produto —</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                    data-stock="{{ $p->quantity }}"
                                    data-unit="{{ $p->unit ?? 'un' }}"
                                    data-price="{{ number_format($p->unit_price, 2, ',', '.') }}"
                                    data-category="{{ $p->category ?? '—' }}"
                                    data-supplier="{{ $p->supplier?->name ?? '—' }}"
                                    data-location="{{ $p->location?->full_code ?? $p->warehouse_location ?? '—' }}"
                                    {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->barcode ?? 'Sem Código' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product Info Card (JS populated) --}}
                    <div id="productInfoCard" style="display:none; margin-top:1.25rem; padding:1.25rem; background:var(--bg-hover); border:1px solid var(--border); border-radius:var(--r-md);">
                        <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin-bottom:0.75rem;">
                            <i class="fa-solid fa-circle-info"></i> Dados Atuais do Produto
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(120px, 1fr)); gap:1rem;">
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Estoque Atual</div>
                                <div style="font-weight:800; font-size:1.1rem; color:var(--text-primary);" id="pi_stock">—</div>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Preço Unitário</div>
                                <div style="font-weight:700; color:var(--text-primary);" id="pi_price">—</div>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Categoria</div>
                                <div style="color:var(--text-secondary);" id="pi_category">—</div>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Fornecedor</div>
                                <div style="color:var(--text-secondary);" id="pi_supplier">—</div>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">Localização</div>
                                <div style="font-family:monospace; color:var(--text-secondary);" id="pi_location">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Supplier & Entry Details --}}
            <div class="card anim-entrance" style="animation-delay:0.05s;">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                        <h3 style="margin:0;">2. Dados da Movimentação</h3>
                    </div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem;">
                    {{-- Supplier --}}
                    <div class="form-group">
                        <label class="form-label">Fornecedor da Entrada</label>
                        <div style="display:flex; gap:0.5rem;">
                            <select name="supplier_id" class="form-select" style="flex:1;">
                                <option value="">— Mesmo do produto —</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-secondary" data-open-supplier-modal title="Cadastrar fornecedor">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <small style="color:var(--text-muted);">Deixe vazio para usar o fornecedor padrão do produto.</small>
                    </div>

                    {{-- Details Grid --}}
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Data e Hora <span style="color:var(--red);">*</span></label>
                            <input type="datetime-local" name="entry_date" value="{{ old('entry_date', now()->format('Y-m-d\TH:i')) }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantidade <span style="color:var(--red);">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required class="form-input" placeholder="Ex: 100">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número do Lote</label>
                            <input type="text" name="lot_number" value="{{ old('lot_number') }}" class="form-input" placeholder="Ex: L-2024-001">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Data de Validade</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Observações / Nota Fiscal</label>
                        <textarea name="notes" rows="3" class="form-textarea" placeholder="Descreva os detalhes desta movimentação...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:0.5rem;">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                    <i class="fa-solid fa-check"></i> Confirmar Entrada
                </button>
            </div>

        </div>
    </form>
</div>

@include('partials.supplier_quick_create')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel  = document.getElementById('productSelect');
    const card = document.getElementById('productInfoCard');
    if (!sel || !card) return;

    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) { card.style.display = 'none'; return; }

        document.getElementById('pi_stock').textContent    = opt.dataset.stock + ' ' + opt.dataset.unit;
        document.getElementById('pi_price').textContent    = 'R$ ' + opt.dataset.price;
        document.getElementById('pi_category').textContent = opt.dataset.category;
        document.getElementById('pi_supplier').textContent = opt.dataset.supplier;
        document.getElementById('pi_location').textContent = opt.dataset.location;
        card.style.display = 'block';
    });

    // Trigger on page load if old value present
    if (sel.value) sel.dispatchEvent(new Event('change'));
});
</script>
@endpush
