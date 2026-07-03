@extends('layouts.app')

@section('title', 'Localizações de Estoque')
@section('page-title', 'Gestão de Endereçamento')
@section('page-subtitle', 'Controle o mapeamento físico do seu armazém')

@push('styles')
<style>
    .loc-card {
        padding: 0.8rem 0.5rem;
        border: 2px solid var(--border);
        border-radius: var(--r-sm);
        cursor: pointer;
        background: var(--bg-surface);
        text-align: center;
        width: 100%;
        transition: all 0.2s ease;
    }
    .loc-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent);
    }
    .loc-card.loc-free {
        border-color: var(--green);
        background: var(--green-bg);
    }
    .loc-card.loc-occupied {
        border-color: var(--red);
        background: var(--red-bg);
    }
    .loc-card-code {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--accent);
        display: block;
    }
    .loc-card-sub {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
        display: block;
    }
    .loc-card-occupant {
        font-size: 0.65rem;
        color: var(--red);
        margin-top: 0.25rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="anim-entrance">

    {{-- Feedback Alerts --}}
    @if(session('success'))
        <div class="alert alert-success mb-6">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-6">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="card p-6 mb-8 flex flex-mobile-col justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <div style="width:48px; height:48px; background:var(--blue-bg); color:var(--blue); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <h3 class="m-0">Mapa do Armazém</h3>
                <p class="text-sm text-muted m-0">Gerencie corredores, colunas e níveis.</p>
            </div>
        </div>

        {{-- Search Input --}}
        <div style="flex:1; max-width:300px; margin:0 1rem;">
            <form method="GET" action="{{ route('locations.index') }}">
                <div style="position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="Buscar localização..."
                           class="form-input" style="padding-left:2.5rem; width:100%;">
                </div>
            </form>
        </div>

        <div class="flex gap-3 items-center flex-wrap">
            <div class="flex mr-2" style="border: 1px solid var(--border); padding: 2px; border-radius: var(--r-md); background: var(--bg-base);">
                <button type="button" id="btn-view-table" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600;">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
                <button type="button" id="btn-view-visual" class="btn btn-sm btn-secondary" style="padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600; border: none; background: transparent; color: var(--text-muted);">
                    <i class="fa-solid fa-table-cells"></i> Visual
                </button>
            </div>

            @if($search)
                <a href="{{ route('locations.index') }}" class="btn btn-secondary" title="Limpar busca">
                    <i class="fa-solid fa-times"></i>
                </a>
            @endif
            <button onclick="document.getElementById('modal-bulk').style.display='flex'" class="btn btn-secondary">
                <i class="fa-solid fa-layer-group"></i> Gerador em Lote
            </button>
            <button onclick="document.getElementById('modal-single').style.display='flex'" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Localização
            </button>
        </div>
    </div>

    {{-- Locations Table Container --}}
    <div id="view-container-table">
        <div class="card overflow-hidden">
        <div class="table-wrap" style="border:none;">
            <table class="table-stack">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Corredor</th>
                        <th>Coluna</th>
                        <th>Nível</th>
                        <th>Dimensões (LxAxP)</th>
                        <th>Peso Máx.</th>
                        <th>Status</th>
                        <th>Produtos</th>
                        <th style="text-align:right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $loc)
                    <tr>
                        <td>
                            <div style="font-family:'Outfit'; font-weight:800; color:var(--accent);">{{ $loc->full_code }}</div>
                        </td>
                        <td>{{ $loc->aisle }}</td>
                        <td>{{ $loc->column }}</td>
                        <td>{{ $loc->level }}</td>
                        <td>
                            @if($loc->width || $loc->height || $loc->depth)
                                <span style="font-weight:500;">{{ number_format($loc->width ?? 0, 2, ',', '.') }}m x {{ number_format($loc->height ?? 0, 2, ',', '.') }}m x {{ number_format($loc->depth ?? 0, 2, ',', '.') }}m</span>
                            @else
                                <span class="text-muted text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            @if($loc->max_weight)
                                <span style="font-weight:600;">{{ number_format($loc->max_weight, 0, ',', '.') }} kg</span>
                            @else
                                <span class="text-muted text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            @if($loc->is_occupied)
                                <span class="badge badge-warning">Ocupado</span>
                            @else
                                <span class="badge badge-success">Livre</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span style="font-weight:600;">{{ $loc->products_count }}</span>
                                <span class="text-xs text-muted">SKUs</span>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                                <button class="btn btn-secondary btn-sm edit-location-btn" style="border-color:transparent;"
                                        data-id="{{ $loc->id }}"
                                        data-aisle="{{ $loc->aisle }}"
                                        data-column="{{ $loc->column }}"
                                        data-level="{{ $loc->level }}"
                                        data-width="{{ $loc->width }}"
                                        data-height="{{ $loc->height }}"
                                        data-depth="{{ $loc->depth }}"
                                        data-max-weight="{{ $loc->max_weight }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Excluir esta localização?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--red); border-color:transparent;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state" style="padding:5rem 2rem; text-align:center;">
                                <i class="fa-solid fa-map-pin" style="font-size:3rem; color:var(--text-muted); margin-bottom:1.5rem;"></i>
                                @if($search)
                                    <h3>Nenhuma localização encontrada</h3>
                                    <p>Nenhum resultado corresponde à busca "{{ $search }}".</p>
                                    <a href="{{ route('locations.index') }}" class="btn btn-secondary mt-4">Limpar busca</a>
                                @else
                                    <h3>Nenhuma localização cadastrada</h3>
                                    <p>Crie localizações manualmente ou use o gerador em lote.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($locations->hasPages())
            <div class="p-6 bg-hover">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div> {{-- Closes view-container-table --}}

{{-- Visual Layout --}}
<div id="view-container-visual" style="display:none; flex-direction:column; gap:1.5rem;">
    
    {{-- Warehouse Occupancy Statistics --}}
    <div class="card p-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-center shadow-md">
        <div>
            <h4 class="text-sm font-bold text-muted m-0 uppercase" style="letter-spacing: 0.05em;">Status de Ocupação</h4>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-black" id="stat-occupancy-rate">0%</span>
                <span class="text-xs text-muted">do espaço ocupado</span>
            </div>
            <div class="w-full bg-base rounded-full h-2 mt-3 overflow-hidden" style="background: var(--bg-base); height: 8px; border-radius: 4px;">
                <div id="stat-progress-bar" class="h-full bg-accent" style="width: 0%; height: 100%; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); background: var(--accent);"></div>
            </div>
        </div>
        
        <div style="border-left: 1px solid var(--border); padding-left: 1.5rem;" class="md:col-span-1">
            <span class="text-xs text-muted font-bold uppercase" style="letter-spacing: 0.05em;">Posições Totais</span>
            <h3 class="text-2xl font-black mt-1 m-0" id="stat-total-slots">0</h3>
            <span class="text-xs text-muted mt-1 block">Endereços cadastrados</span>
        </div>

        <div style="border-left: 1px solid var(--border); padding-left: 1.5rem;">
            <span class="text-xs text-muted font-bold uppercase" style="letter-spacing: 0.05em; color: var(--green);">Posições Livres</span>
            <h3 class="text-2xl font-black mt-1 m-0" style="color: var(--green);" id="stat-free-slots">0</h3>
            <span class="text-xs text-muted mt-1 block">Prontas para alocação</span>
        </div>

        <div style="border-left: 1px solid var(--border); padding-left: 1.5rem;">
            <span class="text-xs text-muted font-bold uppercase" style="letter-spacing: 0.05em; color: var(--red);">Posições Ocupadas</span>
            <h3 class="text-2xl font-black mt-1 m-0" style="color: var(--red);" id="stat-occupied-slots">0</h3>
            <span class="text-xs text-muted mt-1 block">Com estoque ativo</span>
        </div>
    </div>

    {{-- Interactive Dashboard Map Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Left Control Sidebar: Filters and Aisle Selector --}}
        <div class="lg:col-span-1 flex flex-col gap-4">
            
            {{-- Quick Filter Cards --}}
            <div class="card p-5 flex flex-col gap-4">
                <h4 class="font-bold text-sm m-0 border-b pb-2 mb-2" style="border-bottom: 1px solid var(--border); font-family: 'Outfit';">Filtros de Visão</h4>
                
                <div class="form-group">
                    <label class="form-label" style="font-size:0.75rem;">Status</label>
                    <select id="visual-filter-status" class="form-control" style="height: 40px; font-size: 0.85rem;">
                        <option value="all">Todas as posições</option>
                        <option value="free">Apenas livres</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size:0.75rem;">Filtro rápido (Cód. completo)</label>
                    <input type="text" id="visual-location-search" class="form-control" style="height: 40px; font-size: 0.85rem;" placeholder="Ex: R01-05-N2">
                </div>

                <button type="button" id="visual-btn-search" class="btn btn-primary w-full mt-2" style="height: 42px; justify-content: center; font-size:0.85rem;">
                    <i class="fa-solid fa-sync mr-1"></i> Atualizar Mapa
                </button>
            </div>

            {{-- Corridor Selector Panel --}}
            <div class="card p-5">
                <h4 class="font-bold text-sm m-0 border-b pb-2 mb-3" style="border-bottom: 1px solid var(--border); font-family: 'Outfit';">Corredores (Aisle)</h4>
                <div class="flex flex-col gap-2" id="visual-aisle-tabs" style="max-height: 250px; overflow-y: auto; padding-right: 0.25rem;">
                    {{-- Populated dynamically --}}
                    <span class="text-xs text-muted py-4 text-center">Nenhum corredor carregado.</span>
                </div>
            </div>
        </div>

        {{-- Right Main Content: Visual Rack Matrix --}}
        <div class="lg:col-span-3 flex flex-col gap-4">
            <div class="card p-6 flex flex-col gap-4 min-h-[400px]">
                
                <div class="flex justify-between items-center border-b pb-3" style="border-bottom: 1px solid var(--border);">
                    <div class="flex items-center gap-3">
                        <div style="width:10px; height:10px; border-radius:50%; background:var(--accent); animation: pulse 2s infinite;"></div>
                        <h4 class="font-black m-0" style="font-family:'Outfit'; font-size:1.1rem;" id="visual-current-aisle-title">Selecione um Corredor</h4>
                    </div>
                    
                    {{-- Legend --}}
                    <div class="flex gap-4 text-xs font-semibold">
                        <span class="flex items-center gap-1"><span style="display:inline-block; width:12px; height:12px; border-radius:3px; background:#f0fdf4; border: 1.5px solid var(--green);"></span> Livre</span>
                        <span class="flex items-center gap-1"><span style="display:inline-block; width:12px; height:12px; border-radius:3px; background:#fef2f2; border: 1.5px solid var(--red);"></span> Ocupada</span>
                    </div>
                </div>

                {{-- Visual Map Rendering Area --}}
                <div style="position:relative; width: 100%;">
                    
                    {{-- Loader --}}
                    <div id="visual-loading" style="display:none; text-align:center; padding: 5rem 0;" class="text-muted">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size:2.5rem; color:var(--accent);"></i>
                        <p style="margin-top:1rem; font-weight: 600;">Processando mapa físico...</p>
                    </div>

                    {{-- Empty state --}}
                    <div id="visual-empty" style="text-align:center; padding: 5rem 0;" class="text-muted">
                        <i class="fa-solid fa-warehouse" style="font-size:3rem; color:var(--text-muted); margin-bottom: 1rem;"></i>
                        <p style="margin:0; font-weight: 600;">Nenhum corredor selecionado ou dados indisponíveis.</p>
                        <p style="font-size: 0.8rem; margin-top: 0.25rem;">Use os filtros à esquerda para iniciar.</p>
                    </div>

                    {{-- Interactive Matrix Wrapper --}}
                    <div id="visual-grid-wrapper" style="display: none; overflow: auto; max-height: 580px; width: 100%; max-width: 100%; padding-bottom: 1rem;">
                        <div id="visual-matrix-grid" style="display: grid; gap: 8px; width: 100%;">
                            {{-- Dynamically populated via CSS Grid --}}
                        </div>
                    </div>
                    {{-- Pagination Controls --}}
                    <div id="visual-matrix-pagination" class="flex justify-between items-center mt-3 pt-3" style="border-top: 1px solid var(--border); display: none; gap: 1rem; flex-wrap: wrap;">
                        {{-- Dynamically populated --}}
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>
</div>

{{-- Modal: Single Creation --}}
<div id="modal-single" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:100; align-items:center; justify-content:center;">
    <div class="card p-8 anim-entrance" style="width:100%; max-width:500px;">
        <div class="flex justify-between items-center mb-6">
            <h3 class="m-0">Nova Localização</h3>
            <button onclick="document.getElementById('modal-single').style.display='none'" class="btn-close">&times;</button>
        </div>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div class="form-group">
                    <label class="form-label">Corredor (Aisle) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="aisle" class="form-control" placeholder="Ex: A01" required style="height:44px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Coluna (Column) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="column" class="form-control" placeholder="Ex: 05" required style="height:44px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Nível (Level) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="level" class="form-control" placeholder="Ex: N3" required style="height:44px;">
                </div>
                
                <div style="border-top: 1px solid var(--border); padding-top:1rem; margin-top:0.5rem;">
                    <span style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:0.75rem;">Dimensões e Capacidade</span>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="form-group">
                            <label class="form-label">Largura (m)</label>
                            <input type="number" name="width" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Altura (m)</label>
                            <input type="number" name="height" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Profund. (m)</label>
                            <input type="number" name="depth" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label class="form-label">Peso Máximo (kg)</label>
                        <input type="number" name="max_weight" step="0.1" min="0" class="form-control" placeholder="Ex: 1500" style="height:42px;">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="button" onclick="document.getElementById('modal-single').style.display='none'" class="btn btn-secondary w-full">Cancelar</button>
                <button type="submit" class="btn btn-primary w-full">Criar Endereço</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Location --}}
