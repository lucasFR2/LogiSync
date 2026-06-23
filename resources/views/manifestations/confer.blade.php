@extends('layouts.app')

@section('title', 'Conferência de Recebimento')
@section('page-title', 'Conferência de Recebimento')
@section('page-subtitle', 'NF-e #' . $manifestation->number . ' • ' . $manifestation->supplier_name)

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    {{-- Header Bar --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="{{ route('manifestations.show', $manifestation) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left mr-2"></i> Voltar
            </a>
            <div id="global-status-badge" class="badge" style="background:var(--orange-bg); color:var(--orange); padding:0.6rem 1.2rem; font-size:0.95rem; font-weight:800;">
                <i class="fa-solid fa-clock mr-2"></i> <span id="global-status-text">Aguardando Conferência</span>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
            <div id="progress-counter" style="font-family:'Outfit'; font-size:1.1rem; font-weight:700; color:var(--text-secondary);">
                <span id="items-checked-count">0</span> / <span id="items-total-count">{{ $manifestation->items->count() }}</span> itens conferidos
            </div>
            <button type="button" onclick="submitConference()" id="btn-finalize" class="btn btn-primary" style="background:var(--green); border-color:var(--green); box-shadow:0 8px 20px -6px rgba(52,199,89,0.4); padding:0.75rem 2rem; font-size:1rem; font-weight:700;" disabled>
                <i class="fa-solid fa-check-double mr-2"></i> Finalizar Conferência
            </button>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid" style="grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;">
        <div class="card" style="border-left:4px solid var(--blue);">
            <div class="card-body" style="padding:1.25rem;">
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">Fornecedor</div>
                <div style="font-weight:700; color:var(--text-primary); font-size:1.05rem;">{{ $manifestation->supplier_name }}</div>
                <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:0.15rem;">CNPJ: {{ $manifestation->supplier_cnpj }}</div>
            </div>
        </div>
        <div class="card" style="border-left:4px solid var(--accent);">
            <div class="card-body" style="padding:1.25rem;">
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">Chave de Acesso</div>
                <div style="font-weight:600; color:var(--text-primary); font-size:0.85rem; font-family:monospace; word-break:break-all;">{{ $manifestation->access_key }}</div>
            </div>
        </div>
        <div class="card" style="border-left:4px solid var(--green);">
            <div class="card-body" style="padding:1.25rem;">
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem;">Valor Total</div>
                <div style="font-weight:800; color:var(--accent); font-size:1.5rem; font-family:'Outfit';">R$ {{ number_format($manifestation->total_amount, 2, ',', '.') }}</div>
                <div style="font-size:0.8rem; color:var(--text-secondary);">{{ $manifestation->items->count() }} itens • Emissão: {{ $manifestation->emission_date->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Barcode Scanner Input --}}
    <div class="card" id="scanner-card" style="border:2px solid var(--accent); position:relative; overflow:hidden;">
        <div style="position:absolute; inset:0; background:linear-gradient(135deg, rgba(99,102,241,0.03) 0%, transparent 50%); pointer-events:none;"></div>
        <div class="card-body" style="padding:1.5rem; display:flex; align-items:center; gap:1.5rem;">
            <div style="width:56px; height:56px; background:var(--accent-subtle); color:var(--accent); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; animation:pulse-glow 2s ease-in-out infinite;">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <div style="flex:1;">
                <label for="barcode-input" style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.35rem; display:block;">Bipar Código de Barras (EAN)</label>
                <input type="text" id="barcode-input" class="form-input" autofocus autocomplete="off"
                       placeholder="Escaneie ou digite o código de barras e pressione Enter..."
                       style="font-size:1.2rem; padding:0.9rem 1.2rem; background:var(--bg-base); border:2px solid var(--accent-subtle); font-family:monospace; font-weight:700; letter-spacing:0.1em; transition:all 0.3s ease;">
            </div>
            <div id="scan-feedback" style="min-width:200px; text-align:center; display:none;">
                <div id="scan-feedback-icon" style="font-size:2.5rem; margin-bottom:0.25rem;"></div>
                <div id="scan-feedback-text" style="font-size:0.85rem; font-weight:700;"></div>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <form id="conference-form" action="{{ route('manifestations.confer-save', $manifestation) }}" method="POST">
        @csrf
        <div class="card" style="padding:0; overflow:hidden;">
            <div class="card-header" style="background:var(--bg-hover);">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--blue-bg); color:var(--blue); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Itens para Conferência</h3>
                </div>
                <div style="display:flex; gap:0.75rem; align-items:center;">
                    <div class="badge" style="background:var(--bg-hover); color:var(--text-primary); font-weight:700;">{{ $manifestation->items->count() }} ITENS</div>
                    <button type="button" onclick="resetAllChecked()" class="btn btn-secondary btn-sm" style="font-size:0.75rem; padding:0.35rem 0.75rem;">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Resetar
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center; padding:1rem;">#</th>
                            <th style="padding:1rem;">Produto</th>
                            <th style="text-align:center; padding:1rem;">Código de Barras</th>
                            <th style="text-align:right; padding:1rem;">Qtd. Nota</th>
                            <th style="text-align:center; padding:1rem; width:180px;">Qtd. Conferida</th>
                            <th style="text-align:center; padding:1rem; width:140px;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody">
                        @foreach($manifestation->items as $idx => $item)
                            <tr id="item-row-{{ $item->id }}"
                                data-item-id="{{ $item->id }}"
                                data-barcode="{{ $item->barcode }}"
                                data-expected="{{ $item->quantity }}"
                                class="confer-row"
                                style="border-bottom:1px solid var(--border); transition:all 0.4s ease;">
                                <td style="text-align:center; color:var(--text-muted); font-size:0.8rem; padding:1rem;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="padding:1rem;">
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $item->description }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">NCM: {{ $item->ncm }} • CFOP: {{ $item->cfop }} • {{ $item->unit }}</div>
                                </td>
                                <td style="text-align:center; padding:1rem;">
                                    <span style="font-family:monospace; font-size:0.85rem; background:var(--bg-hover); padding:0.3rem 0.6rem; border-radius:6px; color:var(--text-secondary); font-weight:600;">
                                        {{ $item->barcode ?: 'SEM EAN' }}
                                    </span>
                                </td>
                                <td style="text-align:right; padding:1rem; font-weight:700; color:var(--text-primary); font-family:'Outfit'; font-size:1.1rem;">
                                    {{ number_format($item->quantity, 0, ',', '.') }}
                                </td>
                                <td style="text-align:center; padding:1rem;">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:0.35rem;">
                                        <button type="button" onclick="adjustQty({{ $item->id }}, -1)" class="btn btn-secondary btn-sm" style="width:32px; height:32px; padding:0; border-radius:8px; font-size:1rem; font-weight:800; display:flex; align-items:center; justify-content:center;">−</button>
                                        <input type="number" name="checked_quantities[{{ $item->id }}]" id="qty-{{ $item->id }}"
                                               value="{{ (int)$item->checked_quantity }}" min="0" step="1"
                                               onchange="updateRowStatus({{ $item->id }})"
                                               style="width:70px; text-align:center; font-size:1.1rem; font-weight:800; font-family:'Outfit'; padding:0.5rem; background:var(--bg-base); border:2px solid var(--border); border-radius:8px; color:var(--text-primary); transition:border-color 0.3s ease;">
                                        <button type="button" onclick="adjustQty({{ $item->id }}, 1)" class="btn btn-secondary btn-sm" style="width:32px; height:32px; padding:0; border-radius:8px; font-size:1rem; font-weight:800; display:flex; align-items:center; justify-content:center;">+</button>
                                    </div>
                                </td>
                                <td style="text-align:center; padding:1rem;">
                                    <span id="status-badge-{{ $item->id }}" class="badge" style="padding:0.4rem 0.8rem; font-weight:700; font-size:0.8rem; min-width:90px; display:inline-block; transition:all 0.3s ease;">
                                        Pendente
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>

