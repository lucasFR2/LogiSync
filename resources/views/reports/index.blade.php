@extends('layouts.app')

@section('title', 'Relatórios WMS')
@section('page-title', 'Relatórios Operacionais')
@section('page-subtitle', 'Painel central de relatórios de estoque, movimentações e faturamento.')

@push('styles')
<style>
    .reports-page {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        width: 100%;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.5rem;
    }
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 2rem;
    }
    .report-card {
        transition: all 0.3s var(--spring);
        border: 1px solid var(--border);
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--border-strong);
    }
    .report-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="anim-entrance reports-page">
    
    {{-- High-Level Operational Stats --}}
    <div class="stats-grid">
        {{-- Total Stock Cost Value --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-subtle); color:var(--accent);">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div class="stat-label">Valor de Custo em Estoque</div>
            <div class="stat-value" style="font-size: 1.5rem;">R$ {{ number_format($stats['total_cost_value'], 2, ',', '.') }}</div>
        </div>

        {{-- Total Stock Selling Value --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-bg); color:var(--blue);">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="stat-label">Valor Estimado de Venda</div>
            <div class="stat-value" style="font-size: 1.5rem;">R$ {{ number_format($stats['total_selling_value'], 2, ',', '.') }}</div>
        </div>

        {{-- Total Items Count --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-bg); color:var(--green);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-label">Volume Total de Itens</div>
            <div class="stat-value">{{ number_format($stats['total_items_in_stock'], 0, ',', '.') }} un</div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--red-bg); color:var(--red);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-label">Produtos em Estoque Crítico</div>
            <div class="stat-value">{{ $stats['low_stock_alerts'] }}</div>
        </div>
    </div>

    {{-- Reports Selection Cards --}}
    <div class="reports-grid">
        
        {{-- 1. Posição de Estoque --}}
        <div class="card report-card p-6">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                <div class="report-card-icon" style="background:var(--accent-subtle); color:var(--accent);">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit'; font-weight:700; font-size:1.2rem; color:var(--text-primary);">Posição de Estoque Atual</h3>
                    <p style="font-size:0.8rem; color:var(--text-muted);">Listagem detalhada dos itens atualmente disponíveis e suas localizações.</p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="GET" target="_blank" style="display:flex; flex-direction:column; gap:1rem;">
                <input type="hidden" name="report_type" value="stock_position">

                <div class="grid grid-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="category" class="form-input">
                            <option value="">Todas</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Localização</label>
                        <select name="location_id" class="form-input">
                            <option value="">Todas</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->full_code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:0.5rem; justify-content: flex-end;">
                    <button type="submit" name="export" value="csv" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-csv"></i> CSV
                    </button>
                    <button type="submit" name="export" value="pdf" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Visualizar
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. Movimentação de Estoque --}}
        <div class="card report-card p-6">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                <div class="report-card-icon" style="background:var(--blue-bg); color:var(--blue);">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit'; font-weight:700; font-size:1.2rem; color:var(--text-primary);">Movimentação de Estoque</h3>
                    <p style="font-size:0.8rem; color:var(--text-muted);">Extrato de fluxos de entrada e saída em um período selecionado.</p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="GET" target="_blank" style="display:flex; flex-direction:column; gap:1rem;">
                <input type="hidden" name="report_type" value="stock_movement">

                <div class="grid grid-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Data de Início</label>
                        <input type="date" name="start_date" class="form-input" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data de Fim</label>
                        <input type="date" name="end_date" class="form-input" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de Movimentação</label>
                    <select name="type" class="form-input">
                        <option value="">Todas (Entradas e Saídas)</option>
                        <option value="entrada">Entradas</option>
                        <option value="saida">Saídas</option>
                    </select>
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:0.5rem; justify-content: flex-end;">
                    <button type="submit" name="export" value="csv" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-csv"></i> CSV
                    </button>
                    <button type="submit" name="export" value="pdf" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Visualizar
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. Faturamento e Notas Fiscais --}}
        <div class="card report-card p-6">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                <div class="report-card-icon" style="background:var(--green-bg); color:var(--green);">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit'; font-weight:700; font-size:1.2rem; color:var(--text-primary);">Faturamento e NF-e</h3>
                    <p style="font-size:0.8rem; color:var(--text-muted);">Relatório consolidado de notas fiscais emitidas no período.</p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="GET" target="_blank" style="display:flex; flex-direction:column; gap:1rem;">
                <input type="hidden" name="report_type" value="billing">

                <div class="grid grid-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Data de Início</label>
                        <input type="date" name="start_date" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Data de Fim</label>
                        <input type="date" name="end_date" class="form-input" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status da Nota</label>
                    <select name="status" class="form-input">
                        <option value="">Todos</option>
                        <option value="emitida">Emitida</option>
                        <option value="concluída">Concluída</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:0.5rem; justify-content: flex-end;">
                    <button type="submit" name="export" value="csv" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-csv"></i> CSV
                    </button>
                    <button type="submit" name="export" value="pdf" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Visualizar
                    </button>
                </div>
            </form>
        </div>

        {{-- 4. Alerta de Estoque Crítico --}}
        <div class="card report-card p-6">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                <div class="report-card-icon" style="background:var(--red-bg); color:var(--red);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 style="font-family:'Outfit'; font-weight:700; font-size:1.2rem; color:var(--text-primary);">Alerta de Estoque Crítico</h3>
                    <p style="font-size:0.8rem; color:var(--text-muted);">Produtos que atingiram ou estão abaixo do ponto de ressuprimento.</p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="GET" target="_blank" style="display:flex; flex-direction:column; gap:1rem; height:calc(100% - 70px); justify-content:space-between;">
                <input type="hidden" name="report_type" value="low_stock">

                <div style="background:var(--red-bg); padding:1rem; border-radius:var(--r-md); border:1px solid rgba(239, 68, 68, 0.2); font-size:0.85rem; color:var(--text-secondary); line-height:1.4;">
                    <i class="fa-solid fa-circle-info" style="color:var(--red); margin-right:4px;"></i> 
                    Este relatório lista de forma dinâmica e priorizada todos os produtos cujo estoque físico é menor ou igual ao ponto de ressuprimento configurado, ideal para tomada de decisões de compra.
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:1.5rem; justify-content: flex-end;">
                    <button type="submit" name="export" value="csv" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-csv"></i> CSV
                    </button>
                    <button type="submit" name="export" value="pdf" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Visualizar
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
