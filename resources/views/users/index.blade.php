@extends('layouts.app')

@section('title', 'Gestão de Funcionários')
@section('page-title', 'Gestão de Funcionários')
@section('page-subtitle', 'Administre os dados e acessos dos colaboradores')

@section('content')
<div class="anim-entrance">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:1.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Funcionários Cadastrados</h3>
            </div>
            <a href="{{ route('register') }}" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Novo Cadastro
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>E-mail</th>
                        <th>CPF</th>
                        <th style="text-align:right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div style="width:36px; height:36px; background:var(--bg-hover); border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; color:var(--accent);">
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:var(--text-primary);">{{ $emp->name }}</div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">ID: #{{ str_pad($emp->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge" style="background:var(--bg-hover); color:var(--text-primary); font-weight:700; font-size:0.7rem; text-transform:uppercase;">{{ $emp->role }}</span></td>
                            <td>{{ $emp->email }}</td>
                            <td>{{ $emp->cpf }}</td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                    <a href="{{ route('employees.edit', $emp->id) }}" class="icon-btn" title="Editar Funcionário">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    @if($emp->document_path)
                                        @php $docs = json_decode($emp->document_path, true) ?? []; @endphp
                                        <button class="icon-btn" title="Ver Documentos" style="color:var(--blue); background: var(--bg-base); border: 1px solid var(--border);" 
                                                onclick="showDocModal({{ $emp->id }}, @json($emp->name), @json($docs))">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>
                                    @endif

                                    @if($emp->id !== auth()->id())
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este funcionário?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn" title="Remover" style="color:var(--red);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal de Documentos --}}
<div id="docModal" class="hidden" style="position:fixed; inset:0; background:rgba(2,6,23,0.6); z-index:9999; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="card anim-entrance" style="width:100%; max-width:450px; margin:1rem; box-shadow:var(--shadow-2xl);">
        <div class="card-header" style="padding:1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:8px; height:20px; background:var(--blue); border-radius:2px;"></div>
                <h3 style="margin:0; font-size:1.1rem;">Anexos do Funcionário</h3>
            </div>
            <button onclick="closeDocModal()" style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:1.25rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="card-body" style="padding:1.5rem;">
            <div id="modal-emp-name" style="font-weight:700; color:var(--text-primary); margin-bottom:1.25rem; font-size:1rem;"></div>
            <div id="modal-doc-list" style="display:flex; flex-direction:column; gap:0.75rem; max-height:350px; overflow-y:auto; padding-right:0.5rem;" class="custom-scrollbar">
                {{-- Dinâmico via JS --}}
            </div>
        </div>
    </div>
</div>

<style>
    .doc-item:hover { background: var(--bg-hover); transform: translateX(5px); }
    .hidden { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
</style>

<script>
    function showDocModal(id, name, docs) {
        document.getElementById('modal-emp-name').innerText = name;
        const list = document.getElementById('modal-doc-list');
        list.innerHTML = '';

        if (docs.length === 0) {
            list.innerHTML = '<div style="text-align:center; color:var(--text-muted); padding:2rem;">Nenhum documento anexado.</div>';
        } else {
            docs.forEach((path, index) => {
                const fileName = path.split('/').pop();
                const ext = fileName.split('.').pop().toLowerCase();
                let icon = 'fa-file';
                let color = 'var(--text-muted)';

                if (ext === 'pdf') { icon = 'fa-file-pdf'; color = 'var(--red)'; }
                else if (['jpg', 'jpeg', 'png'].includes(ext)) { icon = 'fa-file-image'; color = 'var(--blue)'; }

                const item = document.createElement('a');
                item.href = `/storage/${path}`;
                item.target = '_blank';
                item.className = 'doc-item';
                item.style = 'display:flex; align-items:center; gap:1rem; padding:1rem; text-decoration:none; color:var(--text-primary); background:var(--bg-base); border:1px solid var(--border); border-radius:var(--r-md); transition:all 0.2s;';
                item.innerHTML = `
                    <i class="fa-solid ${icon}" style="color:${color}; font-size:1.25rem;"></i>
                    <div style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.9rem;">${fileName}</div>
                    <i class="fa-solid fa-up-right-from-square" style="font-size:0.75rem; opacity:0.4;"></i>
                `;
                list.appendChild(item);
            });
        }

        document.getElementById('docModal').classList.remove('hidden');
    }

    function closeDocModal() {
        document.getElementById('docModal').classList.add('hidden');
    }

    // Fechar ao clicar fora do card
    document.getElementById('docModal').addEventListener('click', function(e) {
        if (e.target === this) closeDocModal();
    });
</script>
@endsection
