@extends('layouts.app')

@section('title', 'Emissão NF-e')
@section('page-title', 'Emissão NF-e')
@section('page-subtitle', 'Gerenciamento e monitoramento de notas fiscais emitidas')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    {{-- Header Action Card --}}
    <div class="card" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border: none; padding: 2.5rem; position: relative; overflow: hidden;">
        <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <h2 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.75rem;">Gestão de Faturamento</h2>
                <p style="opacity: 0.85; font-size: 1rem; max-width: 550px; line-height: 1.6;">
                    Emita novas notas fiscais de saída ou entrada, gerencie rascunhos e acompanhe o status de processamento fiscal em tempo real.
                </p>
            </div>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem; box-shadow: 0 15px 30px -10px var(--accent-glow);">
                <i class="fa-solid fa-plus-circle mr-2"></i> Emitir Nova NF-e
            </a>
        </div>
        <div class="anim-float" style="position: absolute; right: 5%; top: -30px; font-size: 12rem; opacity: 0.07; pointer-events: none;">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body" style="padding: 1.25rem;">
            <form method="GET" action="{{ route('invoices.index') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <div style="position: relative; flex: 1; min-width: 280px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                    <input type="text" name="search" placeholder="Buscar por número, série ou destinatário..." 
                           value="{{ request('search') }}"
                           class="form-control"
                           style="width: 100%; padding-left: 3rem;">
                </div>
                
                <select name="status" class="form-control" style="width: auto; min-width: 160px;">
                    <option value="">Status: Todos</option>
                    <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                    <option value="emitida" {{ request('status') == 'emitida' ? 'selected' : '' }}>Emitida</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>

                <select name="type" class="form-control" style="width: auto; min-width: 160px;">
                    <option value="">Tipo: Todos</option>
                    <option value="saida" {{ request('type') == 'saida' ? 'selected' : '' }}>Saída (Venda)</option>
                    <option value="entrada" {{ request('type') == 'entrada' ? 'selected' : '' }}>Entrada (Compra)</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter mr-2"></i> Filtrar
                </button>
                
                @if(request()->anyFilled(['search', 'status', 'type']))
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark mr-2"></i> Limpar
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-wrap">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 160px;">Número / Série</th>
                        <th>Tipo</th>
                        <th>Destinatário</th>
                        <th>Data Emissão</th>
                        <th style="text-align: right;">Valor Total</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="anim-entrance" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                            <td>
                                <div style="font-family: 'Outfit'; font-weight: 700; color: var(--accent); font-size: 1rem;">{{ $invoice->number }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Série {{ $invoice->series }}</div>
                            </td>
                            <td>
                                @if($invoice->type === 'saida')
                                    <span class="badge" style="background: var(--blue-bg); color: var(--blue); font-size: 0.7rem;">
                                        <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Saída
                                    </span>
                                @else
                                    <span class="badge" style="background: var(--accent-subtle); color: var(--accent); font-size: 0.7rem;">
                                        <i class="fa-solid fa-arrow-down-left-and-arrow-up-right-to-center mr-1"></i> Entrada
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $invoice->recipient_name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $invoice->recipient_document ?: 'Sem documento' }}</div>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.9rem;">
                                {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}
                            </td>
                            <td style="text-align: right;">
                                <div style="font-weight: 800; color: var(--text-primary); font-family: 'Outfit'; font-size: 1.1rem;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</div>
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $statusStyle = [
                                        'rascunho' => ['bg' => 'var(--orange-bg)', 'color' => 'var(--orange)'],
                                        'emitida' => ['bg' => 'var(--green-bg)', 'color' => 'var(--green)'],
                                        'cancelada' => ['bg' => 'var(--red-bg)', 'color' => 'var(--red)']
                                    ][$invoice->status] ?? ['bg' => 'var(--bg-hover)', 'color' => 'var(--text-muted)'];
                                @endphp
                                <span class="badge" style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }};">
                                    {{ $invoice->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.75rem; justify-content: center;">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="icon-btn" title="Visualizar Detalhes">
                                        <i class="fa-solid fa-eye" style="font-size: 0.9rem;"></i>
                                    </a>
                                    
                                    @if($invoice->status === 'emitida')
                                        <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="icon-btn" style="color: var(--green);" title="Baixar DANFE (PDF)">
                                            <i class="fa-solid fa-file-pdf" style="font-size: 0.9rem;"></i>
                                        </a>
                                    @endif

                                    @if($invoice->status === 'rascunho')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="icon-btn" style="color: var(--orange);" title="Editar Rascunho">
                                            <i class="fa-solid fa-pen-to-square" style="font-size: 0.9rem;"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div style="padding: 5rem 2rem; text-align: center;">
                                    <div class="anim-float" style="width:100px; height:100px; background:var(--bg-hover); border-radius:32px; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem; box-shadow: var(--shadow-md);">
                                        <i class="fa-solid fa-file-circle-xmark" style="font-size:2.5rem; color:var(--text-muted);"></i>
                                    </div>
                                    <h3 style="font-family:'Outfit'; font-size:1.5rem; margin-bottom: 0.75rem;">Nenhuma nota encontrada</h3>
                                    <p style="color:var(--text-muted); font-size: 1rem; margin-bottom: 2rem;">Ainda não há documentos fiscais registrados com estes critérios.</p>
                                    <a href="{{ route('invoices.create') }}" class="btn btn-primary" style="padding: 0.875rem 2rem;">
                                        <i class="fa-solid fa-plus-circle mr-2"></i> Emitir Primeira Nota
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="card" style="padding: 1rem;">
        <div style="display: flex; justify-content: center;">
            {{ $invoices->links() }}
        </div>
    </div>
    @endif

</div>
@endsection