</div>

@push('styles')
<style>
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.2); }
        50% { box-shadow: 0 0 20px 4px rgba(99,102,241,0.35); }
    }
    @keyframes flash-green {
        0% { background: rgba(52,199,89,0.15); }
        100% { background: transparent; }
    }
    @keyframes flash-red {
        0% { background: rgba(255,69,58,0.15); }
        100% { background: transparent; }
    }
    @keyframes scan-success-pop {
        0% { transform: scale(0.5); opacity: 0; }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); opacity: 1; }
    }
    .confer-row:hover {
        background: var(--bg-hover) !important;
    }
    .confer-row.flash-success {
        animation: flash-green 1s ease-out;
    }
    .confer-row.flash-error {
        animation: flash-red 1s ease-out;
    }
    #barcode-input:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 4px var(--accent-subtle), 0 0 25px rgba(99,102,241,0.15) !important;
    }
    .scan-pop {
        animation: scan-success-pop 0.4s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode-input');

    // Initialize all row statuses
    document.querySelectorAll('.confer-row').forEach(row => {
        updateRowStatus(row.dataset.itemId);
    });

    // Barcode scan handler
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = this.value.trim();
            if (!barcode) return;

            processBarcode(barcode);
            this.value = '';
        }
    });

    // Keep focus on barcode input
    document.addEventListener('click', function(e) {
        if (!e.target.closest('input[type="number"]') && !e.target.closest('button') && !e.target.closest('a')) {
            barcodeInput.focus();
        }
    });
});

