@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral inteligente das operações LogiSync')

@push('styles')
<style>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }
    .dashboard-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.5rem;
    }
    .dashboard-hero,
    .dashboard-charts,
    .dashboard-bottom {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 1.5rem;
        align-items: stretch;
    }
    .dashboard-charts .dashboard-chart-wide {
        grid-column: 1;
    }
    .dashboard-welcome {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .dashboard-welcome-bg {
        position: absolute;
        right: -10px;
        top: -10px;
        font-size: 10rem;
        opacity: 0.06;
        pointer-events: none;
        color: var(--accent);
    }
    .dashboard-chart-box {
        height: 280px;
        position: relative;
    }
    .dashboard-actions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .dashboard-log-item {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border);
    }
    .dashboard-log-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    @media (max-width: 1200px) {
        .dashboard-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 900px) {
        .dashboard-kpis,
        .dashboard-hero,
        .dashboard-charts,
        .dashboard-bottom {
            grid-template-columns: 1fr;
        }
        .dashboard-charts .dashboard-chart-wide {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="anim-entrance dashboard-page">

    @unless(Auth::user()->hasRole('rh'))
    {{-- KPIs --}}
    <div class="dashboard-kpis">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-subtle); color:var(--accent);">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div class="stat-label">Estoque Total</div>
            <div class="stat-value">{{ number_format($totalStock, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--orange-bg); color:var(--orange);">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="stat-label">NF-e Pendentes</div>
            <div class="stat-value">{{ $pendingOrders }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--red-bg); color:var(--red);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-label">Estoque Baixo</div>
            <div class="stat-value">{{ $lowStockCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-bg); color:var(--blue);">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div class="stat-label">Ocupação WMS</div>
            <div class="stat-value">{{ $occupancyRate }}%</div>
        </div>
    </div>

    {{-- Boas-vindas + ocupação --}}
    <div class="dashboard-hero">
        <div class="card dashboard-welcome">
            <div style="position:relative; z-index:1;">
                <h2 style="font-family:'Outfit',sans-serif; font-size:1.65rem; margin:0 0 0.5rem; color:var(--text-primary);">
                    Bem-vindo, {{ explode(' ', Auth::user()->name)[0] }}!
                </h2>
                <p style="margin:0; font-size:0.95rem; line-height:1.6; color:var(--text-secondary); max-width:520px;">
                    O armazém está com <strong style="color:var(--accent);">{{ $occupancyRate }}% de ocupação</strong>
                    ({{ $occupiedLocations }} de {{ $totalLocations }} posições).
                    @if($pendingOrders > 0)
                        Há <strong style="color:var(--orange);">{{ $pendingOrders }}</strong> nota(s) em rascunho aguardando emissão.
                    @else
                        Nenhuma nota pendente no momento.
                    @endif
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-top:1.5rem;">
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Nova Entrada
                    </a>
                    @can('notas_fiscais.emitir')
                    <a href="{{ route('invoices.create') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-file-invoice"></i> Emitir NF-e
                    </a>
                    @endcan
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-box"></i> Ver Produtos
                    </a>
                </div>
            </div>
            <div class="dashboard-welcome-bg"><i class="fa-solid fa-warehouse"></i></div>
        </div>

        <div class="card" style="padding:1.5rem; display:flex; flex-direction:column; justify-content:space-between;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Ocupação do Armazém</span>
                    <div style="font-size:2rem; font-weight:800; font-family:'Outfit',sans-serif; color:var(--accent); margin-top:0.25rem;">{{ $occupancyRate }}%</div>
                </div>
                <div style="width:44px; height:44px; background:var(--blue-bg); color:var(--blue); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div style="margin-top:1.25rem; background:var(--bg-hover); height:10px; border-radius:6px; overflow:hidden;">
                <div style="width:{{ min($occupancyRate, 100) }}%; height:100%; background:var(--blue); border-radius:6px; transition:width 0.4s ease;"></div>
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.85rem; display:flex; justify-content:space-between; align-items:center;">
                <span>{{ $occupiedLocations }} / {{ $totalLocations }} posições</span>
                <span style="color:var(--green); font-weight:700;"><i class="fa-solid fa-circle-check"></i> Sincronizado</span>
            </div>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary" style="margin-top:1.25rem; width:100%; justify-content:center;">
                <i class="fa-solid fa-map"></i> Gerenciar Localizações
            </a>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="dashboard-charts">
        <div class="card dashboard-chart-wide" style="padding:1.5rem;">
            <div class="card-header" style="padding:0 0 1.25rem; border:none; background:transparent;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:10px; height:24px; background:var(--blue); border-radius:4px;"></div>
                    <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:1.1rem;">Fluxo de Movimentação (7 dias)</h3>
                </div>
                <span style="font-size:0.75rem; font-weight:700; padding:0.25rem 0.65rem; border-radius:999px; background:var(--blue-bg); color:var(--blue);">Sincronizado</span>
            </div>
            <div class="dashboard-chart-box">
                <canvas id="flowChart"></canvas>
            </div>
        </div>

        <div class="card" style="padding:1.5rem;">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                <div style="width:10px; height:24px; background:var(--orange); border-radius:4px;"></div>
                <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:1.1rem;">Mix de Categorias</h3>
            </div>
            <div class="dashboard-chart-box">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
    @endunless

    {{-- Atividade + ações --}}
    <div class="dashboard-bottom">
        @can('logs.visualizar')
        <div class="card" style="padding:1.5rem;">
            <div class="card-header" style="padding:0 0 1rem; border:none; background:transparent;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                    <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:1.1rem;">Atividade Recente</h3>
                </div>
                <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">Ver todos</a>
            </div>
            @if($recentLogs->count() > 0)
                <div style="max-height:280px; overflow-y:auto;">
                    @foreach($recentLogs->take(6) as $log)
                        <div class="dashboard-log-item">
                            <div style="width:36px; height:36px; border-radius:10px; background:var(--bg-hover); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--text-muted);">
                                <i class="fa-solid fa-clock-rotate-left" style="font-size:0.85rem;"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:0.875rem; color:var(--text-primary);">{{ Str::limit($log->description ?? $log->action ?? 'Registro', 80) }}</div>
                                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.15rem;">
                                    {{ $log->user?->name ?? 'Sistema' }} · {{ $log->created_at?->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin:0; color:var(--text-muted); font-size:0.875rem;">Nenhuma atividade registrada recentemente.</p>
            @endif
        </div>
        @else
        <div class="card" style="padding:1.5rem;">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:1.1rem;">Acesso Rápido</h3>
            </div>
            <p style="margin:0; color:var(--text-muted); font-size:0.875rem; line-height:1.5;">
                Use o menu lateral para navegar entre módulos do WMS. Registre entradas, gerencie produtos e emita notas fiscais.
            </p>
        </div>
        @endcan

        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card" style="padding:1.5rem;">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                    <div style="width:10px; height:24px; background:var(--green); border-radius:4px;"></div>
                    <h3 style="margin:0; font-family:'Outfit',sans-serif; font-size:1.1rem;">Ações Inteligentes</h3>
                </div>
                <div class="dashboard-actions-list">
                    <a href="{{ route('products.labels.select') }}" class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem;">
                        <i class="fa-solid fa-barcode" style="color:var(--blue);"></i> Gerar Etiquetas em Lote
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem;">
                        <i class="fa-solid fa-boxes-packing" style="color:var(--orange);"></i> Consultar Catálogo
                    </a>
                    @unless(Auth::user()->hasRole('rh'))
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem;">
                        <i class="fa-solid fa-map-location-dot" style="color:var(--green);"></i> Mapa de Localizações
                    </a>
                    @endunless
                </div>
            </div>

            <div class="card" style="padding:1.25rem; background:var(--accent-subtle); border:1px dashed var(--accent);">
                <div style="display:flex; gap:1rem; align-items:center;">
                    <div style="width:48px; height:48px; background:var(--accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:0.9rem;">Precisa de ajuda?</div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.15rem;">Suporte técnico disponível para sua operação.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @unless(Auth::user()->hasRole('rh'))
    const flowEl = document.getElementById('flowChart');
    if (flowEl) {
        new Chart(flowEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [
                    {
                        label: 'Entradas (Produtos)',
                        data: @json($entriesData ?? []),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Saídas (Notas)',
                        data: @json($exitsData ?? []),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const catEl = document.getElementById('categoryChart');
    if (catEl) {
        new Chart(catEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($categoriesStats->pluck('category') ?? []),
                datasets: [{
                    data: @json($categoriesStats->pluck('count') ?? []),
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#94A3B8', '#6366F1', '#8B5CF6'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
    @endunless
});
</script>
@endpush
@endsection
