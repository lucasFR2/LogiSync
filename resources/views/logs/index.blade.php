@extends('layouts.app')

@section('title', 'Logs do Sistema')
@section('page-title', 'Logs do Sistema')
@section('page-subtitle', 'Rastreabilidade completa de ações realizadas no sistema')

@section('content')
<div class="anim-entrance">

    {{-- Filter Form --}}
    <div class="card" style="margin-bottom:1.5rem; padding:1.5rem;">
        <form action="{{ route('logs.index') }}" method="GET" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; align-items:flex-end;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Usuário</label>
                <select name="user" class="form-select">
                    <option value="">Todos os usuários</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label">Ação</label>
                <select name="action" class="form-select">
                    <option value="">Todas as ações</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ strtoupper($act) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;">
                <label class="form-label">Data</label>
                <input type="date" name="date" class="form-input" value="{{ request('date') }}">
            </div>

            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('logs.index') }}" class="btn btn-secondary" title="Limpar Filtros">
                    <i class="fa-solid fa-eraser"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Histórico de Atividades</h3>
            </div>
            <div style="font-size:0.875rem; color:var(--text-muted);">
                Total: {{ $logs->total() }} registros
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                        <th>IP / Navegador</th>
                        <th>Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div style="width:36px; height:36px; background:var(--bg-hover); border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--accent);">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;">{{ $log->user->name ?? 'Sistema' }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">{{ $log->user->role ?? 'Processo Automático' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $color = match($log->action) {
                                        'login' => 'var(--blue)',
                                        'register_user', 'self_register' => 'var(--accent)',
                                        'update_user' => 'var(--orange)',
                                        'delete_user' => 'var(--red)',
                                        default => 'var(--text-primary)'
                                    };
                                @endphp
                                <span class="badge" style="background:var(--bg-hover); color:{{ $color }}; border:1px solid {{ $color }}44; font-weight:700; font-size:0.65rem; text-transform:uppercase;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td style="font-size:0.875rem; max-width:300px;">
                                {{ $log->description }}
                            </td>
                            <td>
                                <div style="font-size:0.75rem; font-weight:600; color:var(--text-secondary);">{{ $log->ip_address }}</div>
                                <div style="font-size:0.7rem; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </div>
                            </td>
                            <td style="font-size:0.85rem; font-weight:500;">
                                {{ $log->created_at->format('d/m/Y') }}
                                <div style="font-size:0.75rem; color:var(--text-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Estilo básico para paginação Laravel com LogiSync design */
    .pagination { display: flex; gap: 0.5rem; list-style: none; padding: 0; }
    .page-item .page-link { 
        padding: 0.5rem 1rem; 
        border-radius: 8px; 
        background: var(--bg-surface); 
        border: 1px solid var(--border); 
        color: var(--text-primary); 
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }
    .page-item.active .page-link { background: var(--accent); color: var(--accent-fg); border-color: var(--accent); }
    .page-item:hover .page-link:not(.active) { background: var(--bg-hover); }
</style>
@endsection
