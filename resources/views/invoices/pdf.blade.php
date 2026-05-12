<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .page { padding: 30px 36px; }

        /* ── Header ── */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 18px; border-bottom: 3px solid #2563eb; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: 700; color: #1e40af; }
        .company-info { font-size: 10px; color: #64748b; margin-top: 4px; line-height: 1.5; }
        .nf-badge { text-align: right; }
        .nf-title { font-size: 15px; font-weight: 700; color: #2563eb; letter-spacing: 1px; }
        .nf-number { font-size: 24px; font-weight: 900; color: #1e293b; }
        .nf-meta { font-size: 10px; color: #64748b; margin-top: 3px; }

        /* ── Status bar ── */
        .status-bar { display: flex; gap: 12px; margin-bottom: 18px; }
        .status-pill { padding: 4px 14px; border-radius: 99px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .pill-emitida   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .pill-rascunho  { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        .pill-cancelada { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .pill-saida  { background: #fed7aa; color: #9a3412; border: 1px solid #fdba74; }
        .pill-entrada { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

        /* ── Parties ── */
        .parties { display: flex; gap: 16px; margin-bottom: 18px; }
        .party-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
        .party-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 8px; }
        .party-name { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .party-detail { font-size: 10px; color: #475569; line-height: 1.6; }

        /* ── Payment info ── */
        .info-row { display: flex; gap: 12px; margin-bottom: 18px; }
        .info-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; }
        .info-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
        .info-value { font-size: 11px; font-weight: 600; color: #1e293b; }

        /* ── Items table ── */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items-table thead tr { background: #1e40af; }
        .items-table thead th { color: #fff; padding: 9px 12px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .items-table thead th.text-right { text-align: right; }
        .items-table thead th.text-center { text-align: center; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table tbody td { padding: 8px 12px; font-size: 10.5px; border-bottom: 1px solid #f1f5f9; }
        .items-table tbody td.text-right { text-align: right; }
        .items-table tbody td.text-center { text-align: center; }

        /* ── Totals ── */
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 18px; }
        .totals-table { width: 260px; }
        .totals-table tr td { padding: 5px 12px; font-size: 11px; }
        .totals-table tr td:first-child { color: #64748b; }
        .totals-table tr td:last-child { text-align: right; font-weight: 600; }
        .totals-table .grand-row td { font-size: 14px; font-weight: 800; color: #1e40af; border-top: 2px solid #2563eb; padding-top: 8px; }

        /* ── Notes ── */
        .notes-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; background: #fffbeb; margin-bottom: 18px; }
        .notes-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #92400e; margin-bottom: 6px; }
        .notes-text { font-size: 10px; color: #451a03; line-height: 1.6; }

        /* ── Footer ── */
        .footer { border-top: 1px solid #e2e8f0; padding-top: 12px; display: flex; justify-content: space-between; }
        .footer-text { font-size: 9px; color: #94a3b8; }

        /* ── Signature ── */
        .signatures { display: flex; gap: 40px; margin-top: 30px; }
        .sig-line { flex: 1; border-top: 1px solid #94a3b8; padding-top: 6px; text-align: center; font-size: 9px; color: #64748b; }

        /* ── Watermark for cancelled ── */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 80px; font-weight: 900; color: rgba(239,68,68,0.12); letter-spacing: 8px; text-transform: uppercase; pointer-events: none; }
    </style>
</head>
<body>
<div class="page">

    @if($invoice->status === 'cancelada')
    <div class="watermark">CANCELADA</div>
    @endif

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="company-name">{{ $invoice->issuer_name }}</div>
            <div class="company-info">
                CNPJ: {{ $invoice->issuer_cnpj }}<br>
                {{ $invoice->issuer_address }}<br>
                {{ $invoice->issuer_city }} – {{ $invoice->issuer_state }} &bull; CEP {{ $invoice->issuer_zip }}
            </div>
        </div>
        <div class="nf-badge">
            <div class="nf-title">NOTA FISCAL</div>
            <div class="nf-number">{{ $invoice->number }}</div>
            <div class="nf-meta">
                Série {{ $invoice->series }} &bull;
                Emissão: {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : date('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Status bar --}}
    <div class="status-bar">
        <span class="status-pill pill-{{ $invoice->status }}">{{ $invoice->statusLabel() }}</span>
        <span class="status-pill pill-{{ $invoice->type }}">
            {{ $invoice->type === 'saida' ? '↑ Saída' : '↓ Entrada' }}
        </span>
        <span class="status-pill" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">
            Pagto: {{ $invoice->paymentLabel() }}
        </span>
        @if($invoice->due_date)
        <span class="status-pill" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">
            Venc: {{ $invoice->due_date->format('d/m/Y') }}
        </span>
        @endif
    </div>

    {{-- Parties --}}
    <div class="parties">
        <div class="party-box">
            <div class="party-label">Emitente</div>
            <div class="party-name">{{ $invoice->issuer_name }}</div>
            <div class="party-detail">
                CNPJ: {{ $invoice->issuer_cnpj }}<br>
                {{ $invoice->issuer_address }}<br>
                {{ $invoice->issuer_city }} – {{ $invoice->issuer_state }}
            </div>
        </div>
        <div class="party-box">
            <div class="party-label">Destinatário / Remetente</div>
            <div class="party-name">{{ $invoice->recipient_name }}</div>
            <div class="party-detail">
                @if($invoice->recipient_document)CPF/CNPJ: {{ $invoice->recipient_document }}<br>@endif
                @if($invoice->recipient_address){{ $invoice->recipient_address }}<br>@endif
                @if($invoice->recipient_city){{ $invoice->recipient_city }} – {{ $invoice->recipient_state }}@endif
                @if($invoice->recipient_email)<br>E-mail: {{ $invoice->recipient_email }}@endif
                @if($invoice->recipient_phone) | Tel: {{ $invoice->recipient_phone }}@endif
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th>Descrição do Produto / Serviço</th>
                <th class="text-center" style="width:40px">Unid.</th>
                <th class="text-right" style="width:55px">Qtde</th>
                <th class="text-right" style="width:80px">Preço Unit.</th>
                <th class="text-right" style="width:50px">Desc. %</th>
                <th class="text-right" style="width:80px">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
            <tr>
                <td style="color:#94a3b8">{{ $idx + 1 }}</td>
                <td><strong>{{ $item->description }}</strong></td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td class="text-right">{{ $item->discount > 0 ? number_format($item->discount, 2, ',', '.').'%' : '–' }}</td>
                <td class="text-right"><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <table class="totals-table">
            <tr><td>Subtotal</td><td>R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
            @if($invoice->discount > 0)
            <tr><td style="color:#dc2626">Desconto</td><td style="color:#dc2626">– R$ {{ number_format($invoice->discount, 2, ',', '.') }}</td></tr>
            @endif
            @if($invoice->shipping > 0)
            <tr><td style="color:#2563eb">Frete</td><td style="color:#2563eb">+ R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</td></tr>
            @endif
            <tr class="grand-row">
                <td>TOTAL GERAL</td>
                <td>R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div class="notes-box">
        <div class="notes-label">Observações</div>
        <div class="notes-text">{{ $invoice->notes }}</div>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-line">Emitente<br>{{ $invoice->issuer_name }}</div>
        <div class="sig-line">Destinatário<br>{{ $invoice->recipient_name }}</div>
        <div class="sig-line">Transportador / Recebedor</div>
    </div>

    {{-- Footer --}}
    <div class="footer" style="margin-top: 20px;">
        <div class="footer-text">Documento gerado pelo sistema LogiSync WMS &bull; {{ now()->format('d/m/Y H:i') }}</div>
        <div class="footer-text">Este documento é fictício e não possui validade fiscal</div>
    </div>

</div>
</body>
</html>
