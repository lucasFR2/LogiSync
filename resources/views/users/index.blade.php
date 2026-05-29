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

    {{-- Barra de Controle Superior --}}
    <div class="card" style="margin-bottom:2rem; padding:1.25rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1.25rem; align-items:center; justify-content:space-between;">
            
            {{-- Formulário de Filtro --}}
            <form action="{{ route('employees.index') }}" method="GET" style="display:flex; flex:1; gap:0.75rem; min-width:320px;">
                <div style="flex:1; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1.25rem; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Pesquisar por nome, e-mail ou CPF..." style="padding-left:3rem; border-radius:12px;">
                </div>
                
                <select name="role_filter" class="form-select" style="width:200px; border-radius:12px;" onchange="this.form.submit()">
                    <option value="">Todos os Cargos</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_filter') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary" style="padding:0 1.25rem;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                
                @if(request()->hasAny(['search', 'role_filter']))
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary" title="Limpar Filtros" style="padding:0 1rem; display:flex; align-items:center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            {{-- Ações --}}
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('register') }}" class="btn btn-primary" style="box-shadow: 0 10px 20px -5px var(--accent-glow);">
                    <i class="fa-solid fa-user-plus"></i> Novo Funcionário
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Funcionários Cadastrados</h3>
            </div>
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
                    @forelse($employees as $emp)
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
                            <td><span class="badge" style="background:var(--bg-hover); color:var(--text-primary); font-weight:700; font-size:0.7rem; text-transform:uppercase;">{{ $emp->role->name ?? 'Sem Cargo' }}</span></td>
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
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:3rem; color:var(--text-muted);">
                                <i class="fa-solid fa-user-slash" style="font-size:2rem; display:block; margin-bottom:1rem; opacity:0.3;"></i>
                                Nenhum funcionário encontrado para esta busca.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div style="padding:1.25rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                {{ $employees->links() }}
            </div>
        @endif
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
    .doc-item:hover { background: var(--bg-hover); }
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
