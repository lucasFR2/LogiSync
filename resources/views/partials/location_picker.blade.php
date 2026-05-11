{{-- Warehouse Location Picker Modal --}}
<div id="location-picker-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(8px); padding:2rem; overflow-y:auto;">
    <div class="card anim-fade-up" style="max-width:900px; margin:0 auto; box-shadow:var(--shadow-2xl);">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <i class="fa-solid fa-warehouse" style="color:var(--accent); font-size:1.25rem;"></i>
                <h3 style="margin:0;">Mapa de Estocagem (14.000 Posições)</h3>
            </div>
            <button type="button" id="close-location-picker" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.5rem;">&times;</button>
        </div>
        <div class="card-body">
            <div class="grid grid-3" style="gap:1rem; margin-bottom:1.5rem;">
                <div class="form-group">
                    <label class="form-label">Rua / Corredor</label>
                    <select id="filter-aisle" class="form-select">
                        <option value="">Todas</option>
                        @for($i=1; $i<=40; $i++)
                            <option value="R{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">Rua {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Busca Rápida</label>
                    <input type="text" id="location-search" placeholder="Ex: R01-C05" class="form-input">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end;">
                    <button type="button" id="btn-search-locations" class="btn btn-primary" style="width:100%;">
                        <i class="fa-solid fa-search"></i> Pesquisar
                    </button>
                </div>
            </div>

            <div id="location-results-container" style="min-height:300px; max-height:500px; overflow-y:auto; border:1px solid var(--border); border-radius:var(--r-md); background:var(--bg-card); padding:1rem;">
                <div id="location-loading" style="display:none; text-align:center; padding:3rem;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size:2rem; color:var(--accent);"></i>
                    <p style="margin-top:1rem; color:var(--text-muted);">Consultando mapa do armazém...</p>
                </div>
                <div id="location-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap:0.75rem;">
                    {{-- Locations will be injected here --}}
                </div>
                <div id="location-empty" style="text-align:center; padding:3rem; color:var(--text-muted);">
                    <i class="fa-solid fa-info-circle" style="font-size:2rem; margin-bottom:1rem;"></i>
                    <p>Use os filtros para encontrar posições livres.</p>
                </div>
            </div>
        </div>
        <div class="card-footer" style="background:var(--bg-hover); padding:1rem; text-align:center; font-size:0.85rem; color:var(--text-muted);">
            <i class="fa-solid fa-info-circle"></i> Selecione uma posição disponível (Verde) para vincular ao produto.
        </div>
    </div>
</div>

<style>
    .location-card {
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: var(--r-sm);
        cursor: pointer;
        transition: 0.2s;
        background: var(--bg-card);
        text-align: center;
    }
    .location-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }
    .location-card.occupied {
        opacity: 0.5;
        cursor: not-allowed;
        background: var(--bg-hover);
    }
    .location-card-code {
        font-family: 'Outfit';
        font-weight: 700;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 0.25rem;
    }
    .location-card-info {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
    }
    .location-status {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }
    .status-free { background: var(--green); }
    .status-occupied { background: var(--red); }
</style>
