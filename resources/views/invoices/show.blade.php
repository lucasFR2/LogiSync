@extends('layouts.app')

@section('title', 'Detalhes da Nota Fiscal')
@section('page-title', $invoice->number)
@section('page-subtitle', 'Série ' . $invoice->series . ' • ' . ($invoice->type === 'saida' ? 'Saída' : 'Entrada'))

@section('content')
<style>
    .invoice-sheet {
        background: var(--bg-surface);
        border: 1.5px solid var(--border);
        border-radius: var(--r-lg);
        padding: 2rem;
        box-shadow: var(--shadow-lg);
        width: 100%;
    }
    .invoice-sheet table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-bottom: 0.75rem;
    }
    .invoice-sheet td, .invoice-sheet th {
        border: 1px solid var(--border);
        padding: 0.6rem 0.8rem;
        vertical-align: top;
        background: transparent;
        color: var(--text-primary);
        font-size: 0.875rem;
    }
    .invoice-sheet table.no-top-border td, .invoice-sheet table.no-top-border th {
        border-top: none;
    }
    .invoice-sheet .box-title {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        letter-spacing: 0.05em;
        display: block;
    }
    .invoice-sheet .box-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        display: block;
    }
    .invoice-sheet .section-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.075em;
        color: var(--text-secondary);
        margin: 1.5rem 0 0.5rem 0;
    }
    .invoice-sheet .section-title:first-of-type {
        margin-top: 0;
    }
    /* Styles for nested checkbox design */
    .invoice-sheet .checkbox-box {
        border: 1px solid var(--border);
        padding: 0 4px;
        font-weight: bold;
        font-family: monospace;
        background: var(--bg-hover);
        border-radius: 3px;
        display: inline-block;
        min-width: 16px;
        text-align: center;
    }
</style>

