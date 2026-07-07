@extends('layouts.app')

@section('title', 'Nova Entrada de Estoque')
@section('page-title', 'Nova Entrada de Estoque')
@section('page-subtitle', 'Registre a entrada de mercadorias no sistema')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="w-full">

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
                        <div style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="product_id" id="productSelect" required value="{{ old('product_id') }}">
                            <input type="text" id="productSelect_display" readonly class="form-input" style="flex:1; background:var(--bg-hover); cursor:pointer;" placeholder="Clique para pesquisar e selecionar o produto..." onclick="document.getElementById('btn-open-product-picker').click()">
                            <button type="button" class="btn btn-secondary" id="btn-open-product-picker" style="padding:0 0.85rem;" title="Buscar produto (lupa)">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        <select id="productSelectHidden" style="display:none;">
                            <option value="">— Selecionar produto —</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                    data-name="{{ $p->name }}"
                                    data-stock="{{ $p->quantity }}"
                                    data-unit="{{ $p->unit ?? 'un' }}"
                                    data-price="{{ number_format($p->unit_price, 2, ',', '.') }}"
                                    data-category="{{ $p->category ?? '—' }}"
                                    data-supplier="{{ $p->supplier?->name ?? '—' }}"
                                    data-location="{{ $p->location?->full_code ?? $p->warehouse_location ?? '—' }}"
                                    data-location-id="{{ $p->warehouse_location_id ?? '' }}">
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
                            <input type="datetime-local" name="entry_date" id="entry_date_input" value="{{ old('entry_date', now()->format('Y-m-d\TH:i')) }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantidade <span style="color:var(--red);">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" required class="form-input" placeholder="Ex: 100">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número do Lote</label>
                            <div style="display:flex; align-items:center; border:1px solid var(--border); border-radius:var(--r-md); overflow:hidden; background:var(--bg-hover);">
                                <span style="padding:0.75rem 1rem; font-weight:700; color:var(--text-muted); border-right:1px solid var(--border); background:var(--bg-base); font-family:monospace;">L-{{ date('Y') }}-</span>
                                <input type="text" name="lot_suffix" value="{{ old('lot_suffix', $nextSuffix) }}" maxlength="3" class="form-input" style="border:none; border-radius:0; flex:1; font-family:monospace; font-weight:700; font-size:1.1rem; letter-spacing:0.1em; background:transparent;" placeholder="001">
                            </div>
                            <small style="color:var(--text-muted);">Preencha com os últimos 3 caracteres identificadores do lote.</small>
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

            {{-- SECTION 3: Conference Verification --}}
            <div class="card anim-entrance" style="animation-delay:0.1s;">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                        <h3 style="margin:0;">3. Conferência de Mercadoria</h3>
                    </div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem;">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Quantidade Conferida <span style="color:var(--red);">*</span></label>
                            <input type="number" name="checked_quantity" value="{{ old('checked_quantity') }}" min="0" required class="form-input" placeholder="Ex: 100">
                            <small style="color:var(--text-muted);">A quantidade real contada que será adicionada ao estoque.</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status da Conferência</label>
                            <div style="padding: 0.75rem; background: var(--bg-hover); border: 1px solid var(--border); border-radius: var(--r-md); font-weight: 700; height: 42px; display: flex; align-items: center;" id="conference_status_badge">
                                <span class="text-muted">Informe as quantidades acima</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações da Conferência</label>
                        <textarea name="conference_notes" rows="2" class="form-textarea" placeholder="Descreva divergências ou observações do recebimento...">{{ old('conference_notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Localização de Armazenamento --}}
            <div class="card anim-entrance" style="animation-delay:0.15s;" id="location-card">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:10px; height:24px; background:var(--blue); border-radius:4px;"></div>
                        <h3 style="margin:0;">4. Localização de Armazenamento</h3>
                    </div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:1.25rem;">
                    <div class="form-group" style="margin:0;">
                        <label class="form-label">Localização do Produto para Entrada</label>
                        <div style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="warehouse_location_id" id="warehouse_location_id" value="">
                             <input type="text" id="warehouse_location_display" readonly class="form-input" style="flex:1; background:var(--bg-hover); cursor:pointer;" placeholder="Clique para alterar a localização do produto (Localização atual: Nenhuma)">
                            <button type="button" class="btn btn-secondary" id="btn-open-location-picker" style="padding:0 0.85rem;" title="Buscar localização">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </button>
                        </div>
                        <small style="color:var(--text-muted);">Por padrão, a entrada irá para a localização atual do produto. Clique para selecionar outra posição livre.</small>
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
@include('partials.product_picker')
@include('partials.location_picker')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel  = document.getElementById('productSelectHidden');
    const displayInp = document.getElementById('productSelect_display');
    const inputVal = document.getElementById('productSelect');
    const card = document.getElementById('productInfoCard');
    const dateInput = document.getElementById('entry_date_input');
    
    if (!sel || !card) return;

    // Atualiza a hora para o momento exato do clique/abertura se não houver valor antigo
    if (dateInput && !dateInput.defaultValue) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        dateInput.value = now.toISOString().slice(0, 16);
    }

    sel.addEventListener('change', function() {
        const opt = this.querySelector(`option[value="${this.value}"]`) || this.options[this.selectedIndex];
        if (!opt || !opt.value) { card.style.display = 'none'; resetLocationUI(); return; }

        displayInp.value = opt.dataset.name || opt.text;
        inputVal.value = opt.value;

        document.getElementById('pi_stock').textContent    = (opt.dataset.stock || '0') + ' ' + (opt.dataset.unit || 'un');
        document.getElementById('pi_price').textContent    = 'R$ ' + (opt.dataset.price || '0,00');
        document.getElementById('pi_category').textContent = opt.dataset.category || '—';
        document.getElementById('pi_supplier').textContent = opt.dataset.supplier || '—';
        document.getElementById('pi_location').textContent = opt.dataset.location || '—';
        card.style.display = 'block';

        // Pré-seleciona a localização original do produto no modal/input
        const defaultLocId = opt.dataset.locationId || '';
        const defaultLocCode = opt.dataset.location || '';
        
        const locHiddenInput = document.getElementById('warehouse_location_id');
        const locDisplayInput = document.getElementById('warehouse_location_display');
        
        if (locHiddenInput) locHiddenInput.value = defaultLocId;
        if (locDisplayInput) {
            locDisplayInput.value = defaultLocCode;
        }
    });

    // Trigger on page load if old value present
    if (inputVal && inputVal.value) {
        sel.value = inputVal.value;
        sel.dispatchEvent(new Event('change'));
    }

    // Conference Live Verification
    const qtyInput = document.querySelector('input[name="quantity"]');
    const checkedQtyInput = document.querySelector('input[name="checked_quantity"]');
    const statusBadge = document.getElementById('conference_status_badge');

    function updateConferenceStatus() {
        const qty = parseInt(qtyInput.value);
        const checkedQty = parseInt(checkedQtyInput.value);

        if (isNaN(qty) || isNaN(checkedQty)) {
            statusBadge.innerHTML = '<span style="color:var(--text-muted);"><i class="fa-solid fa-spinner"></i> Informe as quantidades</span>';
            statusBadge.style.borderColor = 'var(--border)';
            statusBadge.style.background = 'var(--bg-hover)';
            return;
        }

        if (qty === checkedQty) {
            statusBadge.innerHTML = '<span style="color:var(--green);"><i class="fa-solid fa-circle-check"></i> Sem Divergência (Confirmada)</span>';
            statusBadge.style.borderColor = 'var(--green)';
            statusBadge.style.background = 'var(--green-bg)';
        } else {
            statusBadge.innerHTML = '<span style="color:var(--orange);"><i class="fa-solid fa-triangle-exclamation"></i> Divergente</span>';
            statusBadge.style.borderColor = 'var(--orange)';
            statusBadge.style.background = 'var(--orange-bg)';
        }
    }

    if (qtyInput && checkedQtyInput && statusBadge) {
        qtyInput.addEventListener('input', updateConferenceStatus);
        checkedQtyInput.addEventListener('input', updateConferenceStatus);
    }

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const warningAlert = document.getElementById('qty-warning-alert');
            if (warningAlert && warningAlert.style.display === 'flex') {
                e.preventDefault();
                alert('Não é possível salvar: A quantidade informada excede o espaço físico disponível no endereço!');
            }
        });
    }

    // Event listener para quando o modal de localização atualizar o valor
    document.addEventListener('locationSelected', function(e) {
        const locHiddenInput = document.getElementById('warehouse_location_id');
        const locDisplayInput = document.getElementById('warehouse_location_display');
        if (locHiddenInput) locHiddenInput.value = e.detail.id;
        if (locDisplayInput) locDisplayInput.value = e.detail.full_code;
    });

    document.addEventListener('locationCleared', function() {
        resetLocationUI();
    });
});

// ── Lógica de Localização de Armazenamento ─────────────────────────────
function resetLocationUI() {
    const locHiddenInput = document.getElementById('warehouse_location_id');
    const locDisplayInput = document.getElementById('warehouse_location_display');
    
    // Volta para o padrão do produto selecionado
    const sel = document.getElementById('productSelectHidden');
    const opt = sel?.options[sel.selectedIndex];
    
    if (opt && opt.value) {
        const defaultLocCode = opt.dataset.location || '';
        if (locHiddenInput) locHiddenInput.value = opt.dataset.locationId || '';
        if (locDisplayInput) {
            locDisplayInput.value = defaultLocCode;
        }
    } else {
        if (locHiddenInput) locHiddenInput.value = '';
        if (locDisplayInput) {
            locDisplayInput.value = '';
        }
    }
}
</script>
@endpush

