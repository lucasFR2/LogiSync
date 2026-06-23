{{-- Customer Picker Modal -- reusable partial --}}
<div id="customer-picker-modal" class="modal-backdrop customer-picker-root" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="customer-picker-title">
    <div class="modal customer-picker-dialog">
        <div class="modal-header">
            <div class="customer-picker-header-text">
                <i class="fa-solid fa-user-tie" style="color:var(--accent); font-size:1.25rem;"></i>
                <div>
                    <h3 class="modal-title" id="customer-picker-title">Buscar e Selecionar Cliente</h3>
                    <p class="customer-picker-subtitle">Pesquise por nome, CPF/CNPJ, e-mail ou cidade</p>
                </div>
            </div>
            <button type="button" class="icon-btn" id="close-customer-picker" aria-label="Fechar" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-body customer-picker-body">
            <div class="customer-picker-toolbar">
                <div class="form-group" style="position:relative; margin-bottom:0;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" id="customer-search-input" class="form-control" placeholder="Digite o nome, CPF/CNPJ, e-mail ou cidade..." style="padding-left:2.5rem; width:100%;" autocomplete="off">
                </div>
            </div>

            <div class="customer-picker-results" aria-live="polite">
                <div id="customer-empty-state" class="customer-picker-state" style="display:none;">
                    <i class="fa-solid fa-user-slash" style="font-size:2.5rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                    <p>Nenhum cliente correspondente encontrado.</p>
                </div>
                <table class="customer-picker-table" id="customer-picker-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Contato</th>
                            <th>Localização</th>
                            <th style="text-align: center; width: 120px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $c)
                            <tr class="customer-row" data-id="{{ $c->id }}" data-name="{{ strtolower($c->name) }}" data-document="{{ strtolower($c->document ?? '') }}" data-email="{{ strtolower($c->email ?? '') }}" data-city="{{ strtolower($c->city ?? '') }}">
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $c->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:2px;">
                                        Doc: {{ $c->document ?: 'Sem documento' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:500; color:var(--text-secondary);">{{ $c->email ?: 'Sem e-mail' }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                                        Tel: {{ $c->phone ?: 'Sem telefone' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background:var(--bg-hover); color:var(--text-secondary); font-size:0.75rem; font-weight:600;">
                                        {{ $c->city ?: 'Sem cidade' }} {{ $c->state ? '/ ' . $c->state : '' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-primary btn-sm btn-select-customer" data-id="{{ $c->id }}" style="padding:0.4rem 0.8rem; font-size:0.8rem; justify-content:center; width:100%;">
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
                <button type="button" class="btn btn-secondary" id="cancel-customer-picker">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .customer-picker-root.modal-backdrop {
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
        .customer-picker-root.modal-backdrop {
            left: var(--sidebar-w, 0) !important;
        }
    }
    .customer-picker-root .customer-picker-dialog {
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
    .customer-picker-header-text {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }
    .customer-picker-subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
    }
    .customer-picker-body {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) !important;
        gap: 1rem !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
    }
    .customer-picker-toolbar {
        min-height: 0;
        overflow: visible;
    }
    .customer-picker-results {
        overflow-y: auto !important;
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        background: var(--bg-base);
    }
    .customer-picker-results::-webkit-scrollbar {
        width: 8px;
    }
    .customer-picker-results::-webkit-scrollbar-thumb {
        background: var(--border-strong);
        border-radius: 4px;
    }
    .customer-picker-table {
        width: 100%;
        border-collapse: collapse;
    }
    .customer-picker-table th {
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
    .customer-picker-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .customer-picker-table tr.customer-row:hover {
        background: var(--bg-hover);
    }
    .customer-picker-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('customer-picker-modal');
    if (!modal) return;

    // Move modal to body to avoid parent stacking context issues
    document.body.appendChild(modal);

    const searchInp = document.getElementById('customer-search-input');
    const tableRows = document.querySelectorAll('#customer-picker-table .customer-row');
    const emptyState = document.getElementById('customer-empty-state');
    const table = document.getElementById('customer-picker-table');

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
        const currentRows = document.querySelectorAll('#customer-picker-table .customer-row');

        currentRows.forEach(row => {
            const name = row.dataset.name;
            const documentNum = row.dataset.document;
            const email = row.dataset.email;
            const city = row.dataset.city;

            if (name.includes(query) || documentNum.includes(query) || email.includes(query) || city.includes(query)) {
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

    // Select customer action
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-select-customer')) {
            const btn = e.target.closest('.btn-select-customer');
            const id = btn.dataset.id;
            
            const custSelect = document.getElementById('customer-select');
            if (custSelect) {
                custSelect.value = id;
                custSelect.dispatchEvent(new Event('change'));
            }
            closeModal();
        }
    });

    // Open trigger
    document.addEventListener('click', function(e) {
        if (e.target.closest('#btn-open-customer-picker')) {
            e.preventDefault();
            openModal();
        }
    });

    // Close buttons
    document.getElementById('close-customer-picker')?.addEventListener('click', closeModal);
    document.getElementById('cancel-customer-picker')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
})();
</script>
@endpush
