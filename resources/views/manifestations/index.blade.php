@extends('layouts.app')

@section('title', 'Manifestação do Destinatário')
@section('page-title', 'Manifestação do Destinatário')
@section('page-subtitle', 'Gerencie notas fiscais de entrada (NF-e) emitidas contra o seu CNPJ.')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:1.5rem;">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Ações --}}
    <div class="card" style="padding:1.25rem 1.5rem;">
        <div style="display:flex; flex-wrap:wrap; gap:0.75rem; justify-content:flex-end; align-items:center;">
            <a href="{{ route('manifestations.generateXml') }}" target="_blank" class="btn btn-secondary">
                <i class="fa-solid fa-file-code"></i> Gerar XML de Teste
            </a>
            <button type="button" class="btn btn-primary" onclick="openXmlModal()">
                <i class="fa-solid fa-file-import"></i> Importar XML (Simulação)
            </button>
        </div>
    </div>

    {{-- Filtros + tabela --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0; font-family:'Outfit',sans-serif;">Notas Fiscais de Entrada</h3>
            </div>
            <span style="font-size:0.875rem; font-weight:600; color:var(--text-muted);">
                {{ $invoices->total() }} {{ $invoices->total() === 1 ? 'registro' : 'registros' }}
            </span>
        </div>

        <div style="padding:1.5rem; border-bottom:1px solid var(--border);">
            <form method="GET" action="{{ route('manifestations.index') }}" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
                <div class="form-group" style="flex:1; min-width:220px; margin-bottom:0;">
                    <label class="form-label">Pesquisar</label>
                    <div style="position:relative;">
                        <i class="fa-solid fa-search" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Nº, Chave, Fornecedor ou CNPJ" class="form-input" style="padding-left:2.75rem;">
                    </div>
                </div>

                <div class="form-group" style="width:180px; margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="ciencia" {{ $status == 'ciencia' ? 'selected' : '' }}>Ciência</option>
                        <option value="confirmada" {{ $status == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="desconhecimento" {{ $status == 'desconhecimento' ? 'selected' : '' }}>Desconhecimento</option>
                        <option value="nao_realizada" {{ $status == 'nao_realizada' ? 'selected' : '' }}>Não Realizada</option>
                    </select>
                </div>

                <div class="form-group" style="width:150px; margin-bottom:0;">
                    <label class="form-label">Data início</label>
                    <input type="date" name="date_start" value="{{ $date_start }}" class="form-input">
                </div>

                <div class="form-group" style="width:150px; margin-bottom:0;">
                    <label class="form-label">Data fim</label>
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
        </div>

        @if($invoices->count() > 0)
            <div class="table-wrap" style="border:none; border-radius:0; box-shadow:none;">
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Número/Série</th>
                                <th>Fornecedor</th>
                                <th>Data Emissão</th>
                                <th>Valor Total</th>
                                <th>Manifestação</th>
                                <th>Status Entrada</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr>
                                    <td>
                                        <div style="font-weight:700;">{{ $inv->number }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">Série {{ $inv->series }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">{{ $inv->supplier_name }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace;">{{ $inv->supplier_cnpj }}</div>
                                    </td>
                                    <td style="white-space:nowrap;">{{ $inv->emission_date->format('d/m/Y') }}</td>
                                    <td style="font-weight:700; white-space:nowrap;">R$ {{ number_format($inv->total_amount, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $inv->status_color }}">{{ $inv->status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $inv->entry_status_color }}">{{ $inv->entry_status_label }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('manifestations.show', $inv) }}" class="icon-btn" title="Detalhes" style="width:32px;height:32px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($invoices->hasPages())
                <div style="padding:1.25rem 1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                    {{ $invoices->links() }}
                </div>
            @endif
        @else
            <div class="empty-state" style="padding:4rem 2rem; text-align:center;">
                <i class="fa-solid fa-file-invoice" style="font-size:3rem; color:var(--text-muted); margin-bottom:1.5rem;"></i>
                <h3 style="margin:0 0 0.5rem;">Nenhuma NF-e encontrada</h3>
                <p style="color:var(--text-muted); margin:0 0 1.5rem;">Faça o upload de um XML para simular a recepção de notas.</p>
                <button type="button" class="btn btn-primary" onclick="openXmlModal()">
                    <i class="fa-solid fa-file-import"></i> Importar XML
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Modal importar XML --}}
<div id="xmlModal" class="modal-backdrop" style="display:none;" role="dialog" aria-modal="true">
    <div class="modal" style="max-width:520px; width:100%;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-file-code" style="color:var(--accent);"></i> Importar XML</h3>
            <button type="button" class="icon-btn" onclick="closeXmlModal()" style="width:32px;height:32px;" aria-label="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('manifestations.uploadXml') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p style="color:var(--text-secondary); margin:0 0 1.25rem; font-size:0.9rem; line-height:1.5;">
                    Selecione um arquivo XML de NF-e. O sistema processará os dados e simulará o recebimento do documento.
                </p>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Arquivo XML</label>
                    <input type="file" name="xml_file" required accept=".xml,.txt" class="form-input" style="padding:0.75rem; cursor:pointer;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeXmlModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-upload"></i> Processar XML
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('xmlModal');
    if (!modal) return;

    window.openXmlModal = function() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        document.body.style.overflow = 'hidden';
    };

    window.closeXmlModal = function() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeXmlModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeXmlModal();
    });
})();
</script>
@endpush
