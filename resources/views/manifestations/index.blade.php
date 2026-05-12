@extends('layouts.app')

@section('title', 'Manifestação do Destinatário')
@section('page-title', 'Manifestação do Destinatário')
@section('page-subtitle', 'Gerencie notas fiscais de entrada (NF-e) emitidas contra o seu CNPJ.')

@section('content')
<div class="container-fluid">
    <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:1.5rem; gap:1rem;">
        <a href="{{ route('manifestations.generateXml') }}" target="_blank" class="btn btn-secondary">
            <i class="fa-solid fa-file-code"></i> Gerar XML de Teste
        </a>
        <button class="btn btn-primary" onclick="openXmlModal()">
            <i class="fa-solid fa-file-import"></i> Importar XML (Simulação)
        </button>
    </div>

    @include('partials.alerts')

    <div class="card" style="padding:1.5rem;">
        <form method="GET" action="{{ route('manifestations.index') }}" style="display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end; margin-bottom:1.5rem;">
            <div class="form-group" style="flex:1; min-width:250px; margin-bottom:0;">
                <label class="form-label">Pesquisar</label>
                <div style="position:relative;">
                    <i class="fa-solid fa-search" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nº, Chave, Fornecedor ou CNPJ" class="form-input" style="padding-left:2.5rem;">
                </div>
            </div>
            
            <div class="form-group" style="width:180px; margin-bottom:0;">
                <label class="form-label">Status da Manifestação</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="ciencia" {{ $status == 'ciencia' ? 'selected' : '' }}>Ciência</option>
                    <option value="confirmada" {{ $status == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="desconhecimento" {{ $status == 'desconhecimento' ? 'selected' : '' }}>Desconhecimento</option>
                    <option value="nao_realizada" {{ $status == 'nao_realizada' ? 'selected' : '' }}>Não Realizada</option>
                </select>
            </div>

            <div class="form-group" style="width:140px; margin-bottom:0;">
                <label class="form-label">Data Início</label>
                <input type="date" name="date_start" value="{{ $date_start }}" class="form-input">
            </div>

            <div class="form-group" style="width:140px; margin-bottom:0;">
                <label class="form-label">Data Fim</label>
                <input type="date" name="date_end" value="{{ $date_end }}" class="form-input">
            </div>

            <div style="display:flex; gap:0.5rem;">
                <a href="{{ route('manifestations.index') }}" class="btn btn-secondary" title="Limpar">
                    <i class="fa-solid fa-eraser"></i>
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
        </form>

        @if($invoices->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Número/Série</th>
                            <th>Fornecedor</th>
                            <th>Data Emissão</th>
                            <th>Valor Total</th>
                            <th>Manifestação</th>
                            <th>Status Entrada</th>
                            <th style="width:100px; text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                            <tr class="anim-entrance" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                <td>
                                    <div style="font-weight:600; color:var(--text-primary);">{{ $inv->number }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">Série {{ $inv->series }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:600; color:var(--text-primary);">{{ $inv->supplier_name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $inv->supplier_cnpj }}</div>
                                </td>
                                <td>{{ $inv->emission_date->format('d/m/Y') }}</td>
                                <td style="font-weight:600;">R$ {{ number_format($inv->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $inv->status_color }}">
                                        {{ $inv->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $inv->entry_status_color }}">
                                        {{ $inv->entry_status_label }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <a href="{{ route('manifestations.show', $inv) }}" class="btn btn-secondary btn-sm" title="Detalhes">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top:1.5rem;">
                {{ $invoices->links() }}
            </div>
        @else
            <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
                <i class="fa-solid fa-file-invoice" style="font-size:3rem; margin-bottom:1rem; opacity:0.5;"></i>
                <h3>Nenhuma NF-e encontrada</h3>
                <p>Faça o upload de um XML para simular a recepção de notas.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Upload XML -->
<div id="xmlModal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 style="margin:0;"><i class="fa-solid fa-file-code" style="color:var(--accent);"></i> Importar XML (Simulação)</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeXmlModal()" style="border:none; background:transparent;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('manifestations.uploadXml') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="padding:1.5rem;">
                <p style="color:var(--text-secondary); margin-bottom:1.5rem; font-size:0.9rem;">
                    Selecione um arquivo XML de NF-e. O sistema processará os dados e simulará o recebimento do documento.
                </p>
                <div class="form-group">
                    <input type="file" name="xml_file" required accept=".xml,.txt" class="form-input" style="padding:0.75rem;">
                </div>
            </div>
            <div class="modal-footer" style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeXmlModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Processar XML</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openXmlModal() {
        document.getElementById('xmlModal').style.display = 'flex';
    }
    function closeXmlModal() {
        document.getElementById('xmlModal').style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target == document.getElementById('xmlModal')) {
            closeXmlModal();
        }
    }
</script>
@endsection
