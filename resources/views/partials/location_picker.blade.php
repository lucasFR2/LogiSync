{{-- Warehouse Location Picker Modal — reusable partial --}}
<div id="location-picker-modal" class="modal-backdrop location-picker-root" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="location-picker-title">
    <div class="modal location-picker-dialog">
        <div class="modal-header">
            <div class="location-picker-header-text">
                <i class="fa-solid fa-map-location-dot" style="color:var(--blue); font-size:1.25rem;"></i>
                <div>
                    <h3 class="modal-title" id="location-picker-title">Endereço Físico do Estoque</h3>
                    <p class="location-picker-subtitle">Selecione corredor, coluna e nível — como em Localizações</p>
                </div>
            </div>
            <button type="button" class="icon-btn" id="close-location-picker" aria-label="Fechar" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-body location-picker-body">
            <div class="location-picker-toolbar">
            <div class="location-picker-legend">
                <span><span class="location-picker-dot location-picker-dot--free"></span> Livre</span>
                <span><span class="location-picker-dot location-picker-dot--busy"></span> Ocupada</span>
            </div>

            <div class="location-picker-hint">
                <i class="fa-solid fa-info-circle"></i>
                Mesmo formato do cadastro em <strong>Localizações</strong>:
                <strong>Corredor-Coluna-Nível</strong> (ex.: <strong>R01-05-N2</strong>).
            </div>

            <div class="location-picker-filters-card">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label" for="filter-aisle">Corredor (Aisle)</label>
                        <input type="text" id="filter-aisle" class="form-control" placeholder="Ex: R01" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="filter-column">Coluna (Column)</label>
                        <input type="text" id="filter-column" class="form-control" placeholder="Ex: 05" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="filter-level">Nível (Level)</label>
                        <input type="text" id="filter-level" class="form-control" placeholder="Ex: N3" autocomplete="off">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-top:1rem;">
                    <div class="form-group">
                        <label class="form-label" for="location-search">Código completo</label>
                        <input type="text" id="location-search" class="form-control" placeholder="Ex: R01-05-N2" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="filter-status">Mostrar</label>
                        <select id="filter-status" class="form-control">
                            <option value="free">Apenas livres</option>
                            <option value="all">Todas as posições</option>
                        </select>
                    </div>
                </div>

                <div class="location-picker-actions">
                    <button type="button" id="btn-search-locations" class="btn btn-primary">
                        <i class="fa-solid fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            </div>

            <div class="location-picker-results" aria-live="polite">
                <div id="location-loading" class="location-picker-state" style="display:none;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size:2rem; color:var(--accent);"></i>
                    <p>Carregando posições...</p>
                </div>
                <div id="location-empty" class="location-picker-state">
                    <i class="fa-solid fa-map-pin" style="font-size:2.5rem; color:var(--text-muted);"></i>
                    <p>Preencha os filtros e clique em <strong>Buscar</strong>.</p>
                </div>
                <div id="location-grid" class="location-picker-grid" style="display:none;"></div>
            </div>

        </div>

        <div class="modal-footer location-picker-footer">
            <div id="location-selected-bar" class="location-picker-selected" style="display:none;">
                <i class="fa-solid fa-check-circle"></i>
                Selecionado: <span id="location-selected-code"></span>
            </div>
            <div class="location-picker-footer-actions">
                <button type="button" class="btn btn-secondary" id="clear-location-picker">
                    <i class="fa-solid fa-eraser"></i> Limpar
                </button>
                <button type="button" class="btn btn-secondary" id="cancel-location-picker">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Grid layout: header | body (toolbar + scroll) | footer — altura fixa para scroll confiável */
    .location-picker-root.modal-backdrop {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        z-index: 95 !important;
        padding: 0 !important;
        background: var(--bg-base) !important;
        display: none;
        align-items: stretch !important;
        justify-content: stretch !important;
    }
    @media (min-width: 769px) {
        .location-picker-root.modal-backdrop {
            left: var(--sidebar-w) !important;
        }
    }
    .location-picker-root.modal-backdrop > .location-picker-dialog {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) auto !important;
        flex-direction: unset !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        overflow: hidden !important;
    }
    .location-picker-root .modal-header {
        flex-shrink: 0;
    }
    .location-picker-root .location-picker-body {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr) !important;
        gap: 0.75rem !important;
        padding: 1rem 1.5rem !important;
        min-height: 0 !important;
        overflow: hidden !important;
        overflow-y: hidden !important;
        flex: none !important;
    }
    .location-picker-toolbar {
        min-height: 0;
        overflow: visible;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .location-picker-root .location-picker-footer {
        flex-shrink: 0;
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem 1rem !important;
    }
    .location-picker-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .location-picker-header-text {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }
    .location-picker-subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 400;
    }
    .location-picker-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        padding: 0.65rem 1rem;
        background: var(--bg-hover);
        border-radius: var(--r-md);
        font-size: 0.8rem;
        font-weight: 600;
    }
    .location-picker-legend span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .location-picker-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .location-picker-dot--free { background: var(--green); }
    .location-picker-dot--busy { background: var(--red); }
    .location-picker-hint {
        padding: 0.75rem 1rem;
        background: var(--blue-bg);
        border: 1px solid var(--blue);
        border-radius: var(--r-md);
        font-size: 0.8rem;
        color: var(--blue);
        line-height: 1.45;
    }
    .location-picker-filters-card {
        padding: 1.25rem;
        background: var(--bg-hover);
        border: 1px dashed var(--border);
        border-radius: var(--r-md);
    }
    .location-picker-filters-card .form-group {
        margin-bottom: 0;
    }
    .location-picker-filters-card .form-label {
        text-transform: none;
        letter-spacing: normal;
    }
    .location-picker-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    .location-picker-actions .btn {
        min-width: 140px;
        height: 48px;
        justify-content: center;
    }
    .location-picker-results {
        min-height: 0 !important;
        max-height: 100% !important;
        height: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        background: var(--bg-base);
        padding: 1rem;
        padding-bottom: 1.5rem;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        position: relative;
        isolation: isolate;
    }
    .location-picker-results::-webkit-scrollbar {
        width: 8px;
    }
    .location-picker-results::-webkit-scrollbar-thumb {
        background: var(--border-strong);
        border-radius: 4px;
    }
    .location-picker-state {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--text-muted);
    }
    .location-picker-state p {
        margin: 0.75rem 0 0;
        font-size: 0.875rem;
    }
    .location-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.6rem;
    }
    .location-picker-selected {
        width: 100%;
        padding: 0.65rem 1rem;
        background: var(--green-bg);
        border: 1px solid var(--green);
        border-radius: var(--r-md);
        font-size: 0.875rem;
        color: var(--green);
        font-weight: 600;
    }
    .loc-card {
        padding: 0.65rem 0.5rem;
        border: 2px solid var(--border);
        border-radius: var(--r-sm);
        cursor: pointer;
        background: var(--bg-surface);
        text-align: center;
        width: 100%;
    }
    .loc-card.loc-free {
        border-color: var(--green);
        background: var(--green-bg);
    }
    .loc-card.loc-selected {
        outline: 3px solid var(--accent);
        outline-offset: 2px;
    }
    .loc-card.loc-occupied {
        opacity: 0.55;
        cursor: not-allowed;
        border-color: var(--red);
        background: var(--red-bg);
    }
    .loc-card-code {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--accent);
        display: block;
    }
    .loc-card-sub {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
        display: block;
    }
    .loc-card-occupant {
        font-size: 0.6rem;
        color: var(--red);
        margin-top: 0.25rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
    @media (max-width: 640px) {
        .location-picker-root .modal-body {
            padding: 1.25rem;
        }
        .location-picker-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modal        = document.getElementById('location-picker-modal');
    if (!modal) return;

    // Move modal to body to avoid parent stacking context issues
    document.body.appendChild(modal);

    const displayInp   = document.getElementById('warehouse_location_display');
    const idInput      = document.getElementById('warehouse_location_id');
    const aisleInput   = document.getElementById('filter-aisle');
    const columnInput  = document.getElementById('filter-column');
    const levelInput   = document.getElementById('filter-level');
    const searchInput  = document.getElementById('location-search');
    const statusFilter = document.getElementById('filter-status');
    const searchBtn    = document.getElementById('btn-search-locations');
    const grid         = document.getElementById('location-grid');
    const loading      = document.getElementById('location-loading');
    const empty        = document.getElementById('location-empty');
    const selBar       = document.getElementById('location-selected-bar');
    const selCode      = document.getElementById('location-selected-code');

    const emptyDefaultHtml = empty ? empty.innerHTML : '';
    let pendingId   = idInput?.value || '';
    let pendingCode = displayInp?.value || '';

    function syncSelectedBar() {
        const currentDisplayInp = window.locationPickerState?.displayInp || displayInp;
        const code = currentDisplayInp?.value?.trim();
        if (!selBar || !selCode) return;
        if (code && code !== 'Não alocado' && code !== 'Não definido') {
            selCode.textContent = code;
            selBar.style.display = 'block';
        } else {
            selBar.style.display = 'none';
        }
    }

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
        
        const currentIdInp = window.locationPickerState?.idInput || idInput;
        const currentDisplayInp = window.locationPickerState?.displayInp || displayInp;
        
        pendingId   = currentIdInp?.value || '';
        pendingCode = currentDisplayInp?.value || '';
        syncSelectedBar();
        fetchLocations();
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    }

    function applySelection(id, code) {
        if (idInput) idInput.value = id;
        if (displayInp) displayInp.value = code;

        if (window.locationPickerState) {
            if (window.locationPickerState.idInput) window.locationPickerState.idInput.value = id;
            if (window.locationPickerState.displayInp) window.locationPickerState.displayInp.value = code;
        }

        document.dispatchEvent(new CustomEvent('locationSelected', {
            detail: { id: id, full_code: code }
        }));

        pendingId = String(id);
        pendingCode = code;
        syncSelectedBar();
        closeModal();
    }

    function clearSelection() {
        if (idInput) idInput.value = '';
        if (displayInp) displayInp.value = '';

        if (window.locationPickerState) {
            if (window.locationPickerState.idInput) window.locationPickerState.idInput.value = '';
            if (window.locationPickerState.displayInp) window.locationPickerState.displayInp.value = '';
        }

        document.dispatchEvent(new CustomEvent('locationCleared'));

        pendingId = '';
        pendingCode = '';
        if (selBar) selBar.style.display = 'none';
        grid?.querySelectorAll('.loc-selected').forEach(el => el.classList.remove('loc-selected'));
    }

    function showState(state) {
        if (loading) loading.style.display = state === 'loading' ? 'block' : 'none';
        if (empty)   empty.style.display   = state === 'empty'   ? 'block' : 'none';
        if (grid)    grid.style.display    = state === 'grid'    ? 'grid'  : 'none';
    }

    function buildSearchUrl() {
        const params = new URLSearchParams();
        const q      = (searchInput?.value || '').trim();
        const aisle  = (aisleInput?.value || '').trim();
        const column = (columnInput?.value || '').trim();
        const level  = (levelInput?.value || '').trim();
        const status = statusFilter?.value || 'free';

        if (q) params.set('q', q);
        if (aisle) params.set('aisle', aisle);
        if (column) params.set('column', column);
        if (level) params.set('level', level);
        if (status === 'free') params.set('free_only', '1');

        return `{{ route('locations.search') }}?${params.toString()}`;
    }

    function fetchLocations() {
        showState('loading');
        if (grid) grid.innerHTML = '';
        if (empty) empty.innerHTML = emptyDefaultHtml;

        fetch(buildSearchUrl(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    if (empty) {
                        empty.innerHTML = '<i class="fa-solid fa-inbox" style="font-size:2rem;color:var(--text-muted);"></i><p>Nenhuma posição encontrada. Ajuste os filtros ou cadastre em <strong>Localizações</strong>.</p>';
                    }
                    showState('empty');
                    return;
                }

                data.forEach(loc => {
                    const card = document.createElement('button');
                    card.type = 'button';
                    let cls = 'loc-card';
                    let sub = '';

                    const readable = [loc.aisle, loc.column, loc.level].filter(Boolean).join(' · ');

                    if (loc.is_occupied) {
                        cls += ' loc-occupied';
                        sub = `<span class="loc-card-occupant" title="${loc.occupant_name || ''}"><i class="fa-solid fa-lock"></i> ${loc.occupant_name || 'Ocupado'}</span>`;
                    } else {
                        cls += ' loc-free';
                        sub = `<span class="loc-card-sub" style="color:var(--green);"><i class="fa-solid fa-check"></i> Livre</span>`;
                    }

                    if (String(loc.id) === String(pendingId) || loc.full_code === pendingCode) {
                        cls += ' loc-selected';
                    }

                    card.className = cls;
                    card.innerHTML = `
                        <span class="loc-card-code">${loc.full_code}</span>
                        <span class="loc-card-sub">${readable}</span>
                        ${sub}
                    `;

                    if (!loc.is_occupied) {
                        card.addEventListener('click', () => applySelection(loc.id, loc.full_code));
                    }

                    grid.appendChild(card);
                });

                showState('grid');
            })
            .catch(() => {
                if (empty) {
                    empty.innerHTML = '<p style="margin:0;color:var(--red);"><i class="fa-solid fa-triangle-exclamation"></i> Erro ao carregar posições.</p>';
                }
                showState('empty');
            });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-open-location-picker]') || e.target.closest('#btn-open-location-picker')) {
            e.preventDefault();
            openModal();
        }
        if (e.target.closest('#warehouse_location_display')) {
            e.preventDefault();
            openModal();
        }
    });

    document.getElementById('close-location-picker')?.addEventListener('click', closeModal);
    document.getElementById('cancel-location-picker')?.addEventListener('click', closeModal);
    document.getElementById('clear-location-picker')?.addEventListener('click', clearSelection);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    searchBtn?.addEventListener('click', fetchLocations);
    statusFilter?.addEventListener('change', fetchLocations);
    [aisleInput, columnInput, levelInput, searchInput].forEach(el => {
        el?.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); fetchLocations(); }
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });

    syncSelectedBar();
})();
</script>
@endpush
