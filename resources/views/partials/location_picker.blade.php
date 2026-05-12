{{-- Warehouse Location Picker Modal — Self-contained component --}}
<div id="location-picker-modal" class="modal-backdrop" style="display:none;">
    <div class="modal" style="max-width:920px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <i class="fa-solid fa-warehouse" style="color:var(--accent); font-size:1.25rem;"></i>
                <div>
                    <h3 class="modal-title">Endereço Físico do Estoque</h3>
                    <p style="margin:0; font-size:0.8rem; color:var(--text-muted); font-weight:400;">Selecione a posição de armazenamento no armazém</p>
                </div>
            </div>
            <button type="button" id="close-location-picker" class="icon-btn" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body" style="gap:1.5rem;">

            {{-- Legend --}}
            <div style="display:flex; gap:1.5rem; flex-wrap:wrap; padding:0.75rem 1rem; background:var(--bg-hover); border-radius:var(--r-md); font-size:0.8rem; font-weight:600;">
                <span style="display:flex; align-items:center; gap:0.4rem; color:var(--green);">
                    <span style="width:10px;height:10px;border-radius:50%;background:var(--green);"></span> Livre — Pode ser selecionada
                </span>
                <span style="display:flex; align-items:center; gap:0.4rem; color:var(--red);">
                    <span style="width:10px;height:10px;border-radius:50%;background:var(--red);"></span> Ocupada — Já contém produto
                </span>
            </div>

            {{-- Nomenclature Guide --}}
            <div style="padding:0.75rem 1rem; background:var(--blue-bg); border:1px solid var(--blue); border-radius:var(--r-md); font-size:0.8rem; color:var(--blue);">
                <i class="fa-solid fa-info-circle"></i>
                <strong>Formato:</strong> <code style="background:transparent; font-weight:700;">R = Rua/Corredor</code> ·
                <code style="background:transparent; font-weight:700;">C = Coluna/Estante</code> ·
                <code style="background:transparent; font-weight:700;">L = Nível/Altura</code>
                — Exemplo: <strong>R01-C05-L3</strong> = Rua 01, Coluna 05, Nível 3
            </div>

            {{-- Filters --}}
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:0.75rem; align-items:end;">
                <div class="form-group">
                    <label class="form-label">Rua / Corredor</label>
                    <select id="filter-aisle" class="form-select">
                        <option value="">Todas as Ruas</option>
                        @for($i=1; $i<=40; $i++)
                            <option value="R{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">Rua {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Busca por código</label>
                    <input type="text" id="location-search" placeholder="Ex: R01-C05" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Mostrar</label>
                    <select id="filter-status" class="form-select">
                        <option value="free">Apenas livres</option>
                        <option value="all">Todas as posições</option>
                    </select>
                </div>
                <button type="button" id="btn-search-locations" class="btn btn-primary" style="height:46px;">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>

            {{-- Results --}}
            <div style="min-height:200px; max-height:400px; overflow-y:auto; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-base); padding:1rem;">
                <div id="location-loading" style="display:none; text-align:center; padding:3rem;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size:2rem; color:var(--accent);"></i>
                    <p style="margin-top:1rem; color:var(--text-muted);">Carregando posições...</p>
                </div>
                <div id="location-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap:0.6rem;"></div>
                <div id="location-empty" style="text-align:center; padding:3rem; color:var(--text-muted);">
                    <i class="fa-solid fa-map-location-dot" style="font-size:2.5rem; margin-bottom:1rem; display:block;"></i>
                    <p style="margin:0;">Selecione uma rua ou pesquise para ver posições disponíveis.</p>
                </div>
            </div>

            {{-- Selected indicator --}}
            <div id="location-selected-bar" style="display:none; padding:0.75rem 1rem; background:var(--green-bg); border:1px solid var(--green); border-radius:var(--r-md); font-size:0.9rem; color:var(--green); font-weight:600;">
                <i class="fa-solid fa-check-circle"></i>
                Selecionado: <span id="location-selected-code"></span>
            </div>
        </div>
        <div class="modal-footer">
            <span style="flex:1; font-size:0.8rem; color:var(--text-muted);">
                <i class="fa-solid fa-info-circle"></i> Clique em uma posição verde para selecioná-la.
            </span>
            <button type="button" class="btn btn-secondary" id="cancel-location-picker">Fechar</button>
        </div>
    </div>
</div>

<style>
    .loc-card {
        padding: 0.65rem 0.5rem;
        border: 2px solid var(--border);
        border-radius: var(--r-sm);
        cursor: pointer;
        transition: all 0.2s;
        background: var(--bg-surface);
        text-align: center;
        position: relative;
    }
    .loc-card.loc-free {
        border-color: var(--green);
        background: var(--green-bg);
    }
    .loc-card.loc-free:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px var(--green-bg);
        border-color: var(--green);
    }
    .loc-card.loc-occupied {
        opacity: 0.45;
        cursor: not-allowed;
        border-color: var(--red);
        background: var(--red-bg);
    }
    .loc-card.loc-shared {
        opacity: 0.8;
        cursor: pointer;
        border-color: var(--orange);
        background: var(--orange-bg);
    }
    .loc-card.loc-shared:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--orange-bg);
    }
    .loc-card-code {
        font-family: 'Outfit', monospace;
        font-weight: 700;
        font-size: 0.85rem;
        display: block;
        color: var(--text-primary);
    }
    .loc-card-sub {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
    }
    .loc-card-occupant {
        font-size: 0.6rem;
        color: var(--red);
        margin-top: 0.2rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
</style>

@push('scripts')
<script>
(function() {
    const modal       = document.getElementById('location-picker-modal');
    const openBtn     = document.getElementById('btn-open-location-picker');
    const displayInp  = document.getElementById('warehouse_location_display');
    const idInput     = document.getElementById('warehouse_location_id');
    const aisleSelect = document.getElementById('filter-aisle');
    const searchInput = document.getElementById('location-search');
    const statusFilter= document.getElementById('filter-status');
    const searchBtn   = document.getElementById('btn-search-locations');
    const grid        = document.getElementById('location-grid');
    const loading     = document.getElementById('location-loading');
    const empty       = document.getElementById('location-empty');
    const selBar      = document.getElementById('location-selected-bar');
    const selCode     = document.getElementById('location-selected-code');

    if (!modal || !openBtn) return;

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
        if (grid.children.length === 0) fetchLocations();
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = 'auto';
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    }

    function selectLocation(id, code) {
        if (idInput) idInput.value = id;
        if (displayInp) displayInp.value = code;
        if (selBar) {
            selCode.textContent = code;
            selBar.style.display = 'block';
        }
        closeModal();
    }

    openBtn.addEventListener('click', openModal);
    if (displayInp) displayInp.addEventListener('click', openModal);

    document.getElementById('close-location-picker')?.addEventListener('click', closeModal);
    document.getElementById('cancel-location-picker')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    searchBtn?.addEventListener('click', fetchLocations);
    aisleSelect?.addEventListener('change', fetchLocations);
    searchInput?.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); fetchLocations(); }});

    function fetchLocations() {
        const aisle  = aisleSelect?.value || '';
        const q      = searchInput?.value || '';
        const status = statusFilter?.value || 'free';

        loading.style.display = 'block';
        empty.style.display   = 'none';
        grid.innerHTML        = '';

        let url = `{{ route('locations.search') }}?q=${encodeURIComponent(q)}&aisle=${encodeURIComponent(aisle)}`;
        if (status === 'free') url += '&free_only=1';

        fetch(url)
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';

                if (!data.length) {
                    empty.style.display = 'block';
                    return;
                }

                data.forEach(loc => {
                    const card = document.createElement('div');
                    let cls = 'loc-card';
                    let sub = '';

                    if (loc.is_occupied) {
                        cls += ' loc-occupied';
                        sub = `<span class="loc-card-occupant" title="${loc.occupant_name || ''}"><i class="fa-solid fa-lock" style="font-size:0.55rem;"></i> ${loc.occupant_name || 'Ocupado'}</span>`;
                    } else {
                        cls += ' loc-free';
                        sub = `<span class="loc-card-sub" style="color:var(--green);"><i class="fa-solid fa-check" style="font-size:0.55rem;"></i> Livre</span>`;
                    }

                    // Parse code into readable parts
                    const parts = loc.full_code.split('-');
                    const readable = parts.length === 3
                        ? `Rua ${parts[0].replace('R','')} · Col ${parts[1].replace('C','')} · Nv ${parts[2].replace('L','')}`
                        : '';

                    card.className = cls;
                    card.innerHTML = `
                        <span class="loc-card-code">${loc.full_code}</span>
                        <span class="loc-card-sub">${readable}</span>
                        ${sub}
                    `;

                    if (!loc.is_occupied) {
                        card.addEventListener('click', () => selectLocation(loc.id, loc.full_code));
                    }

                    grid.appendChild(card);
                });
            })
            .catch(() => {
                loading.style.display = 'none';
                empty.innerHTML = '<p style="color:var(--red);"><i class="fa-solid fa-exclamation-triangle"></i> Erro ao carregar posições.</p>';
                empty.style.display = 'block';
            });
    }
})();
</script>
@endpush