<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:0.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Action Bar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            @php
                $statusConfig = [
                    'rascunho' => ['color' => 'var(--orange)', 'bg' => 'var(--orange-bg)', 'icon' => 'fa-pen'],
                    'emitida' => ['color' => 'var(--green)', 'bg' => 'var(--green-bg)', 'icon' => 'fa-check-circle'],
                    'concluída' => ['color' => 'var(--green)', 'bg' => 'var(--green-bg)', 'icon' => 'fa-check-double'],
                    'cancelada' => ['color' => 'var(--red)', 'bg' => 'var(--red-bg)', 'icon' => 'fa-ban']
                ][$invoice->status] ?? ['color' => 'var(--text-muted)', 'bg' => 'var(--bg-hover)', 'icon' => 'fa-circle'];
            @endphp
            <div class="badge" style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; padding: 0.5rem 1rem; font-size: 0.9rem; font-weight: 700;">
                <i class="fa-solid {{ $statusConfig['icon'] }} mr-2"></i> {{ $invoice->statusLabel() }}
            </div>
            <span style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500;">
                <i class="fa-solid fa-clock-rotate-left mr-1"></i> {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : 'Pendente' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            @if($invoice->status === 'emitida' || $invoice->status === 'concluída')
                <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="btn btn-primary" style="background: var(--green); border-color: var(--green); box-shadow: 0 8px 16px -4px var(--green-bg);">
                    <i class="fa-solid fa-file-pdf mr-2"></i> Visualizar DANFE
                </a>
            @endif
 
            @if($invoice->status === 'concluída')
                <a href="{{ route('invoices.romaneio', $invoice) }}" target="_blank" class="btn btn-primary" style="background: var(--blue); border-color: var(--blue); box-shadow: 0 8px 16px -4px var(--blue-bg);">
                    <i class="fa-solid fa-print mr-2"></i> Imprimir Romaneio
                </a>
            @endif

            @if($invoice->status === 'emitida' && $invoice->type === 'saida' && auth()->user()->hasPermission('notas_fiscais.editar'))
                <form action="{{ route('invoices.conclude', $invoice) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja concluir esta nota? Isso dará baixa no estoque dos produtos.')">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: var(--accent); border-color: var(--accent); box-shadow: 0 8px 16px -4px var(--accent-glow);">
                        <i class="fa-solid fa-check-double mr-2"></i> Concluir Nota
                    </button>
                </form>
            @endif

            @if($invoice->status === 'rascunho')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary" style="background: var(--orange); border-color: var(--orange); box-shadow: 0 8px 16px -4px var(--orange-bg);">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Editar Rascunho
                </a>
            @endif

            @if($invoice->status !== 'cancelada' && $invoice->status !== 'concluída')
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta nota?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="color: var(--red); border-color: var(--red-bg);">
                        <i class="fa-solid fa-ban mr-2"></i> Cancelar Nota
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Main Invoice Sheet Document --}}
    <div class="invoice-sheet">
        <div style="font-size: 1.15rem; font-weight: 800; text-align: center; margin-bottom: 1.5rem; text-transform: uppercase; color: var(--text-primary); letter-spacing: 0.05em;">
            Nota Fiscal Modelo 1
        </div>

        {{-- 1. IDENTIFICAÇÃO DO EMITENTE & DADOS DA NOTA (ROW 1) --}}
        <table>
            <tr>
                <!-- Logotipo -->
                <td rowspan="2" style="width: 15%; text-align: center; vertical-align: middle; padding: 0.5rem;">
                    @if($invoice->logo)
                        <img src="{{ $invoice->logo }}" style="max-height: 45px; max-width: 100%;">
                    @else
                        <div style="font-size: 0.8rem; font-weight: bold; color: var(--text-muted); border: 1.5px dashed var(--border); padding: 12px 2px; border-radius: var(--r-sm);">LOGO</div>
                    @endif
                </td>
                <!-- Dados Emitente -->
                <td rowspan="2" style="width: 45%;">
                    <span class="box-title">IDENTIFICAÇÃO DO EMITENTE</span>
                    <span style="font-size: 1.1rem; font-weight: bold; display: block; margin-bottom: 0.25rem; color: var(--text-primary);">{{ $invoice->issuer_name }}</span>
                    <span style="font-size: 0.8rem; display: block; line-height: 1.3; color: var(--text-secondary);">
                        {{ $invoice->issuer_address }}<br>
                        CEP: {{ $invoice->issuer_zip }} - {{ $invoice->issuer_city }} - {{ $invoice->issuer_state }} - Fone: (11) 3344-5566
                    </span>
                </td>
                <!-- Dados Identificação Nota -->
                <td style="width: 15%; text-align: center; vertical-align: middle; padding: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 0.25rem; color: var(--text-primary); text-transform: uppercase;">Tipo da Nota</span>
                    <div style="font-size: 0.75rem; text-align: left; padding-left: 8px; color: var(--text-secondary); line-height: 1.4;">
                        <span class="checkbox-box">{{ $invoice->type === 'entrada' ? 'X' : ' ' }}</span> 0 - ENTRADA<br>
                        <span class="checkbox-box" style="margin-top: 4px;">{{ $invoice->type === 'saida' ? 'X' : ' ' }}</span> 1 - SAÍDA
                    </div>
                </td>
                <!-- CNPJ e IE -->
                <td style="width: 25%;" colspan="2">
                    <span class="box-title">CNPJ / IE DO EMITENTE</span>
                    <span class="box-value">{{ $invoice->formatted_issuer_cnpj }}</span>
                    <span class="box-value" style="font-size: 0.8rem; font-weight: normal; margin-top: 2px; color: var(--text-secondary);">IE: 123.456.789.110</span>
                </td>
            </tr>
            <tr>
                <!-- Via -->
                <td style="text-align: center; vertical-align: middle;">
                    <span style="font-size: 0.85rem; font-weight: bold; color: var(--text-primary);">1ª VIA</span>
                </td>
                <!-- Número / Série -->
                <td style="width: 13%;">
                    <span class="box-title">NÚMERO</span>
                    <span class="box-value" style="font-size: 1rem; color: var(--red);">Nº {{ str_pad(str_replace('NF-', '', $invoice->number), 6, '0', STR_PAD_LEFT) }}</span>
                </td>
                <td style="width: 12%;">
                    <span class="box-title">SÉRIE</span>
                    <span class="box-value" style="font-size: 1rem;">{{ $invoice->series }}</span>
                </td>
            </tr>
        </table>
            
        {{-- 2. DADOS DA NATUREZA DA OPERAÇÃO, CFOP, IMPOSTOS, CNPJ/IE EMITENTE (ROW 2) --}}
        <table class="no-top-border">
            <tr>
                <!-- Natureza da Operação -->
                <td style="width: 50%;">
                    <span class="box-title">NATUREZA DA OPERAÇÃO</span>
                    <span class="box-value">{{ $invoice->type === 'saida' ? 'VENDA DE MERCADORIA' : 'COMPRA PARA INDUSTRIALIZACAO' }}</span>
                </td>
                <!-- CFOP -->
                <td style="width: 10%; text-align: center;">
                    <span class="box-title">CFOP</span>
                    <span class="box-value">{{ $invoice->items->first()->cfop ?? '' }}</span>
                </td>
                <!-- Insc Estadual do Subst Trib -->
                <td style="width: 25%;">
                    <span class="box-title">INSC. ESTADUAL DO SUBST. TRIB.</span>
                    <span class="box-value">-</span>
                </td>
                <!-- IE Emitente -->
                <td style="width: 15%;">
                    <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                    <span class="box-value">123.456.789.110</span>
                </td>
            </tr>
        </table>

        {{-- 3. DESTINATÁRIO / REMETENTE --}}
        <div class="section-title">DESTINATÁRIO / REMETENTE</div>
        <table>
            <tr>
                <!-- Nome / Razão Social -->
                <td style="width: 70%;">
                    <span class="box-title">NOME / RAZÃO SOCIAL</span>
                    <span class="box-value">{{ $invoice->recipient_name }}</span>
                </td>
                <!-- CNPJ / CPF -->
                <td style="width: 15%;">
                    <span class="box-title">CNPJ / CPF</span>
                    <span class="box-value">{{ $invoice->formatted_recipient_document ?: '-' }}</span>
                </td>
                <!-- Data da Emissão -->
                <td style="width: 15%;">
                    <span class="box-title">DATA DA EMISSÃO</span>
                    <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}</span>
                </td>
            </tr>
        </table>
            
        <table class="no-top-border">
            <tr>
                <!-- Endereço -->
                <td style="width: 50%;">
                    <span class="box-title">ENDEREÇO</span>
                    <span class="box-value">{{ $invoice->recipient_address ?: '-' }}</span>
                </td>
                <!-- Bairro / Distrito -->
                <td style="width: 20%;">
                    <span class="box-title">BAIRRO / DISTRITO</span>
                    <span class="box-value">CENTRO</span>
                </td>
                <!-- CEP -->
                <td style="width: 15%;">
                    <span class="box-title">CEP</span>
                    <span class="box-value">{{ $invoice->recipient_zip ?: '-' }}</span>
                </td>
                <!-- Data Saida / Entrada -->
                <td style="width: 15%;">
                    <span class="box-title">DATA SAÍDA / ENTRADA</span>
                    <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}</span>
                </td>
            </tr>
        </table>
            
        <table class="no-top-border">
            <tr>
                <!-- Município -->
                <td style="width: 45%;">
                    <span class="box-title">MUNICÍPIO</span>
                    <span class="box-value">{{ $invoice->recipient_city ?: '-' }}</span>
                </td>
                <!-- Fone / Fax -->
                <td style="width: 15%;">
                    <span class="box-title">FONE / FAX</span>
                    <span class="box-value">{{ $invoice->recipient_phone ?: '-' }}</span>
                </td>
                <!-- UF -->
                <td style="width: 10%; text-align: center;">
                    <span class="box-title">UF</span>
                    <span class="box-value" style="text-transform: uppercase;">{{ $invoice->recipient_state ?: '-' }}</span>
                </td>
                <!-- Inscrição Estadual -->
                <td style="width: 15%;">
                    <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                    <span class="box-value">ISENTO</span>
                </td>
                <!-- Hora da Saída -->
                <td style="width: 15%;">
                    <span class="box-title">HORA DA SAÍDA</span>
                    <span class="box-value">{{ $invoice->issued_at ? $invoice->issued_at->format('H:i') : '-' }}</span>
                </td>
            </tr>
        </table>

        {{-- 4. FATURA --}}
        <div class="section-title">FATURA</div>
        <table>
            <tr>
                <td style="width: 100%; padding: 0.75rem 1rem;">
                    <span class="box-title">FATURA / DUPLICATAS</span>
                    <span class="box-value" style="font-size: 0.85rem;"><i class="fa-solid fa-wallet mr-2" style="color: var(--blue);"></i> PAGAMENTO VIA: {{ strtoupper($invoice->paymentLabel()) }}</span>
                </td>
            </tr>
        </table>

        {{-- 5. CÁLCULO DO IMPOSTO --}}
        <div class="section-title">CÁLCULO DO IMPOSTO</div>
        <table>
            <tr>
                <td style="width: 20%;">
                    <span class="box-title">BASE DE CÁLCULO DO ICMS</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_base'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">VALOR DO ICMS</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_value'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">BASE DE CÁLCULO ICMS SUBST.</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_st_base'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">VALOR DO ICMS SUBSTITUIÇÃO</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('icms_st_value'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">VALOR TOTAL DOS PRODUTOS</span>
                    <span class="box-value">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                </td>
            </tr>
        </table>
        <table class="no-top-border">
            <tr>
                <td style="width: 15%;">
                    <span class="box-title">VALOR DO FRETE</span>
                    <span class="box-value">R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</span>
                </td>
                <td style="width: 15%;">
                    <span class="box-title">VALOR DO SEGURO</span>
                    <span class="box-value">R$ 0,00</span>
                </td>
                <td style="width: 15%;">
                    <span class="box-title">DESCONTO</span>
                    <span class="box-value">R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span>
                </td>
                <td style="width: 15%;">
                    <span class="box-title">OUTRAS DESPESAS ACESSÓRIAS</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('ii_value'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">VALOR TOTAL DO IPI</span>
                    <span class="box-value">R$ {{ number_format($invoice->items->sum('ipi_value'), 2, ',', '.') }}</span>
                </td>
                <td style="width: 20%; background-color: var(--accent-subtle);">
                    <span class="box-title" style="color: var(--text-primary);">VALOR TOTAL DA NOTA</span>
                    <span class="box-value" style="font-size: 1.15rem; font-weight: bold; color: var(--text-primary);">R$ {{ number_format($invoice->total, 2, ',', '.') }}</span>
                </td>
            </tr>
        </table>

        {{-- Tributos Consolidados (Reforma 2026 Integrada) --}}
        <div class="section-title">Tributos Consolidados da Nota Fiscal (2026)</div>
        <table>
            <tr>
                <td style="width: 25%;">
                    <span class="box-title">ICMS Próprio & ST</span>
                    <span class="box-value" style="font-size: 0.8rem; font-weight: normal; line-height: 1.4;">
                        ICMS: <strong>R$ {{ number_format($invoice->items->sum('icms_value'), 2, ',', '.') }}</strong><br>
                        ICMS ST: <strong>R$ {{ number_format($invoice->items->sum('icms_st_value'), 2, ',', '.') }}</strong>
                    </span>
                </td>
                <td style="width: 25%;">
                    <span class="box-title">IPI, PIS & COFINS</span>
                    <span class="box-value" style="font-size: 0.8rem; font-weight: normal; line-height: 1.4;">
                        IPI: <strong>R$ {{ number_format($invoice->items->sum('ipi_value'), 2, ',', '.') }}</strong><br>
                        PIS: <strong>R$ {{ number_format($invoice->items->sum('pis_value'), 2, ',', '.') }}</strong><br>
                        COFINS: <strong>R$ {{ number_format($invoice->items->sum('cofins_value'), 2, ',', '.') }}</strong>
                    </span>
                </td>
                <td style="width: 25%;">
                    <span class="box-title">ISS & Retenções</span>
                    <span class="box-value" style="font-size: 0.8rem; font-weight: normal; line-height: 1.4;">
                        ISS: <strong>R$ {{ number_format($invoice->items->sum('iss_value'), 2, ',', '.') }}</strong><br>
                        CSLL: <strong>R$ {{ number_format($invoice->items->sum('csll_value'), 2, ',', '.') }}</strong><br>
                        IRPJ / CPP: <strong>R$ {{ number_format($invoice->items->sum('irpj_value') + $invoice->items->sum('cpp_value'), 2, ',', '.') }}</strong>
                    </span>
                </td>
                <td style="width: 25%; background-color: var(--blue-bg);">
                    <span class="box-title" style="color: var(--blue);">Total Tributos (2026)</span>
                    <span class="box-value" style="font-size: 1.05rem; color: var(--blue); font-weight: 800; margin-top: 5px;">
                        R$ {{ number_format(
                            $invoice->items->sum('icms_value') +
                            $invoice->items->sum('icms_st_value') +
                            $invoice->items->sum('ipi_value') +
                            $invoice->items->sum('pis_value') +
                            $invoice->items->sum('cofins_value') +
                            $invoice->items->sum('iss_value') +
                            $invoice->items->sum('csll_value') +
                            $invoice->items->sum('irpj_value') +
                            $invoice->items->sum('cpp_value') +
                            $invoice->items->sum('ibs_value') +
                            $invoice->items->sum('cbs_value') +
                            $invoice->items->sum('is_value') +
                            $invoice->items->sum('ii_value'),
                            2, ',', '.'
                        ) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- 6. TRANSPORTADOR / VOLUMES TRANSPORTADOS --}}
        @php
            $totalWeight = $invoice->items->sum(function($item) {
                $w = floatval($item->product->weight ?? 0);
                return floatval($item->quantity) * $w;
            });
        @endphp
        <div class="section-title">TRANSPORTADOR / VOLUMES TRANSPORTADOS</div>
        <table>
            <tr>
                <td style="width: 40%;">
                    <span class="box-title">NOME / RAZÃO SOCIAL</span>
                    <span class="box-value">{{ $invoice->issuer_name }} (Próprio)</span>
                </td>
                <td style="width: 20%; padding: 0.5rem 0.75rem;">
                    <span class="box-title">FRETE POR CONTA</span>
                    <div style="font-size: 0.75rem; line-height: 1; margin-top: 4px; color: var(--text-primary); font-weight: bold;">
                        <span class="checkbox-box">X</span> 1 - EMITENTE
                        <span class="checkbox-box" style="margin-left: 4px;"> </span> 2 - DEST
                    </div>
                </td>
                <td style="width: 15%;">
                    <span class="box-title">PLACA DO VEÍCULO</span>
                    <span class="box-value">-</span>
                </td>
                <td style="width: 5%; text-align: center;">
                    <span class="box-title">UF</span>
                    <span class="box-value">{{ $invoice->issuer_state }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">CNPJ / CPF</span>
                    <span class="box-value">{{ $invoice->formatted_issuer_cnpj }}</span>
                </td>
            </tr>
        </table>
        <table class="no-top-border">
            <tr>
                <td style="width: 50%;">
                    <span class="box-title">ENDEREÇO</span>
                    <span class="box-value">{{ $invoice->issuer_address }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="box-title">MUNICÍPIO</span>
                    <span class="box-value">{{ $invoice->issuer_city }}</span>
                </td>
                <td style="width: 5%; text-align: center;">
                    <span class="box-title">UF</span>
                    <span class="box-value">{{ $invoice->issuer_state }}</span>
                </td>
                <td style="width: 20%;">
                    <span class="box-title">INSCRIÇÃO ESTADUAL</span>
                    <span class="box-value">123.456.789.110</span>
                </td>
            </tr>
        </table>
        <table class="no-top-border">
            <tr>
                <td style="width: 12.5%;">
                    <span class="box-title">QUANTIDADE</span>
                    <span class="box-value">{{ number_format($invoice->items->sum('quantity'), 0) }}</span>
                </td>
                <td style="width: 12.5%;">
                    <span class="box-title">ESPÉCIE</span>
                    <span class="box-value">VOLUMES</span>
                </td>
                <td style="width: 12.5%;">
                    <span class="box-title">MARCA</span>
                    <span class="box-value">-</span>
                </td>
                <td style="width: 12.5%;">
                    <span class="box-title">NÚMERO</span>
                    <span class="box-value">-</span>
                </td>
                <td style="width: 25%;">
                    <span class="box-title">PESO BRUTO</span>
                    <span class="box-value">{{ $totalWeight > 0 ? number_format($totalWeight, 3, ',', '.') . ' kg' : '-' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="box-title">PESO LÍQUIDO</span>
                    <span class="box-value">{{ $totalWeight > 0 ? number_format($totalWeight, 3, ',', '.') . ' kg' : '-' }}</span>
                </td>
            </tr>
        </table>

        {{-- 7. DADOS DO PRODUTO / SERVIÇO --}}
        <div class="section-title">DADOS DO PRODUTO / SERVIÇOS</div>
        <table>
            <thead>
                <tr style="background-color: var(--bg-hover);">
                    <th rowspan="2" style="width: 9%; font-size: 0.7rem; font-weight: bold; text-align: left; vertical-align: middle;">CÓDIGO</th>
                    <th rowspan="2" style="width: 32%; font-size: 0.7rem; font-weight: bold; text-align: left; vertical-align: middle;">DESCRIÇÃO DOS PRODUTOS/SERVIÇOS</th>
                    <th rowspan="2" style="width: 9%; font-size: 0.7rem; font-weight: bold; text-align: center; vertical-align: middle;">NCM/SH</th>
                    <th rowspan="2" style="width: 6%; font-size: 0.7rem; font-weight: bold; text-align: center; vertical-align: middle;">CST</th>
                    <th rowspan="2" style="width: 5%; font-size: 0.7rem; font-weight: bold; text-align: center; vertical-align: middle;">UNID</th>
                    <th rowspan="2" style="width: 8%; font-size: 0.7rem; font-weight: bold; text-align: right; vertical-align: middle;">QUANT</th>
                    <th rowspan="2" style="width: 8%; font-size: 0.7rem; font-weight: bold; text-align: right; vertical-align: middle;">V. UNIT</th>
                    <th rowspan="2" style="width: 9%; font-size: 0.7rem; font-weight: bold; text-align: right; vertical-align: middle;">V. TOTAL</th>
                    <th colspan="2" style="width: 8%; font-size: 0.7rem; font-weight: bold; text-align: center; border-bottom: 1px solid var(--border); padding: 4px;">ALÍQUOTAS</th>
                    <th rowspan="2" style="width: 6%; font-size: 0.7rem; font-weight: bold; text-align: right; vertical-align: middle;">V. IPI</th>
                </tr>
                <tr style="background-color: var(--bg-hover);">
                    <th style="width: 4%; font-size: 0.6rem; text-align: center; padding: 2px;">ICMS</th>
                    <th style="width: 4%; font-size: 0.6rem; text-align: center; padding: 2px;">IPI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                <tr>
                    <td style="font-family: monospace; font-size: 0.8rem; vertical-align: middle;">{{ $item->product->barcode ?? '0000' }}</td>
                    <td>
                        <div style="font-weight: bold; color: var(--text-primary);">{{ $item->description }}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">SKU/ID: {{ $item->product_id ?: 'MANUAL' }}</div>
                        <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 6px; font-size: 0.65rem; margin-top: 4px; border-radius: 4px; height: auto;" onclick="toggleDetailsRow({{ $item->id }})">
                            <i class="fa-solid fa-calculator mr-1"></i> Detalhes Tributários
                        </button>
                    </td>
                    <td style="font-family: monospace; text-align: center; vertical-align: middle;">{{ $item->ncm }}</td>
                    <td style="text-align: center; vertical-align: middle;">{{ $item->icms_cst ?: '00' }}</td>
                    <td style="text-align: center; vertical-align: middle;">{{ strtoupper($item->unit) }}</td>
                    <td style="text-align: right; vertical-align: middle; font-weight: 700;">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td style="text-align: right; vertical-align: middle;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td style="text-align: right; vertical-align: middle; font-weight: 800; color: var(--accent-hover);">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                    <td style="text-align: center; vertical-align: middle;">{{ number_format($item->icms_rate, 1, ',', '.') }}%</td>
                    <td style="text-align: center; vertical-align: middle;">{{ number_format($item->ipi_rate, 1, ',', '.') }}%</td>
                    <td style="text-align: right; vertical-align: middle;">R$ {{ number_format($item->ipi_value, 2, ',', '.') }}</td>
                </tr>
                {{-- Expandable Tax Detail Row --}}
                <tr id="details-row-{{ $item->id }}" style="display: none; background: var(--bg-hover);">
                    <td colspan="11" style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.8rem;">
                            <div class="card p-4" style="border: 1px solid var(--border); background: var(--bg-surface);">
                                <div style="font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 4px; margin-bottom: 6px; color: var(--blue);">ICMS & ICMS ST</div>
                                <div>CST: {{ $item->icms_cst ?: '00' }} | Origem: {{ $item->icms_orig }}</div>
                                <div>BC ICMS: R$ {{ number_format($item->icms_base, 2, ',', '.') }}</div>
                                <div>Alíquota: {{ number_format($item->icms_rate, 2, ',', '.') }}% | Valor: R$ {{ number_format($item->icms_value, 2, ',', '.') }}</div>
                                <div style="margin-top: 8px; border-top: 1px dashed var(--border); padding-top: 6px;">
                                    <div>CST ST: {{ $item->icms_st_cst ?: '10' }} | MVA: {{ number_format($item->icms_st_mva, 2, ',', '.') }}%</div>
                                    <div>BC ST: R$ {{ number_format($item->icms_st_base, 2, ',', '.') }}</div>
                                    <div>Alíquota ST: {{ number_format($item->icms_st_rate, 2, ',', '.') }}% | Valor ST: R$ {{ number_format($item->icms_st_value, 2, ',', '.') }}</div>
                                </div>
                            </div>
                            
                            <div class="card p-4" style="border: 1px solid var(--border); background: var(--bg-surface);">
                                <div style="font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 4px; margin-bottom: 6px; color: var(--red);">IPI, PIS & COFINS</div>
                                <div>IPI CST: {{ $item->ipi_cst ?: '50' }} | Enq: {{ $item->ipi_enq ?: '999' }}</div>
                                <div>BC IPI: R$ {{ number_format($item->ipi_base, 2, ',', '.') }} | Alíq: {{ number_format($item->ipi_rate, 2, ',', '.') }}% | Valor: R$ {{ number_format($item->ipi_value, 2, ',', '.') }}</div>
                                <div style="margin-top: 8px; border-top: 1px dashed var(--border); padding-top: 6px;">
                                    <div>PIS CST: {{ $item->pis_cst ?: '01' }} | BC PIS: R$ {{ number_format($item->pis_base, 2, ',', '.') }}</div>
                                    <div>Alíq PIS: {{ number_format($item->pis_rate, 2, ',', '.') }}% | Valor PIS: R$ {{ number_format($item->pis_value, 2, ',', '.') }}</div>
                                </div>
                                <div style="margin-top: 8px; border-top: 1px dashed var(--border); padding-top: 6px;">
                                    <div>COFINS CST: {{ $item->cofins_cst ?: '01' }} | BC COF: R$ {{ number_format($item->cofins_base, 2, ',', '.') }}</div>
                                    <div>Alíq COF: {{ number_format($item->cofins_rate, 2, ',', '.') }}% | Valor COF: R$ {{ number_format($item->cofins_value, 2, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="card p-4" style="border: 1px solid var(--border); background: var(--bg-surface);">
                                <div style="font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 4px; margin-bottom: 6px; color: var(--orange);">Retenções & ISS</div>
                                <div>ISS CST: {{ $item->iss_cst ?: '01' }} | BC ISS: R$ {{ number_format($item->iss_base, 2, ',', '.') }}</div>
                                <div>Alíq: {{ number_format($item->iss_rate, 2, ',', '.') }}% | Valor ISS: R$ {{ number_format($item->iss_value, 2, ',', '.') }}</div>
                                <div style="margin-top: 8px; border-top: 1px dashed var(--border); padding-top: 6px;">
                                    <div>CSLL: {{ number_format($item->csll_rate, 2, ',', '.') }}% | Valor CSLL: R$ {{ number_format($item->csll_value, 2, ',', '.') }}</div>
                                    <div>IRPJ: {{ number_format($item->irpj_rate, 2, ',', '.') }}% | Valor IRPJ: R$ {{ number_format($item->irpj_value, 2, ',', '.') }}</div>
                                    <div>CPP: {{ number_format($item->cpp_rate, 2, ',', '.') }}% | Valor CPP: R$ {{ number_format($item->cpp_value, 2, ',', '.') }}</div>
                                </div>
                            </div>

                            <div class="card p-4" style="border: 1px solid var(--border); background: var(--bg-surface);">
                                <div style="font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 4px; margin-bottom: 6px; color: var(--green);">Reforma 2026 & Importação</div>
                                <div>IBS CST: {{ $item->ibs_cst ?: '01' }} | Alíq: {{ number_format($item->ibs_rate, 2, ',', '.') }}% | Valor: R$ {{ number_format($item->ibs_value, 2, ',', '.') }}</div>
                                <div>CBS CST: {{ $item->cbs_cst ?: '01' }} | Alíq: {{ number_format($item->cbs_rate, 2, ',', '.') }}% | Valor: R$ {{ number_format($item->cbs_value, 2, ',', '.') }}</div>
                                <div>IS CST: {{ $item->is_cst ?: '01' }} | Alíq: {{ number_format($item->is_rate, 2, ',', '.') }}% | Valor: R$ {{ number_format($item->is_value, 2, ',', '.') }}</div>
                                @if($item->ii_value > 0 || $item->ii_desp > 0 || $item->ii_iof > 0)
                                    <div style="margin-top: 8px; border-top: 1px dashed var(--border); padding-top: 6px;">
                                        <div>II BC: R$ {{ number_format($item->ii_base, 2, ',', '.') }} | Valor: R$ {{ number_format($item->ii_value, 2, ',', '.') }}</div>
                                        <div>Despesas: R$ {{ number_format($item->ii_desp, 2, ',', '.') }} | IOF: R$ {{ number_format($item->ii_iof, 2, ',', '.') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 8. DADOS ADICIONAIS --}}
        <div class="section-title">DADOS ADICIONAIS</div>
        <table>
            <tr>
                <td style="width: 60%; min-height: 80px;">
                    <span class="box-title">INFORMAÇÕES COMPLEMENTARES</span>
                    <span style="font-size: 0.85rem; display: block; line-height: 1.4; color: var(--text-secondary); white-space: pre-wrap;">{{ $invoice->notes ?: '—' }}<br><small style="color:var(--text-muted);">Documento emitido para fins de simulação e controle interno.</small></span>
                </td>
                <td style="width: 25%; min-height: 80px;">
                    <span class="box-title">RESERVADO AO FISCO</span>
                    <span class="box-value">—</span>
                </td>
                <td style="width: 15%; min-height: 80px; text-align: center; padding: 0.5rem;">
                    <span style="font-size: 0.6rem; font-weight: bold; display: block; text-transform: uppercase; line-height: 1.2; color: var(--text-muted);">Nº DE CONTROLE DO FORMULÁRIO</span>
                    <span style="font-size: 1rem; font-weight: bold; display: block; margin-top: 0.5rem;">000.000</span>
                </td>
            </tr>
        </table>

        {{-- 9. CONFERÊNCIA DA NOTA FISCAL (INTERNAL WORKFLOW) --}}
        <div class="section-title" style="margin-top: 2rem; border-top: 1px dashed var(--border); padding-top: 1.5rem;">Conferência da Nota Fiscal</div>
        
        <div style="background: var(--bg-hover); border: 1.5px solid var(--border); border-radius: var(--r-md); padding: 1.5rem; display:flex; flex-direction:column; gap:1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--orange-bg); color:var(--orange); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem;">Status e Registro de Conferência</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">Vincule o responsável físico e registre observações de auditoria</div>
                    </div>
                </div>
                @php
                    $confColors = [
                        'Pendente' => ['bg' => 'var(--orange-bg)', 'color' => 'var(--orange)', 'icon' => 'fa-clock'],
                        'Conferida' => ['bg' => 'var(--green-bg)', 'color' => 'var(--green)', 'icon' => 'fa-circle-check'],
                        'Divergente' => ['bg' => 'var(--red-bg)', 'color' => 'var(--red)', 'icon' => 'fa-circle-xmark'],
                    ][$invoice->conference_status] ?? ['bg' => 'var(--orange-bg)', 'color' => 'var(--orange)', 'icon' => 'fa-clock'];
                @endphp
                <div class="badge" style="background: {{ $confColors['bg'] }}; color: {{ $confColors['color'] }}; font-weight: 800; font-size: 0.85rem; padding: 0.5rem 1rem;">
                    <i class="fa-solid {{ $confColors['icon'] }} mr-2"></i> {{ $invoice->conference_status ?? 'Pendente' }}
                </div>
            </div>

            {{-- Info / Detail Block --}}
            <div id="conference-details-panel" style="{{ $invoice->conference_status !== 'Pendente' ? '' : 'display:none;' }}">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Responsável</div>
                        <div style="font-size:0.95rem; font-weight:600; color:var(--text-primary);">
                            <i class="fa-solid fa-user-circle mr-1" style="color:var(--text-secondary);"></i>
                            {{ $invoice->conferredBy->name ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Data / Hora</div>
                        <div style="font-size:0.95rem; font-weight:600; color:var(--text-primary);">
                            <i class="fa-solid fa-calendar mr-1" style="color:var(--text-secondary);"></i>
                            {{ $invoice->conferred_at ? $invoice->conferred_at->format('d/m/Y H:i') : '—' }}
                        </div>
                    </div>
                </div>
                
                @if($invoice->conference_notes)
                    <div style="background:var(--bg-base); border:1px solid var(--border); border-radius:var(--r-md); padding:1rem; margin-bottom:1rem;">
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem; display:flex; align-items:center; gap:0.4rem;">
                            <i class="fa-solid fa-comment-dots"></i> Observações da Conferência
                        </div>
                        <p style="margin:0; font-size:0.9rem; color:var(--text-primary); white-space:pre-wrap;">{{ $invoice->conference_notes }}</p>
                    </div>
                @endif

                <button type="button" class="btn btn-secondary" style="height: auto; padding: 0.5rem 1rem;" onclick="toggleConferenceForm()">
                    <i class="fa-solid fa-arrows-rotate mr-2"></i> Alterar Status / Observações
                </button>
            </div>

            {{-- Form Block --}}
            <div id="conference-form-panel" style="{{ $invoice->conference_status === 'Pendente' ? '' : 'display:none;' }}">
                <form action="{{ route('invoices.confer', $invoice) }}" method="POST" style="display:flex; flex-direction:column; gap:1rem; width: 100%;">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Status da Conferência <span style="color:var(--red);">*</span></label>
                        <select name="conference_status" id="conference_status_select" required class="form-select" style="max-width:320px;">
                            <option value="Pendente" {{ $invoice->conference_status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Conferida" {{ $invoice->conference_status === 'Conferida' ? 'selected' : '' }}>Conferida (Sem divergências)</option>
                            <option value="Divergente" {{ $invoice->conference_status === 'Divergente' ? 'selected' : '' }}>Divergente (Possui pendências/erros)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Observações da Conferência</label>
                        <textarea name="conference_notes" rows="3" class="form-textarea" placeholder="Descreva observações, divergências encontradas, justificativas, etc.">{{ $invoice->conference_notes }}</textarea>
                    </div>

                    <div style="display:flex; gap:0.75rem; align-items:center;">
                        <button type="submit" class="btn btn-primary" style="height: auto; padding: 0.5rem 1.25rem;">
                            <i class="fa-solid fa-save mr-2"></i> Salvar Conferência
                        </button>
                        
                        @if($invoice->conference_status !== 'Pendente')
                            <button type="button" class="btn btn-secondary" style="height: auto; padding: 0.5rem 1.25rem;" onclick="toggleConferenceForm()">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Interactive Conference Button --}}
            <div style="border-top:1px solid var(--border); padding-top:1rem;">
                <a href="{{ route('invoices.confer-workflow', $invoice) }}" class="btn btn-secondary" style="width:100%; justify-content:center; padding:0.75rem; background:var(--accent-subtle); color:var(--accent); border-color:var(--accent-subtle); font-weight:700; font-size:0.9rem;">
                    <i class="fa-solid fa-barcode" style="margin-right:0.5rem;"></i> Abrir Conferência Interativa (Bipe)
                </a>
                <p style="margin:0.4rem 0 0; font-size:0.75rem; color:var(--text-muted); text-align:center;">Utilize o leitor de código de barras para conferir cada item</p>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    function toggleConferenceForm() {
        const details = document.getElementById('conference-details-panel');
        const form = document.getElementById('conference-form-panel');
        
        if (form.style.display === 'none') {
            form.style.display = '';
            details.style.display = 'none';
        } else {
            form.style.display = 'none';
            details.style.display = '';
        }
    }

    function toggleDetailsRow(id) {
        const row = document.getElementById('details-row-' + id);
        if (row) {
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
    }
 
    @if(session('open_romaneio'))
        window.addEventListener('DOMContentLoaded', () => {
            window.open("{{ route('invoices.romaneio', $invoice) }}", "_blank");
        });
    @endif
</script>
@endpush
@endsection
