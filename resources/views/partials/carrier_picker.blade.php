{{-- Carrier Picker Modal -- reusable partial --}}
<div id="carrier-picker-modal" class="modal-backdrop carrier-picker-root" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="carrier-picker-title">
    <div class="modal carrier-picker-dialog">
        <div class="modal-header">
            <div class="carrier-picker-header-text">
                <i class="fa-solid fa-truck-fast" style="color:var(--accent); font-size:1.25rem;"></i>
                <div>
                    <h3 class="modal-title" id="carrier-picker-title">Buscar e Selecionar Transportadora</h3>
                    <p class="carrier-picker-subtitle">Pesquise por nome, CNPJ, cidade ou UF</p>
                </div>
            </div>
            <button type="button" class="icon-btn" id="close-carrier-picker" aria-label="Fechar" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-body carrier-picker-body">
            <div class="carrier-picker-toolbar">
                <div class="form-group" style="position:relative; margin-bottom:0;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" id="carrier-search-input" class="form-control" placeholder="Digite o nome, CNPJ, cidade ou UF..." style="padding-left:2.5rem; width:100%;" autocomplete="off">
                </div>
            </div>

            <div class="carrier-picker-results" aria-live="polite">
                <div id="carrier-empty-state" class="carrier-picker-state" style="display:none;">
                    <i class="fa-solid fa-truck-slash" style="font-size:2.5rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                    <p>Nenhuma transportadora correspondente encontrada.</p>
                </div>
                <table class="carrier-picker-table" id="carrier-picker-table">
                    <thead>
                        <tr>
                            <th>Transportadora</th>
                            <th>CNPJ / RNTRC</th>
                            <th>Localização</th>
                            <th>Placa</th>
                            <th style="text-align: center; width: 120px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carriers ?? [] as $c)
                            <tr class="carrier-row"
                                data-id="{{ $c->id }}"
                                data-name="{{ strtolower($c->name) }}"
                                data-cnpj="{{ strtolower($c->cnpj ?? '') }}"
                                data-city="{{ strtolower($c->city ?? '') }}"
                                data-state="{{ strtolower($c->state ?? '') }}">
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $c->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                                        IE: {{ $c->state_registration ?: 'Não informada' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500; color:var(--text-secondary); font-family:monospace;">{{ $c->cnpj ?: 'Sem CNPJ' }}</div>
                                </td>
                                <td>
                                    <span class="badge" style="background:var(--bg-hover); color:var(--text-secondary); font-size:0.75rem; font-weight:600;">
                                        {{ $c->city ?: 'Sem cidade' }}{{ $c->state ? ' / ' . $c->state : '' }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-family:monospace; font-size:0.85rem; font-weight:600; color:var(--text-primary);">
                                        {{ $c->vehicle_plate ?: '—' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-primary btn-sm btn-select-carrier"
                                            data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}"
                                            data-cnpj="{{ $c->cnpj }}"
                                            data-state_reg="{{ $c->state_registration }}"
                                            data-street="{{ $c->street }}"
                                            data-number="{{ $c->number }}"
                                            data-city="{{ $c->city }}"
                                            data-state="{{ $c->state }}"
                                            data-plate="{{ $c->vehicle_plate }}"
                                            data-uf="{{ $c->vehicle_uf }}"
                                            style="padding:0.4rem 0.8rem; font-size:0.8rem; justify-content:center; width:100%;">
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
                <button type="button" class="btn btn-secondary" id="cancel-carrier-picker">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .carrier-picker-root.modal-backdrop {
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
        .carrier-picker-root.modal-backdrop {
            left: var(--sidebar-w, 0) !important;
        }
    }
    .carrier-picker-root .carrier-picker-dialog {
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
    .carrier-picker-header-text {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }
    .carrier-picker-subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
    }
    .carrier-picker-body {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) !important;
        gap: 1rem !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
    }
    .carrier-picker-toolbar {
        min-height: 0;
        overflow: visible;
    }
    .carrier-picker-results {
        overflow-y: auto !important;
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        background: var(--bg-base);
    }
    .carrier-picker-results::-webkit-scrollbar {
        width: 8px;
    }
    .carrier-picker-results::-webkit-scrollbar-thumb {
        background: var(--border-strong);
        border-radius: 4px;
    }
    .carrier-picker-table {
        width: 100%;
        border-collapse: collapse;
    }
    .carrier-picker-table th {
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
    .carrier-picker-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .carrier-picker-table tr.carrier-row:hover {
        background: var(--bg-hover);
    }
    .carrier-picker-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('carrier-picker-modal');
    if (!modal) return;

    // Move modal to body to avoid parent stacking context issues
    document.body.appendChild(modal);

    const searchInp = document.getElementById('carrier-search-input');
    const emptyState = document.getElementById('carrier-empty-state');
    const table = document.getElementById('carrier-picker-table');

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
        searchInp.value = '';
        document.querySelectorAll('#carrier-picker-table .carrier-row').forEach(row => row.style.display = '');
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
        const currentRows = document.querySelectorAll('#carrier-picker-table .carrier-row');

        currentRows.forEach(row => {
            const name  = row.dataset.name;
            const cnpj  = row.dataset.cnpj;
            const city  = row.dataset.city;
            const state = row.dataset.state;

            if (name.includes(query) || cnpj.includes(query) || city.includes(query) || state.includes(query)) {
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

    // Select carrier action — fills the carrier-select AND the manual fields
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-select-carrier');
        if (!btn) return;

        const id       = btn.dataset.id;
        const name     = btn.dataset.name     || '';
        const cnpj     = btn.dataset.cnpj     || '';
        const stateReg = btn.dataset.state_reg || '';
        const street   = btn.dataset.street   || '';
        const number   = btn.dataset.number   || '';
        const city     = btn.dataset.city     || '';
        const state    = btn.dataset.state    || '';
        const plate    = btn.dataset.plate    || '';
        const uf       = btn.dataset.uf       || '';

        // Update the carrier-select dropdown
        const carrierSel = document.getElementById('carrier-select');
        if (carrierSel) {
            carrierSel.value = id;
            carrierSel.dispatchEvent(new Event('change'));
        }

        // Fill manual fields directly (same as fillCarrierData)
        fillCarrierFieldsManually({ name, cnpj, stateReg, street, number, city, state, plate, uf });

        closeModal();
    });

    // Open trigger — button with id="btn-open-carrier-picker"
    document.addEventListener('click', function(e) {
        if (e.target.closest('#btn-open-carrier-picker')) {
            e.preventDefault();
            openModal();
        }
    });

    // Close buttons
    document.getElementById('close-carrier-picker')?.addEventListener('click', closeModal);
    document.getElementById('cancel-carrier-picker')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
})();
</script>
@endpush