<div id="modal-edit" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:100; align-items:center; justify-content:center;">
    <div class="card p-8 anim-entrance" style="width:100%; max-width:500px;">
        <div class="flex justify-between items-center mb-6">
            <h3 class="m-0">Editar Localização</h3>
            <button onclick="document.getElementById('modal-edit').style.display='none'" class="btn-close">&times;</button>
        </div>
        <form id="edit-location-form" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-4">
                <div class="form-group">
                    <label class="form-label">Corredor (Aisle) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="aisle" id="edit-aisle" class="form-control" placeholder="Ex: A01" required style="height:44px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Coluna (Column) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="column" id="edit-column" class="form-control" placeholder="Ex: 05" required style="height:44px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Nível (Level) <span style="color:var(--red);">*</span></label>
                    <input type="text" name="level" id="edit-level" class="form-control" placeholder="Ex: N3" required style="height:44px;">
                </div>
                
                <div style="border-top: 1px solid var(--border); padding-top:1rem; margin-top:0.5rem;">
                    <span style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:0.75rem;">Dimensões e Capacidade</span>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="form-group">
                            <label class="form-label">Largura (m)</label>
                            <input type="number" name="width" id="edit-width" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Altura (m)</label>
                            <input type="number" name="height" id="edit-height" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Profund. (m)</label>
                            <input type="number" name="depth" id="edit-depth" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label class="form-label">Peso Máximo (kg)</label>
                        <input type="number" name="max_weight" id="edit-max-weight" step="0.1" min="0" class="form-control" placeholder="Ex: 1500" style="height:42px;">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="button" onclick="document.getElementById('modal-edit').style.display='none'" class="btn btn-secondary w-full">Cancelar</button>
                <button type="submit" class="btn btn-primary w-full">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Bulk Generator --}}
