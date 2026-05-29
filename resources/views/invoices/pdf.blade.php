<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DANFE - {{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; color: #000; background: #fff; }

        .page { padding: 15px; }

        /* ── DANFE Header Structure ── */
        .danfe-box { border: 1px solid #000; margin-bottom: 5px; }
        .danfe-row { display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #000; }
        .danfe-row:last-child { border-bottom: none; }
        .danfe-col { display: table-cell; padding: 3px 5px; border-right: 1px solid #000; vertical-align: top; }
        .danfe-col:last-child { border-right: none; }

        .label { font-size: 6px; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; display: block; }
        .value { font-size: 9px; font-weight: bold; }
        .value-sm { font-size: 8px; font-weight: normal; }

        /* ── Top Section (DANFE Identification) ── */
        .id-section { height: 120px; }
        .company-info { width: 40%; text-align: center; padding-top: 10px; }
        .danfe-label { width: 15%; text-align: center; border-left: 1px solid #000; border-right: 1px solid #000; padding: 10px 0; }
        .danfe-label h1 { font-size: 14px; font-weight: 800; margin-bottom: 5px; }
        .danfe-label p { font-size: 7px; line-height: 1.2; }
        .barcode-section { width: 45%; padding: 5px; }

        .access-key-box { border: 1px solid #000; padding: 5px; margin-bottom: 5px; text-align: center; }
        .access-key { font-family: 'Courier', monospace; font-size: 11px; letter-spacing: 1px; font-weight: bold; }

        /* ── Sections ── */
        .section-title { font-size: 7px; font-weight: 800; background: #f0f0f0; padding: 2px 5px; border: 1px solid #000; border-bottom: none; margin-top: 10px; text-transform: uppercase; }

        /* ── Tables ── */
        .data-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .data-table th { background: #f0f0f0; border: 1px solid #000; padding: 3px; font-size: 7px; text-transform: uppercase; text-align: left; }
        .data-table td { border: 1px solid #000; padding: 3px; font-size: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ── Footer ── */
        .footer { margin-top: 10px; font-size: 7px; text-align: center; border-top: 1px dashed #000; padding-top: 5px; }

        /* ── Fake Barcode ── */
        .fake-barcode { background: repeating-linear-gradient(90deg, #000, #000 1px, #fff 1px, #fff 3px); height: 40px; width: 100%; border: 1px solid #000; margin-bottom: 5px; }

        /* ── Watermark ── */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; font-weight: bold; color: rgba(0,0,0,0.05); z-index: -1; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">SIMULAÇÃO SEM VALOR FISCAL</div>

    {{-- Recibo --}}
    <div class="danfe-box">
        <div class="danfe-row" style="height: 40px;">
            <div class="danfe-col" style="width: 80%;">
                <span class="label">Recebemos de {{ $invoice->issuer_name }} os produtos/serviços constantes na Nota Fiscal indicada ao lado</span>
                <div style="margin-top: 10px; border-bottom: 1px solid #000; width: 100%;"></div>
                <div style="display: flex; justify-content: space-between; margin-top: 2px;">
                    <span class="label">DATA DE RECEBIMENTO</span>
                    <span class="label">IDENTIFICAÇÃO E ASSINATURA DO RECEBEDOR</span>
                </div>
            </div>
            <div class="danfe-col" style="width: 20%; text-align: center;">
                <span class="label">NF-e</span>
                <span class="value" style="font-size: 14px;">Nº {{ str_replace('NF-', '', $invoice->number) }}</span><br>
                <span class="label">SÉRIE {{ $invoice->series }}</span>
            </div>
        </div>
    </div>

    {{-- Identificação do Emitente --}}
    <div class="danfe-box">
        <div class="danfe-row" style="border-bottom: none;">
            <div class="danfe-col company-info">
                <span class="value" style="font-size: 12px; display: block; margin-bottom: 5px;">{{ $invoice->issuer_name }}</span>
                <span class="value-sm">{{ $invoice->issuer_address }}<br>{{ $invoice->issuer_zip }} - {{ $invoice->issuer_city }} - {{ $invoice->issuer_state }}<br>Fone: (11) 3344-5566</span>
            </div>
            <div class="danfe-col danfe-label">
                <h1>DANFE</h1>
                <p>Documento Auxiliar da<br>Nota Fiscal Eletrônica</p>
                <div style="margin: 5px 0;">
                    <span class="label">0 - ENTRADA<br>1 - SAÍDA</span>
                    <span class="value" style="border: 1px solid #000; padding: 2px 8px;">{{ $invoice->type === 'saida' ? '1' : '0' }}</span>
                </div>
                <span class="value">Nº {{ str_replace('NF-', '', $invoice->number) }}</span><br>
                <span class="label">SÉRIE 001</span><br>
                <span class="label">PÁGINA 1 de 1</span>
            </div>
            <div class="danfe-col barcode-section">
                <span class="label">CONTROLE DO FISCO</span>
                <div class="fake-barcode"></div>
                <span class="label">CHAVE DE ACESSO</span>
                <div class="access-key-box">
                    <span class="access-key">3524 05{{ rand(10000000, 99999999) }} {{ rand(10000000, 99999999) }} {{ rand(1000, 9999) }} 5500 1000 {{ rand(100000, 999999) }}</span>
                </div>
                <div style="text-align: center;">
                    <span class="label">Consulta de autenticidade no portal nacional da NF-e www.nfe.fazenda.gov.br</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Natureza da Operação --}}
    <div class="danfe-box">
        <div class="danfe-row">
            <div class="danfe-col" style="width: 60%;">
                <span class="label">NATUREZA DA OPERAÇÃO</span>
                <span class="value">{{ $invoice->type === 'saida' ? 'VENDA DE MERCADORIA' : 'COMPRA PARA INDUSTRIALIZACAO' }}</span>
            </div>
            <div class="danfe-col" style="width: 40%;">
                <span class="label">PROTOCOLO DE AUTORIZAÇÃO DE USO</span>
                <span class="value">{{ rand(100000000000000, 999999999999999) }} - {{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
        <div class="danfe-row" style="border-bottom: none;">
            <div class="danfe-col"><span class="label">INSCRIÇÃO ESTADUAL</span><span class="value">123.456.789.110</span></div>
            <div class="danfe-col"><span class="label">INSC.ESTADUAL DO SUBST. TRIB.</span><span class="value"></span></div>
            <div class="danfe-col"><span class="label">CNPJ</span><span class="value">{{ $invoice->issuer_cnpj }}</span></div>
        </div>
    </div>

    {{-- Destinatário / Remetente --}}
    <div class="section-title">Destinatário / Remetente</div>
    <div class="danfe-box">
        <div class="danfe-row">
            <div class="danfe-col" style="width: 70%;"><span class="label">NOME / RAZÃO SOCIAL</span><span class="value">{{ $invoice->recipient_name }}</span></div>
            <div class="danfe-col" style="width: 20%;"><span class="label">CNPJ / CPF</span><span class="value">{{ $invoice->recipient_document }}</span></div>
            <div class="danfe-col" style="width: 10%;"><span class="label">DATA EMISSÃO</span><span class="value">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
        </div>
        <div class="danfe-row">
            <div class="danfe-col" style="width: 50%;"><span class="label">ENDEREÇO</span><span class="value">{{ $invoice->recipient_address }}</span></div>
            <div class="danfe-col" style="width: 25%;"><span class="label">BAIRRO / DISTRITO</span><span class="value">CENTRO</span></div>
            <div class="danfe-col" style="width: 15%;"><span class="label">CEP</span><span class="value">{{ $invoice->recipient_zip }}</span></div>
            <div class="danfe-col" style="width: 10%;"><span class="label">DATA SAÍDA</span><span class="value">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
        </div>
        <div class="danfe-row" style="border-bottom: none;">
            <div class="danfe-col" style="width: 40%;"><span class="label">MUNICÍPIO</span><span class="value">{{ $invoice->recipient_city }}</span></div>
            <div class="danfe-col" style="width: 10%;"><span class="label">UF</span><span class="value">{{ $invoice->recipient_state }}</span></div>
            <div class="danfe-col" style="width: 20%;"><span class="label">FONE / FAX</span><span class="value">{{ $invoice->recipient_phone }}</span></div>
            <div class="danfe-col" style="width: 20%;"><span class="label">INSCRIÇÃO ESTADUAL</span><span class="value">ISENTO</span></div>
            <div class="danfe-col" style="width: 10%;"><span class="label">HORA SAÍDA</span><span class="value">{{ now()->format('H:i') }}</span></div>
        </div>
    </div>

    {{-- Cálculo do Imposto --}}
    <div class="section-title">Cálculo do Imposto</div>
    <div class="danfe-box">
        <div class="danfe-row">
            <div class="danfe-col"><span class="label">BASE DE CÁLC. ICMS</span><span class="value">{{ number_format($invoice->items->sum('icms_base'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR DO ICMS</span><span class="value">{{ number_format($invoice->items->sum('icms_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">BASE CÁLC. ICMS S.T.</span><span class="value">{{ number_format($invoice->items->sum('icms_st_base'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR DO ICMS S.T.</span><span class="value">{{ number_format($invoice->items->sum('icms_st_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR TOTAL DOS PRODUTOS</span><span class="value">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</span></div>
        </div>
        <div class="danfe-row">
            <div class="danfe-col"><span class="label">VALOR DO FRETE</span><span class="value">R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">DESCONTO</span><span class="value">R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR DO IPI</span><span class="value">{{ number_format($invoice->items->sum('ipi_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR PIS / COFINS</span><span class="value">{{ number_format($invoice->items->sum('pis_value') + $invoice->items->sum('cofins_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col"><span class="label">VALOR ISS / CSLL / IRPJ / CPP</span><span class="value">{{ number_format($invoice->items->sum('iss_value') + $invoice->items->sum('csll_value') + $invoice->items->sum('irpj_value') + $invoice->items->sum('cpp_value'), 2, ',', '.') }}</span></div>
        </div>
        <div class="danfe-row" style="border-bottom: none;">
            <div class="danfe-col" style="width: 40%;"><span class="label">VALOR IBS / CBS / IS (Reforma 2026)</span><span class="value">R$ {{ number_format($invoice->items->sum('ibs_value') + $invoice->items->sum('cbs_value') + $invoice->items->sum('is_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col" style="width: 30%;"><span class="label">VALOR II (Importação)</span><span class="value">R$ {{ number_format($invoice->items->sum('ii_value'), 2, ',', '.') }}</span></div>
            <div class="danfe-col" style="width: 30%;"><span class="label">VALOR TOTAL DA NOTA</span><span class="value" style="font-size: 11px;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</span></div>
        </div>
    </div>

    {{-- Dados do Produto / Serviço --}}
    <div class="section-title">Dados do Produto / Serviço</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">CÓDIGO</th>
                <th style="width: 32%;">DESCRIÇÃO DO PRODUTO / SERVIÇO</th>
                <th style="width: 10%;">NCM</th>
                <th style="width: 6%;">CFOP</th>
                <th style="width: 5%;">UNID.</th>
                <th style="width: 7%;" class="text-right">QUANT.</th>
                <th style="width: 8%;" class="text-right">V. UNIT.</th>
                <th style="width: 8%;" class="text-right">V. TOTAL</th>
                <th style="width: 7%;" class="text-right">BC ICMS</th>
                <th style="width: 7%;" class="text-right">V. ICMS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->barcode ?? '0000' }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->ncm }}</td>
                <td class="text-center">{{ $item->cfop }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->total, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->icms_base, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->icms_value, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Dados Adicionais --}}
    <div class="section-title">Dados Adicionais</div>
    <div class="danfe-box">
        <div class="danfe-row" style="border-bottom: none; height: 60px;">
            <div class="danfe-col" style="width: 70%;">
                <span class="label">INFORMAÇÕES COMPLEMENTARES</span>
                <span class="value-sm">
                    Pagamento via: {{ $invoice->paymentLabel() }}<br>
                    {{ $invoice->notes }}<br>
                    Documento gerado em ambiente de teste LogiSync WMS.
                </span>
            </div>
            <div class="danfe-col" style="width: 30%;">
                <span class="label">RESERVADO AO FISCO</span>
            </div>
        </div>
    </div>

    <div class="footer">
        Gerado por LogiSync WMS &bull; Este documento é uma simulação sem validade jurídica.
    </div>
</div>
</body>
</html>
