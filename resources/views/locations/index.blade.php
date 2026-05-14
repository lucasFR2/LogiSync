@extends('layouts.app')

@section('title', 'Localizações de Estoque')
@section('page-title', 'Gestão de Endereçamento')
@section('page-subtitle', 'Controle o mapeamento físico do seu armazém')

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
        <div class="flex gap-3">
            <button onclick="document.getElementById('modal-bulk').style.display='flex'" class="btn btn-secondary">
                <i class="fa-solid fa-layer-group"></i> Gerador em Lote
            </button>
            <button onclick="document.getElementById('modal-single').style.display='flex'" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Localização
            </button>
        </div>
    </div>

    {{-- Locations Table --}}
    <div class="card overflow-hidden">
        <div class="table-wrap" style="border:none;">
            <table class="table-stack">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Corredor</th>
                        <th>Coluna</th>
                        <th>Nível</th>
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
                        <td colspan="7">
                            <div class="empty-state" style="padding:5rem 2rem; text-align:center;">
                                <i class="fa-solid fa-map-pin" style="font-size:3rem; color:var(--text-muted); margin-bottom:1.5rem;"></i>
                                <h3>Nenhuma localização cadastrada</h3>
                                <p>Crie localizações manualmente ou use o gerador em lote.</p>
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
                    <label class="form-label">Corredor (Aisle)</label>
                    <input type="text" name="aisle" class="form-control" placeholder="Ex: A01" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Coluna (Column)</label>
                    <input type="text" name="column" class="form-control" placeholder="Ex: 05" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nível (Level)</label>
                    <input type="text" name="level" class="form-control" placeholder="Ex: N3" required>
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="button" onclick="document.getElementById('modal-single').style.display='none'" class="btn btn-secondary w-full">Cancelar</button>
                <button type="submit" class="btn btn-primary w-full">Criar Endereço</button>
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
@endsection