function processBarcode(barcode) {
    const rows = document.querySelectorAll('.confer-row');
    let found = false;

    rows.forEach(row => {
        if (row.dataset.barcode === barcode) {
            found = true;
            const itemId = row.dataset.itemId;
            const expected = parseFloat(row.dataset.expected);
            const input = document.getElementById('qty-' + itemId);
            const current = parseFloat(input.value) || 0;

            input.value = current + 1;
            updateRowStatus(itemId);

            // Visual feedback
            row.classList.remove('flash-success', 'flash-error');
            void row.offsetWidth; // trigger reflow

            if (current + 1 <= expected) {
                row.classList.add('flash-success');
                showScanFeedback('success', row.querySelector('td:nth-child(2) div:first-child').textContent.trim());
            } else {
                row.classList.add('flash-error');
                showScanFeedback('excess', row.querySelector('td:nth-child(2) div:first-child').textContent.trim());
            }

            // Scroll to item
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    if (!found) {
        showScanFeedback('notfound', barcode);
        // Shake the scanner card
        const scannerCard = document.getElementById('scanner-card');
        scannerCard.style.borderColor = 'var(--red)';
        setTimeout(() => { scannerCard.style.borderColor = 'var(--accent)'; }, 800);
    }
}

function showScanFeedback(type, text) {
    const container = document.getElementById('scan-feedback');
    const icon = document.getElementById('scan-feedback-icon');
    const label = document.getElementById('scan-feedback-text');

    container.style.display = 'block';
    container.className = 'scan-pop';

    if (type === 'success') {
        icon.innerHTML = '<i class="fa-solid fa-circle-check" style="color:var(--green);"></i>';
        label.innerHTML = '<span style="color:var(--green);">✓ ' + text + '</span>';
    } else if (type === 'excess') {
        icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:var(--orange);"></i>';
        label.innerHTML = '<span style="color:var(--orange);">⚠ Excesso: ' + text + '</span>';
    } else {
        icon.innerHTML = '<i class="fa-solid fa-circle-xmark" style="color:var(--red);"></i>';
        label.innerHTML = '<span style="color:var(--red);">✗ Código não encontrado: ' + text + '</span>';
    }

    setTimeout(() => { container.style.display = 'none'; }, 3000);
}

function adjustQty(itemId, delta) {
    const input = document.getElementById('qty-' + itemId);
    let val = parseFloat(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
    updateRowStatus(itemId);
}

function updateRowStatus(itemId) {
    const row = document.getElementById('item-row-' + itemId);
    const input = document.getElementById('qty-' + itemId);
    const badge = document.getElementById('status-badge-' + itemId);
    const expected = parseFloat(row.dataset.expected);
    const checked = parseFloat(input.value) || 0;

    if (checked === 0) {
        badge.style.background = 'var(--orange-bg)';
        badge.style.color = 'var(--orange)';
        badge.innerHTML = '<i class="fa-solid fa-clock mr-1"></i> Pendente';
        input.style.borderColor = 'var(--border)';
    } else if (Math.abs(checked - expected) < 0.001) {
        badge.style.background = 'var(--green-bg)';
        badge.style.color = 'var(--green)';
        badge.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i> Correto';
        input.style.borderColor = 'var(--green)';
    } else if (checked < expected) {
        badge.style.background = 'var(--blue-bg)';
        badge.style.color = 'var(--blue)';
        badge.innerHTML = '<i class="fa-solid fa-spinner mr-1"></i> Parcial';
        input.style.borderColor = 'var(--blue)';
    } else {
        badge.style.background = 'var(--red-bg)';
        badge.style.color = 'var(--red)';
        badge.innerHTML = '<i class="fa-solid fa-exclamation-triangle mr-1"></i> Excesso';
        input.style.borderColor = 'var(--red)';
    }

    updateGlobalStatus();
}

function updateGlobalStatus() {
    const rows = document.querySelectorAll('.confer-row');
    let totalItems = rows.length;
    let correctCount = 0;
    let anyChecked = false;
    let hasDivergence = false;

    rows.forEach(row => {
        const expected = parseFloat(row.dataset.expected);
        const input = document.getElementById('qty-' + row.dataset.itemId);
        const checked = parseFloat(input.value) || 0;

        if (checked > 0) anyChecked = true;
        if (Math.abs(checked - expected) < 0.001) correctCount++;
        else if (checked > 0 && Math.abs(checked - expected) >= 0.001) hasDivergence = true;
    });

    document.getElementById('items-checked-count').textContent = correctCount;

    const badge = document.getElementById('global-status-badge');
    const text = document.getElementById('global-status-text');
    const btn = document.getElementById('btn-finalize');

    if (!anyChecked) {
        badge.style.background = 'var(--orange-bg)';
        badge.style.color = 'var(--orange)';
        text.innerHTML = '<i class="fa-solid fa-clock mr-2"></i> Aguardando Conferência';
        btn.disabled = true;
    } else if (correctCount === totalItems) {
        badge.style.background = 'var(--green-bg)';
        badge.style.color = 'var(--green)';
        text.innerHTML = '<i class="fa-solid fa-check-double mr-2"></i> Todos os Itens Conferidos!';
        btn.disabled = false;
    } else {
        badge.style.background = hasDivergence ? 'var(--red-bg)' : 'var(--blue-bg)';
        badge.style.color = hasDivergence ? 'var(--red)' : 'var(--blue)';
        text.innerHTML = '<i class="fa-solid fa-spinner mr-2"></i> Conferência em Andamento';
        btn.disabled = false;
    }
}

function resetAllChecked() {
    if (!confirm('Deseja realmente resetar todas as quantidades conferidas?')) return;

    document.querySelectorAll('.confer-row').forEach(row => {
        const input = document.getElementById('qty-' + row.dataset.itemId);
        input.value = 0;
        updateRowStatus(row.dataset.itemId);
    });
}

function submitConference() {
    const rows = document.querySelectorAll('.confer-row');
    let hasDivergence = false;

    rows.forEach(row => {
        const expected = parseFloat(row.dataset.expected);
        const input = document.getElementById('qty-' + row.dataset.itemId);
        const checked = parseFloat(input.value) || 0;
        if (Math.abs(checked - expected) >= 0.001) hasDivergence = true;
    });

    let msg = hasDivergence
        ? 'Existem divergências nos itens conferidos. Deseja finalizar a conferência com status DIVERGENTE?'
        : 'Todos os itens batem com a nota fiscal. Finalizar conferência com status CONFERIDA?';

    if (confirm(msg)) {
        document.getElementById('conference-form').submit();
    }
}
</script>
@endpush
@endsection
