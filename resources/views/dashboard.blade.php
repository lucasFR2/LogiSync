@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral inteligente das operações LogiSync')

@section('content')
<div class="anim-entrance flex flex-col gap-8">

    {{-- Top Section: Welcome & Global Stats --}}
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;" class="flex-mobile-col">
        {{-- Welcome Banner --}}
        <div class="card" style="background: linear-gradient(135deg, var(--accent), #1E293B); color: white; border: none; position: relative; overflow: hidden; display:flex; flex-direction:column; justify-content:center; padding:2rem;">
            <div style="position: relative; z-index: 2;">
                <h2 style="font-family:'Outfit'; font-size:1.75rem; margin-bottom:0.5rem;">Bem-vindo, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
                <p style="opacity: 0.8; font-size:1rem; max-width: 500px; line-height:1.6;">
                    O centro logístico está operando com <strong>85% de eficiência</strong> hoje. Você tem <strong>{{ $pendingOrders ?? 3 }}</strong> novas remessas aguardando triagem.
                </p>
                <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                    <a href="{{ route('inventory.index') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-weight: 700;">
                        <i class="fa-solid fa-plus"></i> Nova Entrada
                    </a>
                    <a href="{{ route('invoices.create') }}" class="btn" style="background:rgba(255,255,255,0.1); color:var(--text-primary); border:1px solid var(--border); backdrop-filter:blur(10px);">
                        <i class="fa-solid fa-file-invoice"></i> Emitir NF-e
                    </a>
                </div>
            </div>
            <div style="position: absolute; right: -20px; top: -20px; font-size: 12rem; opacity: 0.05; pointer-events: none;">
                <i class="fa-solid fa-warehouse"></i>
            </div>
        </div>

        {{-- Mini Analytics Card --}}
        <div class="card p-6" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Ocupação do Armazém</span>
                    <div style="font-size:1.75rem; font-weight:800; font-family:'Outfit'; color:var(--accent); margin-top:0.25rem;">{{ $occupancyRate }}%</div>
                </div>
                <div style="width:40px; height:40px; background:var(--blue-bg); color:var(--blue); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div style="margin-top:1rem; background:var(--bg-hover); height:8px; border-radius:4px; overflow:hidden;">
                <div style="width:{{ $occupancyRate }}%; height:100%; background:var(--blue); border-radius:4px;"></div>
            </div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.75rem; display:flex; justify-content:space-between;">
                <span>{{ $occupiedLocations }} / {{ $totalLocations }} posições</span>
                <span style="color:var(--green); font-weight:700;"><i class="fa-solid fa-caret-up"></i> Sincronizado</span>
            </div>
        </div>
    </div>

    {{-- Analytics Grid --}}
    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;" class="flex-mobile-col">
        {{-- Chart 1: Stock Flow --}}
        <div class="card p-6" style="grid-column: span 2;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 style="font-family:'Outfit'; font-size:1.1rem; margin:0; display:flex; align-items:center; gap:0.5rem;">
                    <i class="fa-solid fa-arrow-trend-up" style="color:var(--blue);"></i> Fluxo de Movimentação (7 dias)
                </h3>
                <div class="badge badge-info">Sincronizado</div>
            </div>
            <div style="height:250px; position:relative;">
                <canvas id="flowChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Category Distribution --}}
        <div class="card p-6">
            <h3 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-pie-chart" style="color:var(--orange);"></i> Mix de Categorias
            </h3>
            <div style="height:250px; position:relative; display:flex; align-items:center; justify-content:center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Activity & Shortcuts --}}
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem;" class="flex-mobile-col">
        {{-- Recent Activity --}}
        <div class="card overflow-hidden">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem;">
                <h3 style="font-family:'Outfit'; font-size:1.1rem; margin:0;">Atividade Recente</h3>
                <a href="{{ route('logs.index') }}" style="font-size:0.8rem; font-weight:700; color:var(--blue); text-decoration:none;">Ver Tudo</a>
            </div>
            <div class="table-wrap" style="border:none;">
                <table class="table-stack">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Usuário</th>
                            <th>Horário</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs ?? [] as $log)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div style="width:32px; height:32px; background:var(--accent-subtle); color:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.8rem;">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div style="font-weight:600; text-transform:capitalize;">{{ str_replace('_', ' ', $log->action) }}</div>
                                </div>
                            </td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);">{{ $log->user->name ?? 'Sistema' }}</td>
                            <td style="font-size:0.85rem; color:var(--text-muted);">{{ $log->created_at->diffForHumans() }}</td>
                            <td><span class="badge badge-info">Sincronizado</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-8 text-muted">Nenhuma atividade recente encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Controls --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div class="card p-6" style="background:var(--bg-elevated);">
                <h3 style="font-family:'Outfit'; font-size:1rem; margin-bottom:1.25rem;">Ações Inteligentes</h3>
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <a href="{{ route('products.labels.select') }}" class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem; border-color:var(--border);">
                        <i class="fa-solid fa-barcode" style="color:var(--blue);"></i> Gerar Etiquetas em Lote
                    </a>
                    <button class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem; border-color:var(--border);">
                        <i class="fa-solid fa-boxes-packing" style="color:var(--orange);"></i> Otimizar Picking
                    </button>
                    <button class="btn btn-secondary" style="width:100%; justify-content:flex-start; gap:0.75rem; border-color:var(--border);">
                        <i class="fa-solid fa-file-export" style="color:var(--green);"></i> Exportar Balanço Fiscal
                    </button>
                </div>
            </div>

            {{-- Support Card --}}
            <div class="card p-5" style="background:var(--accent-subtle); border-style:dashed;">
                <div style="display:flex; gap:1rem; align-items:center;">
                    <div style="width:48px; height:48px; background:var(--accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:700; font-size:0.9rem;">Precisa de Ajuda?</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">Suporte técnico 24/7 disponível.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Flow Chart
    const flowCtx = document.getElementById('flowChart').getContext('2d');
    new Chart(flowCtx, {
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
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });

    // Category Chart
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(catCtx, {
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
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush
@endsection

