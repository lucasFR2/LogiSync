<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nota Fiscal Modelo 1 - {{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page {
            size: a4 portrait;
            margin: 12px 14px 12px 14px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7.5px;
            color: #111;
            background: #fff;
            line-height: 1.15;
        }

        /* ── SHEET WRAPPER ── */
        .invoice-sheet {
            width: 100%;
            background: #fff;
        }

        /* ── TABLES ── */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 0;
        }
        td, th {
            border: 0.8px solid #334155;
            padding: 3px 5px;
            vertical-align: top;
            background: transparent;
            color: #111;
        }
        table.no-top td, table.no-top th {
            border-top: none;
        }

        /* ── LABELS & VALUES ── */
        .box-title {
            font-size: 5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
            display: block;
        }
        .box-value {
            font-size: 8px;
            font-weight: 700;
            color: #0f172a;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .box-value.wrap {
            white-space: normal;
        }

        /* ── SECTION TITLES ── */
        .section-title {
            font-size: 5.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #475569;
            margin: 5px 0 2px 0;
            padding-bottom: 1px;
            border-bottom: 0.5px solid #cbd5e1;
        }

        /* ── HEADER ── */
        .doc-header {
            font-size: 9px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
        }

        /* ── WATERMARK ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 52px;
            font-weight: bold;
            color: rgba(0,0,0,0.03);
            z-index: -1;
            white-space: nowrap;
        }

        /* ── CHECKBOX ── */
        .chk {
            border: 0.8px solid #334155;
            padding: 0 2px;
            font-weight: bold;
            font-family: monospace;
            background: #f1f5f9;
            border-radius: 2px;
            display: inline-block;
            min-width: 10px;
            text-align: center;
            font-size: 7px;
        }

        /* ── HIGHLIGHT CELLS ── */
        .bg-light { background: #f8fafc !important; }
        .bg-blue  { background: #eff6ff !important; }
        .bg-total { background: #f0fdf4 !important; }

        /* ── COLORS ── */
        .text-red  { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .text-green{ color: #16a34a; }
        .text-muted{ color: #64748b; }

        /* ── CANHOTO ── */
        .canhoto {
            border-top: 1px dashed #94a3b8;
            margin-top: 8px;
            padding-top: 6px;
        }
    </style>
</head>
<body>
<div class="watermark">SIMULAÇÃO SEM VALOR FISCAL</div>

<div class="invoice-sheet">

    <div class="doc-header">Nota Fiscal Modelo 1</div>

    {{-- ══ 1. CABEÇALHO: EMITENTE ══ --}}
    <table>
        <tr>
            {{-- Logo --}}
            <td rowspan="2" style="width:13%; text-align:center; vertical-align:middle; padding:4px;">
                @if($invoice->logo)
                    <img src="{{ $invoice->logo }}" style="max-height:42px; max-width:100%;">
                @else
                    <div style="font-size:6.5px; font-weight:bold; color:#94a3b8; border:0.8px dashed #94a3b8; padding:10px 2px; border-radius:3px;">LOGO</div>
                @endif
            </td>
            {{-- Emitente --}}
            <td rowspan="2" style="width:44%;">
                <span class="box-title">IDENTIFICAÇÃO DO EMITENTE</span>
                <span style="font-size:9px; font-weight:800; display:block; margin-bottom:2px; color:#0f172a;">{{ $invoice->issuer_name }}</span>
                <span style="font-size:6px; display:block; line-height:1.3; color:#475569;">
                    {{ $invoice->issuer_address }}<br>
                    CEP: {{ $invoice->issuer_zip }} — {{ $invoice->issuer_city }} / {{ $invoice->issuer_state }} — Fone: (11) 3344-5566
                </span>
            </td>
            {{-- Tipo da Nota --}}
            <td style="width:13%; text-align:center; vertical-align:middle; padding:3px;">
                <span style="font-size:6px; font-weight:800; display:block; margin-bottom:3px; color:#0f172a; text-transform:uppercase;">Tipo da Nota</span>
                <div style="font-size:6px; text-align:left; padding-left:8px; line-height:1.6; color:#0f172a;">
                    <span class="chk">{{ $invoice->type === 'entrada' ? 'X' : ' ' }}</span> 0 - ENTRADA<br>
                    <span class="chk">{{ $invoice->type === 'saida' ? 'X' : ' ' }}</span> 1 - SAÍDA
                </div>
            </td>
            {{-- CNPJ/IE --}}
            <td colspan="2" style="width:30%;">
                <span class="box-title">CNPJ / IE DO EMITENTE</span>
                <span class="box-value">{{ $invoice->formatted_issuer_cnpj }}</span>
                <span style="font-size:6px; display:block; margin-top:1px; color:#475569;">IE: 123.456.789.110</span>
            </td>
        </tr>
        <tr>
            {{-- Via --}}
            <td style="text-align:center; vertical-align:middle;">
                <span style="font-size:8px; font-weight:800; color:#0f172a;">1ª VIA</span>
            </td>
            {{-- Número --}}
            <td style="width:14%;">
                <span class="box-title">NÚMERO</span>
                <span class="box-value text-red" style="font-size:9px;">Nº {{ str_pad(str_replace('NF-', '', $invoice->number), 6, '0', STR_PAD_LEFT) }}</span>
            </td>
            {{-- Série --}}
            <td style="width:16%;">
                <span class="box-title">SÉRIE</span>
                <span class="box-value" style="font-size:9px;">{{ $invoice->series }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 2. NATUREZA DA OPERAÇÃO ══ --}}
    <table class="no-top">
        <tr>
            <td style="width:50%;">
                <span class="box-title">NATUREZA DA OPERAÇÃO</span>
                <span class="box-value">{{ $invoice->type === 'saida' ? 'VENDA DE MERCADORIA' : 'COMPRA PARA INDUSTRIALIZAÇÃO' }}</span>
            </td>
            <td style="width:10%; text-align:center;">
                <span class="box-title">CFOP</span>
                <span class="box-value">{{ $invoice->items->first()->cfop ?? '—' }}</span>
            </td>
            <td style="width:25%;">
                <span class="box-title">INSC. ESTADUAL DO SUBST. TRIB.</span>
                <span class="box-value">—</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                <span class="box-value">123.456.789.110</span>
            </td>
        </tr>
    </table>

    {{-- ══ 3. DESTINATÁRIO / REMETENTE ══ --}}
    <div class="section-title">DESTINATÁRIO / REMETENTE</div>
    <table>
        <tr>
            <td style="width:70%;">
                <span class="box-title">NOME / RAZÃO SOCIAL</span>
                <span class="box-value">{{ $invoice->recipient_name }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">CNPJ / CPF</span>
                <span class="box-value">{{ $invoice->formatted_recipient_document ?: '—' }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">DATA DA EMISSÃO</span>
                <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '—' }}</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width:50%;">
                <span class="box-title">ENDEREÇO</span>
                <span class="box-value">{{ $invoice->recipient_address ?: '—' }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">BAIRRO / DISTRITO</span>
                <span class="box-value">CENTRO</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">CEP</span>
                <span class="box-value">{{ $invoice->recipient_zip ?: '—' }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">DATA SAÍDA / ENTRADA</span>
                <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '—' }}</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width:45%;">
                <span class="box-title">MUNICÍPIO</span>
                <span class="box-value">{{ $invoice->recipient_city ?: '—' }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">FONE / FAX</span>
                <span class="box-value">{{ $invoice->recipient_phone ?: '—' }}</span>
            </td>
            <td style="width:10%; text-align:center;">
                <span class="box-title">UF</span>
                <span class="box-value" style="text-transform:uppercase;">{{ $invoice->recipient_state ?: '—' }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                <span class="box-value">ISENTO</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">HORA DA SAÍDA</span>
                <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('H:i') : '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 4. FATURA ══ --}}
    <div class="section-title">FATURA</div>
    <table>
        <tr>
            <td style="width:100%; padding:3px 5px;">
                <span class="box-title">FATURA / DUPLICATAS</span>
                <span class="box-value" style="font-size:7.5px;">PAGAMENTO VIA: {{ strtoupper($invoice->paymentLabel()) }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 5. CÁLCULO DO IMPOSTO ══ --}}
    <div class="section-title">CÁLCULO DO IMPOSTO</div>
    <table>
        <tr>
            <td style="width:20%;">
                <span class="box-title">BASE DE CÁLCULO DO ICMS</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_base'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">VALOR DO ICMS</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_value'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">BASE DE CÁLCULO ICMS SUBST.</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_st_base'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">VALOR DO ICMS SUBSTITUIÇÃO</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_st_value'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">VALOR TOTAL DOS PRODUTOS</span>
                <span class="box-value">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width:15%;">
                <span class="box-title">VALOR DO FRETE</span>
                <span class="box-value">R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">VALOR DO SEGURO</span>
                <span class="box-value">R$ 0,00</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">DESCONTO</span>
                <span class="box-value">R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span>
            </td>
            <td style="width:15%;">
                <span class="box-title">OUTRAS DESPESAS ACESS.</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('ii_value'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">VALOR TOTAL DO IPI</span>
                <span class="box-value">R$ {{ number_format($invoice->items->sum('ipi_value'), 2, ',', '.') }}</span>
            </td>
            <td style="width:20%;" class="bg-total">
                <span class="box-title text-green">VALOR TOTAL DA NOTA</span>
                <span class="box-value text-green" style="font-size:9px;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 5b. TRIBUTOS CONSOLIDADOS (2026) ══ --}}
    <div class="section-title">TRIBUTOS CONSOLIDADOS DA NOTA FISCAL (2026)</div>
    <table>
        <tr>
            <td style="width:25%;">
                <span class="box-title">ICMS PRÓPRIO & ST</span>
                <span style="font-size:7px; display:block; line-height:1.4; color:#0f172a;">
                    ICMS: <b>R$ {{ number_format($invoice->items->sum('icms_value'), 2, ',', '.') }}</b><br>
                    ICMS ST: <b>R$ {{ number_format($invoice->items->sum('icms_st_value'), 2, ',', '.') }}</b>
                </span>
            </td>
            <td style="width:25%;">
                <span class="box-title">IPI, PIS & COFINS</span>
                <span style="font-size:7px; display:block; line-height:1.4; color:#0f172a;">
                    IPI: <b>R$ {{ number_format($invoice->items->sum('ipi_value'), 2, ',', '.') }}</b><br>
                    PIS: <b>R$ {{ number_format($invoice->items->sum('pis_value'), 2, ',', '.') }}</b><br>
                    COFINS: <b>R$ {{ number_format($invoice->items->sum('cofins_value'), 2, ',', '.') }}</b>
                </span>
            </td>
            <td style="width:25%;">
                <span class="box-title">ISS & RETENÇÕES</span>
                <span style="font-size:7px; display:block; line-height:1.4; color:#0f172a;">
                    ISS: <b>R$ {{ number_format($invoice->items->sum('iss_value'), 2, ',', '.') }}</b><br>
                    CSLL: <b>R$ {{ number_format($invoice->items->sum('csll_value'), 2, ',', '.') }}</b><br>
                    IRPJ/CPP: <b>R$ {{ number_format($invoice->items->sum('irpj_value') + $invoice->items->sum('cpp_value'), 2, ',', '.') }}</b>
                </span>
            </td>
            <td style="width:25%;" class="bg-blue">
                <span class="box-title text-blue">TOTAL TRIBUTOS (2026)</span>
                @php
                    $totalTributos = $invoice->items->sum('icms_value') + $invoice->items->sum('icms_st_value') +
                        $invoice->items->sum('ipi_value') + $invoice->items->sum('pis_value') +
                        $invoice->items->sum('cofins_value') + $invoice->items->sum('iss_value') +
                        $invoice->items->sum('csll_value') + $invoice->items->sum('irpj_value') +
                        $invoice->items->sum('cpp_value') + $invoice->items->sum('ibs_value') +
                        $invoice->items->sum('cbs_value') + $invoice->items->sum('is_value') +
                        $invoice->items->sum('ii_value');
                @endphp
                <span class="box-value text-blue" style="font-size:9px; margin-top:2px;">R$ {{ number_format($totalTributos, 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 6. TRANSPORTADOR ══ --}}
    @php
        $totalWeight = $invoice->items->sum(function($item) {
            return floatval($item->quantity) * floatval($item->product->weight ?? 0);
        });
    @endphp
    <div class="section-title">TRANSPORTADOR / VOLUMES TRANSPORTADOS</div>
    <table>
        <tr>
            <td style="width:40%;">
                <span class="box-title">NOME / RAZÃO SOCIAL</span>
                <span class="box-value">{{ $invoice->issuer_name }} (Próprio)</span>
            </td>
            <td style="width:18%; padding:2px 4px;">
                <span class="box-title">FRETE POR CONTA</span>
                <div style="font-size:6px; line-height:1.5; margin-top:2px; color:#0f172a; font-weight:bold;">
                    <span class="chk">X</span> 1 - EMITENTE &nbsp;
                    <span class="chk"> </span> 2 - DEST.
                </div>
            </td>
            <td style="width:15%;">
                <span class="box-title">PLACA DO VEÍCULO</span>
                <span class="box-value">—</span>
            </td>
            <td style="width:7%; text-align:center;">
                <span class="box-title">UF</span>
                <span class="box-value">{{ $invoice->issuer_state }}</span>
            </td>
            <td style="width:20%;">
                <span class="box-title">CNPJ / CPF</span>
                <span class="box-value">{{ $invoice->formatted_issuer_cnpj }}</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width:50%;">
                <span class="box-title">ENDEREÇO</span>
                <span class="box-value">{{ $invoice->issuer_address }}</span>
            </td>
            <td style="width:25%;">
                <span class="box-title">MUNICÍPIO</span>
                <span class="box-value">{{ $invoice->issuer_city }}</span>
            </td>
            <td style="width:7%; text-align:center;">
                <span class="box-title">UF</span>
                <span class="box-value">{{ $invoice->issuer_state }}</span>
            </td>
            <td style="width:18%;">
                <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                <span class="box-value">123.456.789.110</span>
            </td>
        </tr>
    </table>
    <table class="no-top">
        <tr>
            <td style="width:12.5%;">
                <span class="box-title">QUANTIDADE</span>
                <span class="box-value">{{ number_format($invoice->items->sum('quantity'), 0) }}</span>
            </td>
            <td style="width:12.5%;">
                <span class="box-title">ESPÉCIE</span>
                <span class="box-value">VOLUMES</span>
            </td>
            <td style="width:12.5%;">
                <span class="box-title">MARCA</span>
                <span class="box-value">—</span>
            </td>
            <td style="width:12.5%;">
                <span class="box-title">NÚMERO</span>
                <span class="box-value">—</span>
            </td>
            <td style="width:25%;">
                <span class="box-title">PESO BRUTO</span>
                <span class="box-value">{{ $totalWeight > 0 ? number_format($totalWeight, 3, ',', '.') . ' kg' : '—' }}</span>
            </td>
            <td style="width:25%;">
                <span class="box-title">PESO LÍQUIDO</span>
                <span class="box-value">{{ $totalWeight > 0 ? number_format($totalWeight, 3, ',', '.') . ' kg' : '—' }}</span>
            </td>
        </tr>
    </table>

    {{-- ══ 7. DADOS DO PRODUTO ══ --}}
    <div class="section-title">DADOS DO PRODUTO / SERVIÇOS</div>
    <table>
        <thead>
            <tr class="bg-light">
                <th rowspan="2" style="width:9%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:left; padding:2px 3px; color:#475569;">CÓDIGO</th>
                <th rowspan="2" style="width:30%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:left; padding:2px 3px; color:#475569;">DESCRIÇÃO DOS PRODUTOS/SERVIÇOS</th>
                <th rowspan="2" style="width:9%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:center; padding:2px 3px; color:#475569;">NCM/SH</th>
                <th rowspan="2" style="width:6%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:center; padding:2px 3px; color:#475569;">CST</th>
                <th rowspan="2" style="width:5%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:center; padding:2px 3px; color:#475569;">UNID</th>
                <th rowspan="2" style="width:8%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:right; padding:2px 3px; color:#475569;">QUANT.</th>
                <th rowspan="2" style="width:8%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:right; padding:2px 3px; color:#475569;">V. UNIT.</th>
                <th rowspan="2" style="width:9%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:right; padding:2px 3px; color:#475569;">V. TOTAL</th>
                <th colspan="2" style="width:10%; font-size:5.5px; font-weight:800; text-align:center; padding:2px; color:#475569; border-bottom:0.8px solid #334155;">ALÍQUOTAS</th>
                <th rowspan="2" style="width:6%; font-size:5.5px; font-weight:800; vertical-align:middle; text-align:right; padding:2px 3px; color:#475569;">V. IPI</th>
            </tr>
            <tr class="bg-light">
                <th style="width:5%; font-size:5px; text-align:center; padding:1px 2px; color:#475569;">ICMS</th>
                <th style="width:5%; font-size:5px; text-align:center; padding:1px 2px; color:#475569;">IPI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td style="font-family:monospace; font-size:6.5px; vertical-align:middle;">{{ $item->product->barcode ?? '0000' }}</td>
                <td style="font-weight:700; font-size:7px; white-space:normal; line-height:1.2; color:#0f172a;">{{ $item->description }}</td>
                <td style="font-family:monospace; text-align:center; vertical-align:middle; font-size:6.5px;">{{ $item->ncm }}</td>
                <td style="text-align:center; vertical-align:middle;">{{ $item->icms_cst ?: '00' }}</td>
                <td style="text-align:center; vertical-align:middle; font-weight:700;">{{ strtoupper($item->unit) }}</td>
                <td style="text-align:right; vertical-align:middle; font-weight:700;">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td style="text-align:right; vertical-align:middle;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td style="text-align:right; vertical-align:middle; font-weight:800; color:#0f172a;">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                <td style="text-align:center; vertical-align:middle; color:#2563eb; font-weight:700;">{{ number_format($item->icms_rate, 1, ',', '.') }}%</td>
                <td style="text-align:center; vertical-align:middle; color:#16a34a; font-weight:700;">{{ number_format($item->ipi_rate, 1, ',', '.') }}%</td>
                <td style="text-align:right; vertical-align:middle;">R$ {{ number_format($item->ipi_value, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ══ 8. DADOS ADICIONAIS ══ --}}
    <div class="section-title">DADOS ADICIONAIS</div>
    <table>
        <tr>
            <td style="width:60%; height:30px; vertical-align:top;">
                <span class="box-title">INFORMAÇÕES COMPLEMENTARES</span>
                <span style="font-size:6.5px; display:block; line-height:1.2; color:#334155; white-space:pre-wrap;">{{ $invoice->notes ?: '—' }}
Documento emitido para fins de simulação e controle interno.</span>
            </td>
            <td style="width:25%; height:30px;">
                <span class="box-title">RESERVADO AO FISCO</span>
            </td>
            <td style="width:15%; height:30px; text-align:center; padding:3px;">
                <span style="font-size:4.5px; font-weight:800; display:block; text-transform:uppercase; line-height:1.2; color:#64748b;">Nº DE CONTROLE DO FORMULÁRIO</span>
                <span style="font-size:8px; font-weight:800; display:block; margin-top:4px; color:#0f172a;">000.000</span>
            </td>
        </tr>
    </table>
    <div style="font-size:4.5px; color:#94a3b8; margin:3px 0 4px 0;">DADOS DA AIDF E DO IMPRESSOR</div>

    {{-- ══ 9. CANHOTO ══ --}}
    <div class="canhoto">
        <table>
            <tr>
                <td colspan="2" style="width:80%; height:14px; border:0.8px solid #334155;">
                    <span style="font-size:5.5px; font-weight:800; text-transform:uppercase; color:#334155;">RECEBEMOS DE {{ strtoupper($invoice->issuer_name) }} OS PRODUTOS CONSTANTES DA NOTA FISCAL INDICADA AO LADO</span>
                </td>
                <td rowspan="2" style="width:20%; text-align:center; vertical-align:middle; border:0.8px solid #334155;">
                    <span style="font-size:6px; font-weight:800; display:block; text-transform:uppercase; color:#475569;">NOTA FISCAL</span>
                    <span style="font-size:9px; font-weight:800; display:block; margin-top:2px; color:#dc2626;">Nº {{ str_pad(str_replace('NF-', '', $invoice->number), 6, '0', STR_PAD_LEFT) }}</span>
                    <span style="font-size:6px; display:block; margin-top:1px; font-weight:700; color:#475569;">SÉRIE {{ $invoice->series }}</span>
                </td>
            </tr>
            <tr>
                <td style="width:30%; height:18px; border:0.8px solid #334155; border-top:none;">
                    <span class="box-title">DATA DO RECEBIMENTO</span>
                </td>
                <td style="width:50%; height:18px; border:0.8px solid #334155; border-top:none; border-left:none;">
                    <span class="box-title">IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR</span>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
