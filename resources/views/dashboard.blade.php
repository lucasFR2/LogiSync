@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral das movimentações do armazém')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2.5rem;">

    {{-- Welcome Card --}}
    <div class="card" style="background: linear-gradient(135deg, #0F172A, #1E293B); color: white; border: none; padding: 2.5rem; position: relative; overflow: hidden; backdrop-filter: none; -webkit-backdrop-filter: none;">
        <div style="position: relative; z-index: 2;">
            <h2 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.5rem;">Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
            <p style="opacity: 0.8; font-size: 1.1rem; max-width: 600px;">
                O sistema LogiSync está operando normalmente. Você tem <strong>{{ $pendingOrders ?? 0 }}</strong> pedidos aguardando processamento hoje.
            </p>
            <div class="flex" style="margin-top: 1.5rem; gap: 1rem;">
                <a href="{{ route('inventory.index') }}" class="btn" style="background: #FFFFFF; color: #0F172A;">
                    <i class="fa-solid fa-plus"></i> Nova Entrada
                </a>
                <a href="{{ route('products.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.25);">
                    Gerenciar Produtos
                </a>
            </div>
        </div>
        {{-- Decorative element --}}
        <div style="position: absolute; right: -50px; bottom: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; pointer-events: none;"></div>
        <div class="anim-float" style="position: absolute; right: 40px; top: 40px; font-size: 8rem; opacity: 0.1; pointer-events: none; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.4));">
            <i class="fa-solid fa-warehouse"></i>
        </div>
    </div>

    {{-- Stat Cards --}}
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

        {{-- Produtos em Alerta --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--red-bg); color: var(--red);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-label">Produtos em Alerta</div>
            <div class="stat-value" style="color: var(--red);">{{ $lowStockCount ?? 0 }}</div>
            <div class="badge badge-danger" style="width: fit-content; margin-top: 0.5rem;">
                <i class="fa-solid fa-arrow-down"></i> Estoque Baixo
            </div>
        </div>

        {{-- Usuário --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent-subtle); color: var(--accent);">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-label">Acesso</div>
            <div class="stat-value" style="font-size: 1.5rem;">{{ ucfirst(Auth::user()->role) }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: 0.5rem;">
                ID: {{ Auth::user()->id }} · CPF: {{ Auth::user()->cpf }}
            </div>
        </div>

        {{-- Simulador Quick Card --}}
        <div class="stat-card" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none;">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="fa-solid fa-vial"></i>
            </div>
            <div class="stat-label" style="color: rgba(255,255,255,0.8);">Simulador</div>
            <div class="stat-value" style="font-size: 1.25rem; color: white;">Simular Saída (NF-e)</div>
            <a href="{{ route('invoices.create', ['simulate' => 1]) }}" class="btn" style="background: white; color: #4f46e5; border: none; font-size: 0.75rem; padding: 0.5rem; margin-top: 0.5rem; justify-content: center;">
                <i class="fa-solid fa-play"></i> Iniciar Simulação
            </a>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Últimas Movimentações</h3>
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                Ver Todas <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem;"></i>
            </a>
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
                            <div class="empty-state" style="padding: 5rem 2rem;">
                                <div class="anim-float" style="width:100px; height:100px; background:var(--bg-hover); border-radius:24px; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem; box-shadow: var(--shadow-md); transform: rotate(-5deg);">
                                    <i class="fa-solid fa-folder-open" style="font-size:2.5rem; color:var(--text-muted);"></i>
                                </div>
                                <h4 style="font-family:'Outfit'; font-size:1.25rem;">Nenhuma movimentação recente</h4>
                                <p style="color:var(--text-muted); margin-bottom:1.5rem;">As novas entradas de mercadorias aparecerão listadas nesta tabela.</p>
                                <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Registrar Primeira Entrada
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