<div id="modal-bulk" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:100; align-items:center; justify-content:center;">
    <div class="card p-8 anim-entrance" style="width:100%; max-width:600px;">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-wand-magic-sparkles" style="color:var(--blue);"></i>
                <h3 class="m-0">Gerador Automático</h3>
            </div>
            <button onclick="document.getElementById('modal-bulk').style.display='none'" class="btn-close">&times;</button>
        </div>
        <p class="text-sm text-muted mb-6">Gere centenas de endereços instantaneamente definindo os limites do seu armazém.</p>
        
        <form action="{{ route('locations.generate') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group col-span-2">
                    <label class="form-label">Prefixo dos Corredores</label>
                    <input type="text" name="prefix" class="form-control" value="R" placeholder="Ex: R (Resultará em R01, R02...)" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Qtd. Corredores</label>
                    <input type="number" name="aisles_count" class="form-control" value="5" min="1" max="50">
                </div>
                <div class="form-group">
                    <label class="form-label">Qtd. Colunas por Corredor</label>
                    <input type="number" name="columns_count" class="form-control" value="10" min="1" max="50">
                </div>
                <div class="form-group col-span-2">
                    <label class="form-label">Níveis por Coluna (Altura)</label>
                    <input type="number" name="levels_count" class="form-control" value="4" min="1" max="10">
                </div>
                
                <div class="col-span-2" style="border-top: 1px solid var(--border); padding-top:1rem; margin-top:0.5rem;">
                    <span style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:0.75rem;">Dimensões Padrão para Lote</span>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="form-group">
                            <label class="form-label">Largura (m)</label>
                            <input type="number" name="width" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Altura (m)</label>
                            <input type="number" name="height" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Profund. (m)</label>
                            <input type="number" name="depth" step="0.01" min="0" class="form-control" placeholder="0,00" style="height:40px; padding:0 0.5rem;">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label class="form-label">Peso Máx. Padrão (kg)</label>
                        <input type="number" name="max_weight" step="0.1" min="0" class="form-control" placeholder="Ex: 1500" style="height:42px;">
                    </div>
                </div>
            </div>
            
            <div class="bg-hover p-4 rounded-md mt-6 mb-8 text-xs text-muted border-dashed border">
                <i class="fa-solid fa-info-circle mr-1"></i> Isso gerará endereços no formato <strong>{Prefixo}{Corredor}-{Coluna}-N{Nível}</strong>. Ex: R01-05-N2.
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modal-bulk').style.display='none'" class="btn btn-secondary w-full">Cancelar</button>
                <button type="submit" class="btn btn-primary w-full">Gerar Endereços</button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        cursor: pointer;
        line-height: 1;
    }
    .btn-close:hover { color: var(--text-primary); }
