@extends('layouts.app')

@section('title', 'Controle de Entradas')
@section('page-title', 'Registro de Entradas')
@section('page-subtitle', 'Movimentações de entrada no estoque em tempo real')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--blue-bg); color:var(--blue);">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
            <div class="stat-label">Total de Registros</div>
            <div class="stat-value">{{ $totalEntries }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-bg); color:var(--green);">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="stat-label">Neste Mês</div>
            <div class="stat-value">{{ $monthEntries }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--orange-bg); color:var(--orange);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="stat-label">Registros Hoje</div>
            <div class="stat-value">{{ $todayEntries }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-subtle); color:var(--accent);">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div class="stat-label">SKUs Ativos</div>
            <div class="stat-value">{{ $activeSKUs }}</div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Histórico Operacional</h3>
            </div>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Registrar Entrada
            </a>
        </div>

        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($inventories->count() > 0)
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produto / SKU</th>
                                <th>Fornecedor</th>
                                <th>Lote</th>
                                <th style="text-align:center;">Qtd.</th>
                                <th>Observações</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inv)
                                @php $d = $inv->entry_date ?? $inv->created_at; @endphp
                                <tr>
                                    <td style="white-space:nowrap;">
                                        <div style="font-family:'Outfit'; font-weight:700;">{{ $d->format('d/m/Y') }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">{{ $d->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;">{{ $inv->product->name }}</div>
                                        <div style="font-size:0.75rem; font-family:monospace; color:var(--text-muted);">{{ $inv->product->barcode ?? '—' }}</div>
                                    </td>
                                    <td style="font-size:0.875rem; color:var(--text-secondary);">
                                        {{ $inv->supplier?->name ?? $inv->product->supplier?->name ?? '—' }}
                                    </td>
                                    <td>
                                        <span style="font-size:0.8rem; font-family:monospace; color:var(--text-secondary);">{{ $inv->lot_number ?? '—' }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-success" style="font-weight:800; padding:0.4rem 0.8rem;">
                                            +{{ $inv->quantity }}
                                        </span>
                                    </td>
                                    <td style="color:var(--text-secondary); font-size:0.875rem; max-width:200px;">
                                        <span style="display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            {{ $inv->notes ?? '—' }}
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('products.show', $inv->product) }}" class="icon-btn" title="Ver Produto" style="width:32px;height:32px;">
                                            <i class="fa-solid fa-up-right-from-square" style="font-size:0.8rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($inventories->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $inventories->links() }}
                    </div>
                @endif
            @else
                <div style="padding:6rem 2rem; text-align:center;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-inbox" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhuma entrada registrada</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Comece movimentando seu estoque registrando a primeira entrada.</p>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary" style="padding:1rem 2rem;">
                        <i class="fa-solid fa-plus"></i> Registrar Primeira Entrada
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
