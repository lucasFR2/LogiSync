{{-- Product Picker Modal — reusable partial --}}
<div id="product-picker-modal" class="modal-backdrop product-picker-root" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="product-picker-title">
    <div class="modal product-picker-dialog">
        <div class="modal-header">
            <div class="product-picker-header-text">
                <i class="fa-solid fa-boxes-stacked" style="color:var(--accent); font-size:1.25rem;"></i>
                <div>
                    <h3 class="modal-title" id="product-picker-title">Buscar e Selecionar Produto</h3>
                    <p class="product-picker-subtitle">Pesquise por nome, SKU, código de barras ou categoria</p>
                </div>
            </div>
            <button type="button" class="icon-btn" id="close-product-picker" aria-label="Fechar" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-body product-picker-body">
            <div class="product-picker-toolbar">
                <div class="form-group" style="position:relative; margin-bottom:0;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" id="product-search-input" class="form-control" placeholder="Digite o nome, EAN/barras ou categoria..." style="padding-left:2.5rem; width:100%;" autocomplete="off">
                </div>
            </div>

            <div class="product-picker-results" aria-live="polite">
                <div id="product-empty-state" class="product-picker-state" style="display:none;">
                    <i class="fa-solid fa-box-open" style="font-size:2.5rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                    <p>Nenhum produto correspondente encontrado.</p>
                </div>
                <table class="product-picker-table" id="product-picker-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th style="text-align: right;">Preço</th>
                            <th style="text-align: right;">Estoque Atual</th>
                            <th style="text-align: center; width: 120px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr class="product-row" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name) }}" data-barcode="{{ strtolower($p->barcode ?? '') }}" data-category="{{ strtolower($p->category ?? '') }}">
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $p->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:2px;">
                                        EAN/Barras: {{ $p->barcode ?: 'Sem Código' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background:var(--bg-hover); color:var(--text-secondary); font-size:0.75rem; font-weight:600;">
                                        {{ $p->category ?: 'Sem categoria' }}
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight:600; color:var(--text-primary);">
                                    R$ {{ number_format($p->unit_price, 2, ',', '.') }}
                                </td>
                                <td style="text-align: right;">
                                    @if($p->quantity <= 0)
                                        <span class="badge" style="background:var(--red-bg); color:var(--red); font-size:0.75rem; font-weight:700;">
                                            Sem Estoque
                                        </span>
                                    @else
                                        <span class="badge" style="background:var(--green-bg); color:var(--green); font-size:0.75rem; font-weight:700;">
                                            {{ $p->quantity }} {{ $p->unit ?? 'un' }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-primary btn-sm btn-select-product" data-id="{{ $p->id }}" style="padding:0.4rem 0.8rem; font-size:0.8rem; justify-content:center; width:100%;">
                                        Selecionar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <div style="display:flex; justify-content:flex-end; width:100%;">
                <button type="button" class="btn btn-secondary" id="cancel-product-picker">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .product-picker-root.modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        z-index: 99 !important;
        background: rgba(15, 23, 42, 0.4) !important;
        display: none;
        align-items: stretch !important;
        justify-content: stretch !important;
        backdrop-filter: blur(5px);
    }
    @media (min-width: 769px) {
        .product-picker-root.modal-backdrop {
            left: var(--sidebar-w, 0) !important;
        }
    }
    .product-picker-root .product-picker-dialog {
        max-width: 100% !important;
        width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) auto !important;
        background: var(--bg-surface) !important;
        overflow: hidden !important;
        animation: modalEntrance 0.2s ease-out;
    }
    .product-picker-header-text {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }
    .product-picker-subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
    }
    .product-picker-body {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) !important;
        gap: 1rem !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
    }
    .product-picker-toolbar {
        min-height: 0;
        overflow: visible;
    }
    .product-picker-results {
        overflow-y: auto !important;
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        background: var(--bg-base);
    }
    .product-picker-results::-webkit-scrollbar {
        width: 8px;
    }
    .product-picker-results::-webkit-scrollbar-thumb {
        background: var(--border-strong);
        border-radius: 4px;
    }
    .product-picker-table {
        width: 100%;
        border-collapse: collapse;
    }
    .product-picker-table th {
        position: sticky;
        top: 0;
        background: var(--bg-hover);
        z-index: 10;
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
    }
    .product-picker-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .product-picker-table tr.product-row:hover {
        background: var(--bg-hover);
    }
    .product-picker-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    @keyframes modalEntrance {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('product-picker-modal');
    if (!modal) return;

    // Move modal to body to avoid parent stacking context issues
    document.body.appendChild(modal);

    const searchInp = document.getElementById('product-search-input');
    const tableRows = document.querySelectorAll('#product-picker-table .product-row');
    const emptyState = document.getElementById('product-empty-state');
    const table = document.getElementById('product-picker-table');
    const productSelect = document.getElementById('productSelect');

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
        searchInp.value = '';
        tableRows.forEach(row => row.style.display = '');
        table.style.display = '';
        emptyState.style.display = 'none';
        setTimeout(() => searchInp.focus(), 50);
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    }

    // Filter logic
    searchInp.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        const currentRows = document.querySelectorAll('#product-picker-table .product-row');

        currentRows.forEach(row => {
            const name = row.dataset.name;
            const barcode = row.dataset.barcode;
            const category = row.dataset.category;

            if (name.includes(query) || barcode.includes(query) || category.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            table.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            table.style.display = '';
            emptyState.style.display = 'none';
        }
    });

    // Select product action
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-select-product')) {
            const btn = e.target.closest('.btn-select-product');
            const id = btn.dataset.id;
            
            if (window.activeProductSelectTarget) {
                window.activeProductSelectTarget.value = id;
                window.activeProductSelectTarget.dispatchEvent(new Event('change'));
            }
            closeModal();
        }
    });

    // Open triggers — click on magnifier glass (inventory/create)
    document.addEventListener('click', function(e) {
        if (e.target.closest('#btn-open-product-picker') || e.target.closest('#productSelect_display')) {
            e.preventDefault();
            window.activeProductSelectTarget = document.getElementById('productSelectHidden');
            openModal();
        }
    });

    // Open triggers — click on magnifier glass in invoice rows (invoices/create)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-invoice-product-picker');
        if (btn) {
            e.preventDefault();
            const rowIdx = btn.dataset.row;
            const card = document.getElementById(`item-card-${rowIdx}`);
            const selectEl = card ? card.querySelector('.product-select') : null;
            window.activeProductSelectTarget = selectEl;
            openModal();
        }
    });

    // Open triggers — click on magnifier glass in bulk import rows (inventory/bulk_import)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-bulk-product-picker');
        if (btn) {
            e.preventDefault();
            const itemId = btn.dataset.itemId;
            const selectEl = document.getElementById(`product-select-${itemId}`);
            window.activeProductSelectTarget = selectEl;
            openModal();
        }
    });

    // Close buttons
    document.getElementById('close-product-picker')?.addEventListener('click', closeModal);
    document.getElementById('cancel-product-picker')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
})();
</script>
@endpush