</style>

@push('scripts')
<script>
    // Helper function to open the Edit Modal with values populated
    function openEditModal(loc) {
        document.getElementById('edit-location-form').action = `/locations/${loc.id}`;
        document.getElementById('edit-aisle').value = loc.aisle || '';
        document.getElementById('edit-column').value = loc.column || '';
        document.getElementById('edit-level').value = loc.level || '';
        document.getElementById('edit-width').value = loc.width ? parseFloat(loc.width) : '';
        document.getElementById('edit-height').value = loc.height ? parseFloat(loc.height) : '';
        document.getElementById('edit-depth').value = loc.depth ? parseFloat(loc.depth) : '';
        document.getElementById('edit-max-weight').value = loc.max_weight ? parseFloat(loc.max_weight) : '';

        document.getElementById('modal-edit').style.display = 'flex';
    }

    // Attach listener to table edit buttons
    document.querySelectorAll('.edit-location-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const loc = {
                id: this.getAttribute('data-id'),
                aisle: this.getAttribute('data-aisle'),
                column: this.getAttribute('data-column'),
                level: this.getAttribute('data-level'),
                width: this.getAttribute('data-width'),
                height: this.getAttribute('data-height'),
                depth: this.getAttribute('data-depth'),
                max_weight: this.getAttribute('data-max-weight')
            };
            openEditModal(loc);
        });
    });

    // View Switcher logic
    const btnTable = document.getElementById('btn-view-table');
    const btnVisual = document.getElementById('btn-view-visual');
    const containerTable = document.getElementById('view-container-table');
    const containerVisual = document.getElementById('view-container-visual');

    function setActiveView(view) {
        if (view === 'table') {
            containerTable.style.display = 'block';
            containerVisual.style.display = 'none';

            btnTable.className = 'btn btn-sm btn-primary';
            btnTable.style.cssText = 'padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600;';
            
            btnVisual.className = 'btn btn-sm btn-secondary';
            btnVisual.style.cssText = 'padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600; border: none; background: transparent; color: var(--text-muted);';
        } else {
            containerTable.style.display = 'none';
            containerVisual.style.display = 'flex';

            btnVisual.className = 'btn btn-sm btn-primary';
            btnVisual.style.cssText = 'padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600;';

            btnTable.className = 'btn btn-sm btn-secondary';
            btnTable.style.cssText = 'padding: 0.35rem 0.75rem; border-radius: var(--r-sm); font-size: 0.8rem; font-weight:600; border: none; background: transparent; color: var(--text-muted);';
            
            fetchVisualLocations();
        }
    }

    btnTable.addEventListener('click', () => setActiveView('table'));
    btnVisual.addEventListener('click', () => setActiveView('visual'));

    // Visual Visualizer Search and Render Logic
    const visualSearch    = document.getElementById('visual-location-search');
    const visualStatus    = document.getElementById('visual-filter-status');
    const visualSearchBtn = document.getElementById('visual-btn-search');
    const visualLoading   = document.getElementById('visual-loading');
    const visualEmpty     = document.getElementById('visual-empty');
    const visualGridWrapper = document.getElementById('visual-grid-wrapper');
    const visualAisleTabs = document.getElementById('visual-aisle-tabs');
    const visualCurrentAisleTitle = document.getElementById('visual-current-aisle-title');
    const visualMatrixGrid = document.getElementById('visual-matrix-grid');
    const visualMatrixPagination = document.getElementById('visual-matrix-pagination');

    let allFetchedLocations = [];
    let currentSelectedAisle = null;
    let visualCurrentPage = 1;
    const colsPerPage = 10;

    function showVisualState(state) {
        visualLoading.style.display = state === 'loading' ? 'block' : 'none';
        visualEmpty.style.display   = state === 'empty'   ? 'block' : 'none';
        visualGridWrapper.style.display = state === 'grid' ? 'block' : 'none';
    }

    function buildVisualSearchUrl() {
        const params = new URLSearchParams();
        const q      = (visualSearch.value || '').trim();
        const status = visualStatus.value;

        if (q) params.set('q', q);
        if (status === 'free') params.set('free_only', '1');

        return `{{ route('locations.search') }}?${params.toString()}`;
    }

    function updateVisualStats(locations) {
        const total = locations.length;
        const occupied = locations.filter(l => l.is_occupied).length;
        const free = total - occupied;
        const rate = total > 0 ? Math.round((occupied / total) * 100) : 0;

        document.getElementById('stat-total-slots').textContent = total;
        document.getElementById('stat-free-slots').textContent = free;
        document.getElementById('stat-occupied-slots').textContent = occupied;
        document.getElementById('stat-occupancy-rate').textContent = rate + '%';
        document.getElementById('stat-progress-bar').style.width = rate + '%';
    }

    function renderAisleTabs(groupedLocations) {
        visualAisleTabs.innerHTML = '';
        const aisles = Object.keys(groupedLocations).sort();

        if (aisles.length === 0) {
            visualAisleTabs.innerHTML = '<span class="text-xs text-muted py-4 text-center">Nenhum corredor correspondente.</span>';
            return;
        }

        aisles.forEach(aisle => {
            const count = groupedLocations[aisle].length;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = aisle === currentSelectedAisle 
                ? 'btn btn-sm btn-primary w-full text-left' 
                : 'btn btn-sm btn-secondary w-full text-left';
            btn.style.cssText = aisle === currentSelectedAisle 
                ? 'padding: 0.6rem 0.8rem; border-radius: var(--r-md); font-weight:700; background: var(--accent); color: var(--accent-fg); border-color: var(--accent);' 
                : 'padding: 0.6rem 0.8rem; border-radius: var(--r-md); background: transparent; color: var(--text-primary); border-color: var(--border);';
            btn.innerHTML = `<i class="fa-solid fa-road mr-1"></i> Corredor <b>${aisle}</b> <span class="badge badge-secondary float-right" style="font-size: 0.65rem; padding: 0.15rem 0.35rem; float:right;">${count}</span>`;
            
            btn.addEventListener('click', () => {
                currentSelectedAisle = aisle;
                visualCurrentPage = 1; // Reset to page 1 on corridor click
                renderAisleTabs(groupedLocations);
                renderAisleMatrix(aisle, groupedLocations[aisle]);
            });

            visualAisleTabs.appendChild(btn);
        });

        if (!currentSelectedAisle && aisles.length > 0) {
            currentSelectedAisle = aisles[0];
            visualCurrentPage = 1;
            renderAisleTabs(groupedLocations);
            renderAisleMatrix(currentSelectedAisle, groupedLocations[currentSelectedAisle]);
        }
    }

    function renderAisleMatrix(aisleName, locations) {
        visualCurrentAisleTitle.innerHTML = `<i class="fa-solid fa-warehouse mr-1" style="color:var(--accent);"></i> Corredor <b>${aisleName}</b>`;
        visualMatrixGrid.innerHTML = '';
        visualMatrixPagination.innerHTML = '';
        visualMatrixPagination.style.display = 'none';

        // Extract and sort unique columns and levels
        const columns = [...new Set(locations.map(l => l.column).filter(c => c !== null && c !== undefined))].sort((a, b) => String(a).localeCompare(String(b), undefined, {numeric: true}));
        const levels = [...new Set(locations.map(l => l.level).filter(l => l !== null && l !== undefined))].sort((a, b) => String(b).localeCompare(String(a), undefined, {numeric: true}));

        if (columns.length === 0 || levels.length === 0) {
            showVisualState('empty');
            return;
        }

        // Paginate Columns
        const totalPages = Math.ceil(columns.length / colsPerPage);
        if (visualCurrentPage > totalPages) {
            visualCurrentPage = totalPages;
        }
        if (visualCurrentPage < 1) {
            visualCurrentPage = 1;
        }

        const pageColumns = columns.slice((visualCurrentPage - 1) * colsPerPage, visualCurrentPage * colsPerPage);

        // Apply grid column styling (1fr makes them distribute equally)
        visualMatrixGrid.style.gridTemplateColumns = `80px repeat(${pageColumns.length}, 1fr)`;

        // 1. Build Top Headers
        // Corner empty cell (Sticky left and top)
        const cornerHeader = document.createElement('div');
        cornerHeader.style.cssText = 'height: 40px; text-align: center; border: none; background: var(--bg-surface); font-size: 0.7rem; color: var(--text-muted); font-weight: 800; position: sticky; top: 0; left: 0; z-index: 12; display: flex; align-items: center; justify-content: center; border-right: 2px solid var(--border); border-bottom: 2px solid var(--border);';
        cornerHeader.textContent = 'NÍVEL';
        visualMatrixGrid.appendChild(cornerHeader);

        // Column headers (Sticky top)
        pageColumns.forEach(col => {
            const colHeader = document.createElement('div');
            colHeader.style.cssText = 'height: 40px; text-align: center; border: none; background: var(--bg-surface); font-size: 0.75rem; color: var(--text-secondary); font-weight: 800; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: center; border-bottom: 2px solid var(--border);';
            colHeader.innerHTML = `<i class="fa-solid fa-grip-vertical mr-1" style="font-size:0.65rem;"></i>Col. ${col}`;
            visualMatrixGrid.appendChild(colHeader);
        });

        // 2. Build rows per level
        levels.forEach(lvl => {
            // Level Label Column (Sticky left)
            const lvlLabel = document.createElement('div');
            lvlLabel.style.cssText = 'height: 85px; text-align: center; border: none; background: var(--bg-surface); font-weight: 800; font-size: 0.75rem; color: var(--accent); font-family: "Outfit"; position: sticky; left: 0; z-index: 9; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 2px solid var(--border);';
            lvlLabel.innerHTML = `<span style="display:block; font-size:0.55rem; color:var(--text-muted); font-weight:700;">NÍVEL</span>${lvl}`;
            visualMatrixGrid.appendChild(lvlLabel);

            // Columns within this row
            pageColumns.forEach(col => {
                const cellWrapper = document.createElement('div');
                cellWrapper.style.cssText = 'height: 85px; padding: 4px; display: flex; align-items: center; justify-content: center;';

                const loc = locations.find(l => l.column === col && l.level === lvl);
                if (loc) {
                    const card = document.createElement('button');
                    card.type = 'button';
                    card.style.cssText = 'width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.2rem; padding: 0.5rem 0.25rem; border-radius: var(--r-md); cursor: pointer; transition: all 0.2s ease; border: 1.5px solid; text-align: center; overflow: hidden;';

                    let bg = '#f0fdf4';
                    let border = 'var(--green)';
                    let color = '#15803d';
                    let labelStatus = '<i class="fa-solid fa-box-open mr-1"></i> Livre';
                    let occupantName = `<span style="font-size:0.6rem; color:var(--text-muted); font-weight: 600;">${parseFloat(loc.width)}x${parseFloat(loc.height)}x${parseFloat(loc.depth)}m</span>`;

                    if (loc.is_occupied) {
                        bg = '#fef2f2';
                        border = 'var(--red)';
                        color = '#991b1b';
                        labelStatus = '<i class="fa-solid fa-box mr-1"></i> Ocupado';
                        occupantName = `<span style="font-size:0.6rem; font-weight: 700; color:var(--red); display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;" title="${loc.occupant_name || ''}">${loc.occupant_name || 'Estoque'}</span>`;
                    }

                    card.style.background = bg;
                    card.style.borderColor = border;
                    card.style.color = color;

                    card.addEventListener('mouseenter', () => {
                        card.style.transform = 'translateY(-2px)';
                        card.style.boxShadow = 'var(--shadow-md)';
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = 'translateY(0)';
                        card.style.boxShadow = 'none';
                    });

                    card.innerHTML = `
                        <span style="font-family:'Outfit'; font-weight:800; font-size:0.75rem; display:block; white-space:nowrap;">${loc.full_code}</span>
                        <span style="font-size: 0.55rem; font-weight: 700; text-transform: uppercase; opacity: 0.95; white-space:nowrap;">${labelStatus}</span>
                        ${occupantName}
                    `;

                    card.addEventListener('click', () => {
                        openEditModal(loc);
                    });

                    cellWrapper.appendChild(card);
                } else {
                    cellWrapper.innerHTML = `<div style="width:100%; height: 100%; border: 1px dashed var(--border); border-radius: var(--r-md); background: transparent; opacity: 0.25;"></div>`;
                }

                visualMatrixGrid.appendChild(cellWrapper);
            });
        });

        // 3. Render Pagination Controls
        if (totalPages > 1) {
            visualMatrixPagination.style.display = 'flex';

            // Previous button
            const btnPrev = document.createElement('button');
            btnPrev.type = 'button';
            btnPrev.className = 'btn btn-secondary btn-sm';
            btnPrev.innerHTML = '<i class="fa-solid fa-chevron-left"></i> Anterior';
            btnPrev.style.cssText = 'padding: 0.4rem 0.8rem; font-size: 0.8rem;';
            if (visualCurrentPage === 1) {
                btnPrev.disabled = true;
                btnPrev.style.opacity = '0.5';
                btnPrev.style.cursor = 'not-allowed';
            } else {
                btnPrev.addEventListener('click', () => {
                    visualCurrentPage--;
                    renderAisleMatrix(aisleName, locations);
                });
            }

            // Info text
            const infoText = document.createElement('span');
            infoText.className = 'text-xs text-muted font-bold';
            infoText.style.fontFamily = 'Outfit';
            infoText.innerHTML = `Colunas ${pageColumns[0]} a ${pageColumns[pageColumns.length - 1]} &middot; Página ${visualCurrentPage} de ${totalPages}`;

            // Next button
            const btnNext = document.createElement('button');
            btnNext.type = 'button';
            btnNext.className = 'btn btn-secondary btn-sm';
            btnNext.innerHTML = 'Próximo <i class="fa-solid fa-chevron-right"></i>';
            btnNext.style.cssText = 'padding: 0.4rem 0.8rem; font-size: 0.8rem;';
            if (visualCurrentPage === totalPages) {
                btnNext.disabled = true;
                btnNext.style.opacity = '0.5';
                btnNext.style.cursor = 'not-allowed';
            } else {
                btnNext.addEventListener('click', () => {
                    visualCurrentPage++;
                    renderAisleMatrix(aisleName, locations);
                });
            }

            visualMatrixPagination.appendChild(btnPrev);
            visualMatrixPagination.appendChild(infoText);
            visualMatrixPagination.appendChild(btnNext);
        }

        showVisualState('grid');
    }

    function fetchVisualLocations() {
        showVisualState('loading');
        visualAisleTabs.innerHTML = '';
        visualMatrixGrid.innerHTML = '';
        fetch(buildVisualSearchUrl(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            allFetchedLocations = data;
            
            if (!Array.isArray(data) || data.length === 0) {
                visualEmpty.innerHTML = '<i class="fa-solid fa-inbox" style="font-size:2.5rem;color:var(--text-muted);"></i><p style="margin-top:0.75rem;">Nenhuma posição encontrada. Ajuste os filtros ou crie localizações.</p>';
                showVisualState('empty');
                updateVisualStats([]);
                return;
            }

            updateVisualStats(data);

            // Group by Aisle
            const grouped = {};
            data.forEach(loc => {
                const aisle = loc.aisle || 'SEM CORREDOR';
                if (!grouped[aisle]) grouped[aisle] = [];
                grouped[aisle].push(loc);
            });

            renderAisleTabs(grouped);
        })
        .catch(() => {
            visualEmpty.innerHTML = '<p style="margin:0;color:var(--red);"><i class="fa-solid fa-triangle-exclamation"></i> Erro ao carregar posições no mapa.</p>';
            showVisualState('empty');
        });
    }

    visualSearchBtn.addEventListener('click', () => {
        currentSelectedAisle = null; // reset selected corridor to default to first matches
        visualCurrentPage = 1;
        fetchVisualLocations();
    });
    visualStatus.addEventListener('change', () => {
        currentSelectedAisle = null;
        visualCurrentPage = 1;
        fetchVisualLocations();
    });
    [visualSearch].forEach(el => {
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                currentSelectedAisle = null;
                visualCurrentPage = 1;
                fetchVisualLocations();
            }
        });
    });
</script>
@endpush
@endsection
