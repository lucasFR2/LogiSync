@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral das movimentações do armazém')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2.5rem;">

    {{-- Welcome Card --}}
    <div class="card" style="background: linear-gradient(135deg, #0F172A, #1E293B); color: white; border: none; padding: 2.5rem; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2;">
            <h2 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.5rem;">Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
            <p style="opacity: 0.8; font-size: 1.1rem; max-width: 600px;">
                @if(Auth::user()->role === 'Recursos Humanos (RH)')
                    Bem-vindo ao painel de Gestão de Pessoal. Aqui você pode gerenciar os dados dos funcionários e realizar novos cadastros.
                @else
                    O sistema LogiSync está operando normalmente. Você tem <strong>{{ $pendingOrders ?? 0 }}</strong> pedidos aguardando processamento hoje.
                @endif
            </p>
            <div class="flex" style="margin-top: 1.5rem; gap: 1rem;">
                @if(Auth::user()->role === 'Administrador' || Auth::user()->role === 'Recursos Humanos (RH)')
                    <a href="{{ route('register') }}" class="btn" style="background: var(--accent); color: var(--accent-fg);">
                        <i class="fa-solid fa-user-plus"></i> Novo Funcionário
                    </a>
                @endif
                
                @if(Auth::user()->role !== 'Recursos Humanos (RH)')
                    <a href="{{ route('inventory.index') }}" class="btn" style="background: #FFFFFF; color: #0F172A;">
                        <i class="fa-solid fa-plus"></i> Nova Entrada
                    </a>
                @endif
            </div>
        </div>
        {{-- Decorative element --}}
        <div style="position: absolute; right: -50px; bottom: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; right: 20px; top: 20px; font-size: 8rem; opacity: 0.05; pointer-events: none;">
            <i class="fa-solid @if(Auth::user()->role === 'Recursos Humanos (RH)') fa-users-gear @else fa-warehouse @endif"></i>
        </div>
    </div>

    @if(Auth::user()->role !== 'Recursos Humanos (RH)')
        {{-- Stat Cards for Logistics --}}
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
            {{-- Total em Estoque --}}
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--blue-bg); color: var(--blue);">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="stat-label">Total em Estoque</div>
                <div class="stat-value">{{ $totalStock ?? 0 }}</div>
                <div class="badge badge-success" style="width: fit-content; margin-top: 0.5rem;">
                    <i class="fa-solid fa-check"></i> Sincronizado
                </div>
            </div>

            {{-- Pedidos Pendentes --}}
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--orange-bg); color: var(--orange);">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-label">Pedidos Pendentes</div>
                <div class="stat-value">{{ $pendingOrders ?? 0 }}</div>
                <div class="badge badge-warning" style="width: fit-content; margin-top: 0.5rem;">
                    <i class="fa-solid fa-hourglass-half"></i> Pendente
                </div>
            </div>

            {{-- Usuário --}}
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--accent-subtle); color: var(--accent);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="stat-label">Acesso Logístico</div>
                <div class="stat-value" style="font-size: 1.5rem;">{{ Auth::user()->role }}</div>
            </div>
        </div>
    @endif

    @if(Auth::user()->role === 'Administrador' || Auth::user()->role === 'Recursos Humanos (RH)')
        {{-- Quick Actions for RH/Admin --}}
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div class="card" style="padding: 2rem; display: flex; align-items: center; gap: 1.5rem; transition: transform 0.2s;">
                <div style="width: 56px; height: 56px; background: var(--accent-subtle); color: var(--accent); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-family: 'Outfit'; margin: 0 0 0.25rem 0;">Gestão de Pessoal</h4>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin: 0;">Gerencie dados, cargos e documentos dos funcionários.</p>
                </div>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Acessar</a>
            </div>

            <div class="card" style="padding: 2rem; display: flex; align-items: center; gap: 1.5rem; transition: transform 0.2s;">
                <div style="width: 56px; height: 56px; background: var(--blue-bg); color: var(--blue); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="font-family: 'Outfit'; margin: 0 0 0.25rem 0;">Novo Cadastro</h4>
                    <p style="font-size: 0.875rem; color: var(--text-muted); margin: 0;">Registre um novo colaborador no sistema.</p>
                </div>
                <a href="{{ route('register') }}" class="btn btn-secondary btn-sm">Cadastrar</a>
            </div>
        </div>
    @endif

    @if(Auth::user()->role !== 'Recursos Humanos (RH)')
        {{-- Recent Activity Table for Logistics --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                    <h3 style="margin:0;">Últimas Movimentações</h3>
                </div>
            </div>
            <div class="table-wrap" style="border:none; box-shadow:none;">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Data</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state" style="padding: 3rem 2rem;">
                                    <i class="fa-solid fa-folder-open" style="font-size:2rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                                    <p style="color:var(--text-muted); margin:0;">Nenhuma movimentação recente encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(Auth::user()->role === 'Administrador' && isset($recentLogs))
        {{-- Recent Activity Logs for Admin --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                    <h3 style="margin:0;">Logs Recentes do Sistema</h3>
                </div>
                <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">
                    Ver Todos <i class="fa-solid fa-list-ul" style="margin-left:0.5rem;"></i>
                </a>
            </div>
            <div class="table-wrap" style="border:none; box-shadow:none;">
                <table>
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Descrição</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight:600;">{{ $log->user->name ?? 'Sistema' }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $log->user->role ?? '-' }}</div>
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
                                    <span style="font-weight:700; color:{{ $color }}; text-transform:uppercase; font-size:0.7rem;">{{ $log->action }}</span>
                                </td>
                                <td style="font-size:0.875rem;">{{ $log->description }}</td>
                                <td style="font-size:0.8rem; color:var(--text-muted);">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state" style="padding: 2rem;">
                                        <p style="color:var(--text-muted); margin:0;">Nenhum log registrado ainda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
