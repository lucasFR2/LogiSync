@extends('layouts.app')

@section('title', 'Detalhes da NF-e')
@section('page-title', 'Nota Fiscal #' . $manifestation->number)
@section('page-subtitle', 'Chave: ' . $manifestation->access_key)

@section('content')
<div class="container-fluid">
    <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:1.5rem; gap:1rem;">
        <a href="{{ route('manifestations.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('manifestations.danfe', $manifestation) }}" target="_blank" class="btn btn-secondary">
            <i class="fa-solid fa-file-pdf"></i> Visualizar DANFE
        </a>
    </div>

    @include('partials.alerts')

    <div class="grid" style="grid-template-columns: 1fr 300px; gap:1.5rem; align-items:start;">
        
        <!-- Main Content -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Supplier Info -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--text-primary);">
                        <i class="fa-solid fa-building" style="color:var(--text-muted); margin-right:0.5rem;"></i> Dados do Emitente
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-2">
                        <div>
                            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.25rem;">Razão Social</p>
                            <p style="font-weight:600;">{{ $manifestation->supplier_name }}</p>
                        </div>
                        <div>
                            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.25rem;">CNPJ</p>
                            <p style="font-weight:600;">{{ $manifestation->supplier_cnpj }}</p>
                        </div>
                    </div>
                    @if($manifestation->supplier_id)
                        <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Fornecedor Cadastrado no Sistema</span>
                        </div>
                    @else
                        <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
                            <span class="badge badge-warning"><i class="fa-solid fa-triangle-exclamation"></i> Fornecedor não encontrado no cadastro local</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0;"><i class="fa-solid fa-box" style="color:var(--text-muted); margin-right:0.5rem;"></i> Produtos da NF-e</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>NCM/CFOP</th>
                                <th style="text-align:right;">Qtd</th>
                                <th style="text-align:right;">V. Unit</th>
                                <th style="text-align:right;">V. Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manifestation->items as $item)
                                <tr>
                                    <td style="font-weight:600;">{{ $item->description }}</td>
                                    <td>
                                        <div style="font-size:0.8rem;">NCM: {{ $item->ncm }}</div>
                                        <div style="font-size:0.8rem; color:var(--text-muted);">CFOP: {{ $item->cfop }}</div>
                                    </td>
                                    <td style="text-align:right;">{{ number_format($item->quantity, 4, ',', '.') }} {{ $item->unit }}</td>
                                    <td style="text-align:right;">R$ {{ number_format($item->unit_price, 4, ',', '.') }}</td>
                                    <td style="text-align:right; font-weight:600;">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0; font-size:1.1rem;">Resumo</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:1rem;">
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.25rem;">Valor Total da Nota</p>
                        <p style="font-size:1.5rem; font-weight:800; color:var(--accent);">R$ {{ number_format($manifestation->total_amount, 2, ',', '.') }}</p>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.25rem;">Data de Emissão</p>
                        <p style="font-weight:600;">{{ $manifestation->emission_date->format('d/m/Y') }}</p>
                    </div>

                    <div style="margin-bottom:1rem;">
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.5rem;">Status na SEFAZ</p>
                        <span class="badge badge-{{ $manifestation->status_color }}" style="font-size:0.9rem; padding:0.5rem 1rem;">
                            {{ $manifestation->status_label }}
                        </span>
                    </div>

                    <div>
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.5rem;">Status no Estoque</p>
                        <span class="badge badge-{{ $manifestation->entry_status_color }}" style="font-size:0.9rem; padding:0.5rem 1rem;">
                            {{ $manifestation->entry_status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0; font-size:1.1rem;">Ações</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('manifestations.manifest', $manifestation) }}" method="POST" style="display:flex; flex-direction:column; gap:0.75rem;">
                        @csrf
                        
                        @if($manifestation->manifestation_status == 'pending')
                            <button type="submit" name="status" value="ciencia" class="btn btn-secondary" style="width:100%; justify-content:center;">
                                Registrar Ciência
                            </button>
                        @endif

                        @if(in_array($manifestation->manifestation_status, ['pending', 'ciencia']))
                            <button type="submit" name="status" value="confirmada" class="btn btn-primary" style="width:100%; justify-content:center; background-color:var(--green); border-color:var(--green);">
                                Confirmar Operação
                            </button>
                            <button type="submit" name="status" value="desconhecimento" class="btn btn-secondary" style="width:100%; justify-content:center; color:var(--red);">
                                Desconhecimento
                            </button>
                            <button type="submit" name="status" value="nao_realizada" class="btn btn-secondary" style="width:100%; justify-content:center;">
                                Operação não Realizada
                            </button>
                        @endif
                    </form>

                    @if($manifestation->manifestation_status == 'confirmada' && $manifestation->entry_status == 'pending')
                        <hr style="margin:1.5rem 0; border-color:var(--border);">
                        @if(in_array($manifestation->conference_status, ['Conferida', 'Divergente']))
                            <form action="{{ route('inventory.bulkCreate', $manifestation) }}" method="GET">
                                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:1rem;">
                                    <i class="fa-solid fa-dolly"></i> Importar para Estoque
                                </button>
                            </form>
                        @else
                            <div style="padding:1rem; background:var(--bg-hover); border:1px dashed var(--border); border-radius:var(--r-md); text-align:center; color:var(--text-muted); font-size:0.85rem; font-weight:600; line-height:1.4;">
                                <i class="fa-solid fa-lock" style="margin-right:0.35rem; color:var(--orange);"></i>
                                Importação bloqueada. Realize a conferência interativa primeiro.
                            </div>
                        @endif
                    @elseif($manifestation->entry_status == 'imported')
                        <hr style="margin:1.5rem 0; border-color:var(--border);">
                        <div style="text-align:center; color:var(--green); font-weight:600;">
                            <i class="fa-solid fa-check-circle"></i> Estoque Atualizado
                        </div>
                    @endif

                    {{-- Interactive Conference Button --}}
                    <hr style="margin:1.5rem 0; border-color:var(--border);">
                    <a href="{{ route('manifestations.confer-workflow', $manifestation) }}" class="btn btn-secondary" style="width:100%; justify-content:center; padding:0.85rem; background:var(--accent-subtle); color:var(--accent); border-color:var(--accent-subtle); font-weight:700;">
                        <i class="fa-solid fa-barcode" style="margin-right:0.5rem;"></i> Iniciar Conferência Interativa
                    </a>
                    @if($manifestation->conference_status && $manifestation->conference_status !== 'Pendente')
                        <div style="text-align:center; margin-top:0.75rem;">
                            @php
                                $confColor = match($manifestation->conference_status) {
                                    'Conferida' => 'var(--green)',
                                    'Divergente' => 'var(--red)',
                                    default => 'var(--orange)',
                                };
                                $confBg = match($manifestation->conference_status) {
                                    'Conferida' => 'var(--green-bg)',
                                    'Divergente' => 'var(--red-bg)',
                                    default => 'var(--orange-bg)',
                                };
                                $confIcon = match($manifestation->conference_status) {
                                    'Conferida' => 'fa-circle-check',
                                    'Divergente' => 'fa-circle-xmark',
                                    default => 'fa-clock',
                                };
                            @endphp
                            <span class="badge" style="background:{{ $confBg }}; color:{{ $confColor }}; font-weight:700; padding:0.4rem 0.8rem; font-size:0.85rem;">
                                <i class="fa-solid {{ $confIcon }}" style="margin-right:0.35rem;"></i> {{ $manifestation->conference_status }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
