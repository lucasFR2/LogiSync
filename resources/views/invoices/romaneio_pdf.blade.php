<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Romaneio de Entrega - NF {{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            size: a4 portrait;
            margin: 15px 18px 15px 18px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5px;
            color: #111;
            background: #fff;
            line-height: 1.25;
        }
        .romaneio-sheet {
            width: 100%;
            background: #fff;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 6px;
        }
        td, th {
            border: 0.8px solid #334155;
            padding: 4px 6px;
            vertical-align: top;
            background: transparent;
            color: #111;
        }
        table.no-top td, table.no-top th {
            border-top: none;
        }
        .box-title {
            font-size: 6px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
            display: block;
        }
        .box-value {
            font-size: 9px;
            font-weight: 700;
            color: #0f172a;
            display: block;
        }
        .section-title {
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin: 8px 0 3px 0;
            padding-bottom: 2px;
            border-bottom: 0.8px solid #cbd5e1;
        }
        .bg-light { background: #f8fafc !important; }
        .text-red { color: #dc2626; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .chk-box {
            border: 1px solid #475569;
            width: 12px;
            height: 12px;
            display: inline-block;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="romaneio-sheet">
    <div class="doc-title">Romaneio de Carga e Separação</div>

    {{-- ══ 1. CABEÇALHO: INFO DA NOTA ══ --}}
    <table>
        <tr>
            <td style="width: 50%;">
                <span class="box-title">EMITENTE</span>
                <span class="box-value">{{ $invoice->issuer_name }}</span>
                <span style="font-size: 7px; color: #475569;">CNPJ: {{ $invoice->formatted_issuer_cnpj }}</span>
            </td>
            <td style="width: 25%;">
                <span class="box-title">NÚMERO DA NOTA</span>
                <span class="box-value text-red">NF-e Nº {{ str_pad(str_replace('NF-', '', $invoice->number), 6, '0', STR_PAD_LEFT) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="box-title">SÉRIE E DATA</span>
                <span class="box-value">Série: {{ $invoice->series }}</span>
                <span style="font-size: 7px; color: #475569;">Emissão: {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : '-' }}</span>
            </td>
        </tr>
    </table>
 
    {{-- ══ 2. DESTINATÁRIO ══ --}}
    <div class="section-title">Dados do Destinatário e Entrega</div>
    <table>
        <tr>
            <td style="width: 70%;">
                <span class="box-title">RAZÃO SOCIAL / CLIENTE</span>
                <span class="box-value">{{ $invoice->recipient_name }}</span>
            </td>
            <td style="width: 30%;">
                <span class="box-title">CNPJ / CPF</span>
                <span class="box-value">{{ $invoice->formatted_recipient_document ?: 'ISENTO' }}</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width: 60%;">
                <span class="box-title">ENDEREÇO DE ENTREGA</span>
                <span class="box-value">{{ $invoice->recipient_address ?: '—' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="box-title">CIDADE / UF</span>
                <span class="box-value">{{ $invoice->recipient_city ?: '—' }} - {{ strtoupper($invoice->recipient_state ?: '—') }}</span>
            </td>
            <td style="width: 15%;">
                <span class="box-title">CEP</span>
                <span class="box-value">{{ $invoice->recipient_zip ?: '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 3. DADOS DE TRANSPORTE ══ --}}
    <div class="section-title">Dados de Logística e Transporte</div>
    <table>
        <tr>
            <td style="width: 40%;">
                <span class="box-title">TRANSPORTADORA</span>
                <span class="box-value">{{ $invoice->carrier?->name ?? 'Próprio / Retira' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="box-title">CNPJ DA TRANS.</span>
                <span class="box-value">{{ $invoice->carrier?->cnpj ?? '—' }}</span>
            </td>
            <td style="width: 20%;">
                <span class="box-title">PLACA VEÍCULO</span>
                <span class="box-value">{{ $invoice->carrier?->vehicle_plate ?? '—' }} ({{ $invoice->carrier?->vehicle_uf ?? '—' }})</span>
            </td>
            <td style="width: 15%;">
                <span class="box-title">QTD. VOLUMES</span>
                <span class="box-value">{{ number_format($invoice->items->sum('quantity'), 0) }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 4. ITENS DO ROMANEIO ══ --}}
    <div class="section-title">Itens para Separação / Conferência de Carga</div>
    <table>
        <thead>
            <tr class="bg-light">
                <th style="width: 5%; text-align: center; font-weight: 800;">CONF.</th>
                <th style="width: 15%; font-weight: 800;">EAN / CÓD. BARRAS</th>
                <th style="width: 15%; font-weight: 800;">SKU / REF</th>
                <th style="width: 35%; font-weight: 800;">DESCRIÇÃO DO PRODUTO</th>
                <th style="width: 18%; font-weight: 800; text-align: center;">LOCALIZAÇÃO (WMS)</th>
                <th style="width: 6%; font-weight: 800; text-align: center;">UNID.</th>
                <th style="width: 6%; font-weight: 800; text-align: right;">QTD.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
            <tr>
                <td class="text-center" style="vertical-align: middle; padding: 2px;">
                    <div class="chk-box"></div>
                </td>
                <td style="font-family: monospace; font-size: 8px; vertical-align: middle;">{{ $item->product->barcode ?? '—' }}</td>
                <td style="font-family: monospace; font-size: 8px; vertical-align: middle;">{{ $item->product->sku ?? '—' }}</td>
                <td style="font-weight: bold; font-size: 8.5px;">{{ $item->description }}</td>
                <td class="text-center" style="font-family: monospace; font-weight: 800; font-size: 9.5px; color: #2563eb; vertical-align: middle;">
                    {{ $item->product->location?->full_code ?? 'NÃO ALOCADO' }}
                </td>
                <td class="text-center" style="vertical-align: middle;">{{ strtoupper($item->unit) }}</td>
                <td class="text-right" style="font-weight: 800; font-size: 9px; vertical-align: middle;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ══ 5. ASSINATURAS ══ --}}
    <div style="margin-top: 40px; border-top: 1px dashed #475569; padding-top: 15px;">
        <table style="border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="width: 33%; border: none; text-align: center; padding: 0 10px;">
                    <div style="border-top: 1px solid #334155; margin-top: 25px; padding-top: 4px; font-weight: bold; font-size: 7.5px;">
                        SEPARADO POR (Assinatura / Nome)
                    </div>
                </td>
                <td style="width: 33%; border: none; text-align: center; padding: 0 10px;">
                    <div style="border-top: 1px solid #334155; margin-top: 25px; padding-top: 4px; font-weight: bold; font-size: 7.5px;">
                        CONFERIDO POR: {{ $invoice->conferredBy?->name ?? '_____________________' }}
                    </div>
                </td>
                <td style="width: 33%; border: none; text-align: center; padding: 0 10px;">
                    <div style="border-top: 1px solid #334155; margin-top: 25px; padding-top: 4px; font-weight: bold; font-size: 7.5px;">
                        MOTORISTA / TRANSPORTADOR (Assinatura)
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Rodapé --}}
    <div style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center; font-size: 6.5px; color: #64748b; border-top: 0.5px solid #cbd5e1; padding-top: 4px;">
        LogiSync WMS — Sistema de Gerenciamento de Armazém — Emitido em {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

</body>
</html>
