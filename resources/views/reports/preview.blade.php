@extends('layouts.app')

@php
    $reportTitles = [
        'stock_position' => 'Posição de Estoque Atual',
        'stock_movement' => 'Histórico de Movimentação de Estoque',
        'billing'        => 'Relatório de Faturamento e NF-e',
        'low_stock'      => 'Produtos em Estoque Crítico',
    ];
    $title = $reportTitles[$reportType] ?? 'Relatório WMS';
@endphp

@section('title', $title)
@section('page-title', $title)
@section('page-subtitle', 'Visualização interativa dos dados operacionais e financeiros.')

@push('styles')
<style>
    .preview-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .summaries-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
    }
    .report-table-card {
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.9rem;
    }
    .report-table th {
        background: var(--bg-hover);
        color: var(--text-primary);
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid var(--border);
    }
    .report-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-secondary);
    }
    .report-table tr:hover td {
        background: var(--bg-hover);
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .preview-page, .preview-page * {
            visibility: visible;
        }
        .preview-page {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            background: #fff !important;
            color: #000 !important;
        }
        .no-print {
            display: none !important;
        }
        .report-table-card {
            border: none;
            box-shadow: none;
        }
        .report-table th {
            background: #f1f5f9 !important;
            color: #000 !important;
            border-bottom: 2px solid #cbd5e1 !important;
        }
        .report-table td {
            border-bottom: 1px solid #e2e8f0 !important;
            color: #334155 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="anim-entrance preview-page">
    
    {{-- Header with action buttons --}}
    <div class="preview-header no-print">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-chevron-left"></i> Voltar ao Painel
        </a>
        
        <div style="display:flex; gap:0.5rem;">
            {{-- Export buttons, keeping existing query params but forcing export format --}}
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-file-csv"></i> Exportar CSV
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-secondary btn-sm" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
            </a>
            <button onclick="window.print();" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- Summaries Metrics --}}
    @if(count($summaries) > 0)
    <div class="summaries-grid">
        @foreach($summaries as $label => $value)
            <div class="stat-card">
                <div class="stat-label" style="text-transform:uppercase; font-size:0.7rem; font-weight:700; letter-spacing:0.05em;">{{ $label }}</div>
                <div class="stat-value" style="font-size:1.35rem; font-family:'Outfit'; margin-top:0.25rem;">{{ $value }}</div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Filters description --}}
    <div class="card p-4 no-print" style="border: 1px solid var(--border); font-size:0.85rem; color:var(--text-secondary); display:flex; gap:1.5rem; flex-wrap:wrap; background: var(--bg-hover);">
        <div><strong style="color:var(--text-primary);">Filtros Aplicados:</strong></div>
        @if(request()->filled('start_date'))
            <div><strong>Início:</strong> {{ date('d/m/Y', strtotime(request('start_date'))) }}</div>
        @endif
        @if(request()->filled('end_date'))
            <div><strong>Fim:</strong> {{ date('d/m/Y', strtotime(request('end_date'))) }}</div>
        @endif
        @if(request()->filled('category'))
            <div><strong>Categoria:</strong> {{ request('category') }}</div>
        @endif
        @if(request()->filled('location_id'))
            @php
                $locCode = \App\Models\WarehouseLocation::find(request('location_id'))?->full_code;
            @endphp
            <div><strong>Localização:</strong> {{ $locCode ?? 'Desconhecida' }}</div>
        @endif
        @if(request()->filled('type'))
            <div><strong>Tipo:</strong> {{ ucfirst(request('type')) }}</div>
        @endif
        @if(request()->filled('status'))
            <div><strong>Status:</strong> {{ ucfirst(request('status')) }}</div>
        @endif
        @if(!request()->filled('start_date') && !request()->filled('end_date') && !request()->filled('category') && !request()->filled('location_id') && !request()->filled('type') && !request()->filled('status'))
            <div>Nenhum filtro específico. Exibindo todos os registros.</div>
        @endif
    </div>

    {{-- Report Table --}}
    <div class="card report-table-card">
        <div style="overflow-x:auto;">
            <table class="report-table">
                @if($reportType === 'stock_position')
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th>Cód. Barras</th>
                            <th>Categoria</th>
                            <th>Endereço</th>
                            <th style="text-align:right;">Qtd</th>
                            <th style="text-align:right;">Custo Unit.</th>
                            <th style="text-align:right;">Venda Unit.</th>
                            <th style="text-align:right;">Custo Total</th>
                            <th style="text-align:right;">Venda Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $p)
                            <tr>
                                <td><strong style="color:var(--text-primary);">{{ $p->name }}</strong></td>
                                <td><code>{{ $p->sku }}</code></td>
                                <td>{{ $p->barcode ?? 'N/A' }}</td>
                                <td><span class="badge badge-secondary">{{ $p->category ?? 'Sem Categoria' }}</span></td>
                                <td>
                                    @if($p->location)
                                        <i class="fa-solid fa-map-location-dot" style="margin-right:4px;"></i>{{ $p->location->full_code }}
                                    @else
                                        <span style="color:var(--text-muted);">Não alocado</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:600; color:var(--text-primary);">{{ number_format($p->quantity, 0, ',', '.') }}</td>
                                <td style="text-align:right;">R$ {{ number_format($p->cost_price ?? 0, 2, ',', '.') }}</td>
                                <td style="text-align:right;">R$ {{ number_format($p->selling_price ?? 0, 2, ',', '.') }}</td>
                                <td style="text-align:right; font-weight:500;">R$ {{ number_format($p->quantity * ($p->cost_price ?? 0), 2, ',', '.') }}</td>
                                <td style="text-align:right; font-weight:500; color:var(--green);">R$ {{ number_format($p->quantity * ($p->selling_price ?? 0), 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align:center; padding:3rem; color:var(--text-muted);">Nenhum produto encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($reportType === 'stock_movement')
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th>Tipo</th>
                            <th style="text-align:right;">Quantidade</th>
                            <th>Operador</th>
                            <th>Ref / Doc</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $m)
                            <tr>
                                <td>{{ $m->created_at->format('d/m/Y H:i:s') }}</td>
                                <td><strong style="color:var(--text-primary);">{{ $m->product ? $m->product->name : 'Produto Removido' }}</strong></td>
                                <td><code>{{ $m->product ? $m->product->sku : 'N/A' }}</code></td>
                                <td>
                                    @if($m->type === 'entrada')
                                        <span class="badge" style="background:var(--green-bg); color:var(--green);"><i class="fa-solid fa-circle-arrow-down" style="margin-right:4px;"></i>Entrada</span>
                                    @else
                                        <span class="badge" style="background:var(--red-bg); color:var(--red);"><i class="fa-solid fa-circle-arrow-up" style="margin-right:4px;"></i>Saída</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:600; color:var(--text-primary);">{{ number_format($m->quantity, 0, ',', '.') }}</td>
                                <td>{{ $m->user ? $m->user->name : 'Sistema' }}</td>
                                <td>{{ $m->reference ?? 'N/A' }}</td>
                                <td style="font-size:0.8rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $m->notes }}">{{ $m->notes ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:3rem; color:var(--text-muted);">Nenhuma movimentação registrada no período.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($reportType === 'billing')
                    <thead>
                        <tr>
                            <th>Número NF</th>
                            <th>Série</th>
                            <th>Destinatário</th>
                            <th>CNPJ / CPF</th>
                            <th>Data Emissão</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th style="text-align:right;">Desconto</th>
                            <th style="text-align:right;">Frete</th>
                            <th style="text-align:right;">Total Geral</th>
                            <th style="text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $inv)
                            <tr>
                                <td><strong style="color:var(--text-primary);">{{ $inv->number }}</strong></td>
                                <td><code>{{ $inv->series }}</code></td>
                                <td>{{ $inv->recipient_name }}</td>
                                <td>{{ $inv->recipient_document }}</td>
                                <td>{{ $inv->issued_at ? $inv->issued_at->format('d/m/Y') : 'N/A' }}</td>
                                <td style="text-align:right;">R$ {{ number_format($inv->subtotal, 2, ',', '.') }}</td>
                                <td style="text-align:right; color:var(--red);">-R$ {{ number_format($inv->discount, 2, ',', '.') }}</td>
                                <td style="text-align:right;">R$ {{ number_format($inv->shipping, 2, ',', '.') }}</td>
                                <td style="text-align:right; font-weight:600; color:var(--text-primary);">R$ {{ number_format($inv->total, 2, ',', '.') }}</td>
                                <td style="text-align:center;">
                                    @if($inv->status === 'concluída')
                                        <span class="badge" style="background:var(--green-bg); color:var(--green);">Concluída</span>
                                    @elseif($inv->status === 'emitida')
                                        <span class="badge" style="background:var(--blue-bg); color:var(--blue);">Emitida</span>
                                    @elseif($inv->status === 'cancelada')
                                        <span class="badge" style="background:var(--red-bg); color:var(--red);">Cancelada</span>
                                    @else
                                        <span class="badge badge-secondary">Rascunho</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align:center; padding:3rem; color:var(--text-muted);">Nenhuma nota fiscal encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>

                @elseif($reportType === 'low_stock')
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th style="text-align:right;">Estoque Atual</th>
                            <th style="text-align:right;">Nível de Alerta</th>
                            <th style="text-align:right;">Estoque Máximo</th>
                            <th style="text-align:right;">Quantidade Faltante</th>
                            <th>Localização</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $p)
                            <tr>
                                <td><strong style="color:var(--text-primary);">{{ $p->name }}</strong></td>
                                <td><code>{{ $p->sku }}</code></td>
                                <td style="text-align:right; font-weight:700; color:var(--red);">{{ number_format($p->quantity, 0, ',', '.') }}</td>
                                <td style="text-align:right; font-weight:500;">{{ number_format($p->reorder_level ?? 0, 0, ',', '.') }}</td>
                                <td style="text-align:right;">{{ number_format($p->max_stock ?? 0, 0, ',', '.') }}</td>
                                <td style="text-align:right; color:var(--orange); font-weight:600;">{{ number_format(max(0, ($p->max_stock ?? 0) - $p->quantity), 0, ',', '.') }}</td>
                                <td>
                                    @if($p->location)
                                        <i class="fa-solid fa-location-dot" style="margin-right:4px;"></i>{{ $p->location->full_code }}
                                    @else
                                        <span style="color:var(--text-muted);">Não alocado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p->quantity == 0)
                                        <span class="badge" style="background:var(--red-bg); color:var(--red);">Sem Estoque</span>
                                    @else
                                        <span class="badge" style="background:var(--orange-bg); color:var(--orange);">Estoque Crítico</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding:3rem; color:var(--green);">Incrível! Nenhum produto está com estoque crítico.</td>
                            </tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
