<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório WMS - LogiSync</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .logo-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -1px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #475569;
            margin-top: 5px;
        }
        .meta-info {
            float: right;
            text-align: right;
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }
        .clearfix {
            clear: both;
        }
        .summaries {
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 6px;
        }
        .summary-item {
            display: inline-block;
            width: 23%;
            vertical-align: top;
        }
        .summary-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 2px solid #cbd5e1;
        }
        .report-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            background: #e2e8f0;
            color: #475569;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
    </style>
</head>
<body>

    <div class="header">
        <div class="meta-info">
            <strong>Gerado em:</strong> {{ date('d/m/Y H:i:s') }}<br>
            <strong>Operador:</strong> {{ auth()->user()->name }}
        </div>
        <div class="logo-title">LogiSync WMS</div>
        @php
            $reportTitles = [
                'stock_position' => 'Posição de Estoque Atual',
                'stock_movement' => 'Histórico de Movimentação de Estoque',
                'billing'        => 'Relatório de Faturamento e NF-e',
                'low_stock'      => 'Produtos em Estoque Crítico',
            ];
            $title = $reportTitles[$reportType] ?? 'Relatório WMS';
        @endphp
        <div class="report-title">{{ $title }}</div>
        <div class="clearfix"></div>
    </div>

    @if(count($summaries) > 0)
        <div class="summaries">
            @foreach($summaries as $label => $value)
                <div class="summary-item">
                    <div class="summary-label">{{ $label }}</div>
                    <div class="summary-value">{{ $value }}</div>
                </div>
            @endforeach
            <div class="clearfix"></div>
        </div>
    @endif

    <table class="report-table">
        @if($reportType === 'stock_position')
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>SKU</th>
                    <th>Cód. Barras</th>
                    <th>Categoria</th>
                    <th>Endereço</th>
                    <th class="text-right">Qtd</th>
                    <th class="text-right">Custo Unit.</th>
                    <th class="text-right">Venda Unit.</th>
                    <th class="text-right">Custo Total</th>
                    <th class="text-right">Venda Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $p)
                    <tr>
                        <td><strong>{{ $p->name }}</strong></td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->barcode ?? 'N/A' }}</td>
                        <td>{{ $p->category ?? 'Sem Categoria' }}</td>
                        <td>{{ $p->location ? $p->location->full_code : 'Não alocado' }}</td>
                        <td class="text-right"><strong>{{ number_format($p->quantity, 0, ',', '.') }}</strong></td>
                        <td class="text-right">R$ {{ number_format($p->cost_price ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($p->selling_price ?? 0, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($p->quantity * ($p->cost_price ?? 0), 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($p->quantity * ($p->selling_price ?? 0), 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px;">Nenhum produto cadastrado no estoque.</td>
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
                    <th class="text-right">Quantidade</th>
                    <th>Operador</th>
                    <th>Ref / Doc</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $m)
                    <tr>
                        <td>{{ $m->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><strong>{{ $m->product ? $m->product->name : 'Produto Removido' }}</strong></td>
                        <td>{{ $m->product ? $m->product->sku : 'N/A' }}</td>
                        <td>
                            @if($m->type === 'entrada')
                                <span class="badge badge-green">Entrada</span>
                            @else
                                <span class="badge badge-red">Saída</span>
                            @endif
                        </td>
                        <td class="text-right"><strong>{{ number_format($m->quantity, 0, ',', '.') }}</strong></td>
                        <td>{{ $m->user ? $m->user->name : 'Sistema' }}</td>
                        <td>{{ $m->reference ?? 'N/A' }}</td>
                        <td>{{ $m->notes ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px;">Nenhuma movimentação registrada no período.</td>
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
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Desconto</th>
                    <th class="text-right">Frete</th>
                    <th class="text-right">Total Geral</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $inv)
                    <tr>
                        <td><strong>{{ $inv->number }}</strong></td>
                        <td>{{ $inv->series }}</td>
                        <td>{{ $inv->recipient_name }}</td>
                        <td>{{ $inv->recipient_document }}</td>
                        <td>{{ $inv->issued_at ? $inv->issued_at->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-right">R$ {{ number_format($inv->subtotal, 2, ',', '.') }}</td>
                        <td class="text-right" style="color: #ef4444;">-R$ {{ number_format($inv->discount, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($inv->shipping, 2, ',', '.') }}</td>
                        <td class="text-right"><strong>R$ {{ number_format($inv->total, 2, ',', '.') }}</strong></td>
                        <td class="text-center">
                            @if($inv->status === 'concluída')
                                <span class="badge badge-green">Concluída</span>
                            @elseif($inv->status === 'emitida')
                                <span class="badge badge-blue">Emitida</span>
                            @elseif($inv->status === 'cancelada')
                                <span class="badge badge-red">Cancelada</span>
                            @else
                                <span class="badge">Rascunho</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 20px;">Nenhuma nota fiscal encontrada.</td>
                    </tr>
                @endforelse
            </tbody>

        @elseif($reportType === 'low_stock')
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>SKU</th>
                    <th class="text-right">Estoque Atual</th>
                    <th class="text-right">Nível de Alerta</th>
                    <th class="text-right">Estoque Máximo</th>
                    <th class="text-right">Quantidade Faltante</th>
                    <th>Localização</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $p)
                    <tr>
                        <td><strong>{{ $p->name }}</strong></td>
                        <td>{{ $p->sku }}</td>
                        <td class="text-right" style="color: #ef4444;"><strong>{{ number_format($p->quantity, 0, ',', '.') }}</strong></td>
                        <td class="text-right">{{ number_format($p->reorder_level ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($p->max_stock ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right" style="color: #f97316;"><strong>{{ number_format(max(0, ($p->max_stock ?? 0) - $p->quantity), 0, ',', '.') }}</strong></td>
                        <td>{{ $p->location ? $p->location->full_code : 'Não alocado' }}</td>
                        <td>
                            @if($p->quantity == 0)
                                <span class="badge badge-red">Sem Estoque</span>
                            @else
                                <span class="badge badge-orange">Estoque Crítico</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #10b981;">Nenhum produto em estoque crítico.</td>
                    </tr>
                @endforelse
            </tbody>
        @endif
    </table>

</body>
</html>
