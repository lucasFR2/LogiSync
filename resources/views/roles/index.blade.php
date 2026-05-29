@extends('layouts.app')

@section('title', 'Cargos e Funções')
@section('page-title', 'Cargos e Funções')
@section('page-subtitle', 'Gerencie as funções e permissões de acesso do sistema')

@section('content')
<div class="anim-entrance">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:1.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-2" style="grid-template-columns: 1fr 450px; gap: 2rem; align-items: start;">
        {{-- List --}}
        <div class="card">
            <div class="card-header" style="padding: 1.5rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:12px; height:24px; background:var(--accent); border-radius:4px; box-shadow: 0 0 15px var(--accent-alpha);"></div>
                    <h3 style="margin:0; font-size: 1.25rem;">Funções Ativas</h3>
                </div>
            </div>
            <div class="table-wrap">
                <table style="width:100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem 1.5rem;">Nome do Cargo</th>
                            <th style="padding: 1rem 1.5rem;">Permissões</th>
                            <th style="padding: 1rem 1.5rem; text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="font-weight:700; color:var(--text-primary); font-size:1rem;">{{ $role->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">{{ $role->description ?? 'Sem descrição' }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                                        @if($role->name === 'Administrador')
                                            <span class="badge badge-primary" style="font-size:0.7rem; background: var(--accent); color: white;">Acesso Total</span>
                                        @else
                                            @forelse($role->permissions->take(3) as $p)
                                                <span class="badge" style="font-size:0.65rem; background: var(--bg-base); border: 1px solid var(--border); color: var(--text-muted);">{{ $p->label }}</span>
                                            @empty
                                                <span style="font-size:0.75rem; color:var(--text-muted); font-style:italic;">Nenhuma</span>
                                            @endforelse
                                            @if($role->permissions->count() > 3)
                                                <span style="font-size:0.75rem; color:var(--accent); font-weight:600;">+{{ $role->permissions->count() - 3 }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align:right;">
                                    <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                        <button class="icon-btn edit-role-btn" style="background:var(--bg-base); border:1px solid var(--border);" title="Editar" 
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}"
                                                data-role-description="{{ $role->description ?? '' }}"
                                                data-role-permissions="{{ json_encode($role->permissions->pluck('id')->toArray()) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($role->name !== 'Administrador')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este cargo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn" title="Remover" style="background:var(--bg-base); border:1px solid var(--red-alpha); color:var(--red);">
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

        {{-- Form (Create/Edit) --}}
        <div class="card" style="position: sticky; top: 2rem;">
            <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                <h3 id="form-title" style="margin:0; font-size:1.15rem; color:var(--text-primary);">Novo Cargo</h3>
                <p id="form-subtitle" style="font-size:0.8rem; color:var(--text-muted); margin:0.25rem 0 0 0;">Preencha os dados e selecione as permissões.</p>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <form id="role-form" action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div id="method-field"></div>
                    
                    <div class="form-group">
                        <label class="form-label">Nome da Função <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" id="role-name" required class="form-input" placeholder="Ex: Analista de Logística">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição Breve</label>
                        <input type="text" name="description" id="role-description" class="form-input" placeholder="O que este cargo faz?">
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <label class="form-label" style="display:block; margin-bottom: 1rem; font-weight:700;">Definir Permissões</label>
                        
                        <div style="display:flex; flex-direction:column; gap:1.25rem; max-height: 400px; overflow-y: auto; padding-right: 0.5rem;" class="custom-scrollbar">
                            @foreach($permissions as $group => $groupPermissions)
                                <div style="background: var(--bg-base); border: 1px solid var(--border); border-radius: 8px; padding: 1rem;">
                                    <div style="font-size:0.7rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
                                        <i class="fa-solid fa-shield-halved"></i> {{ $group }}
                                    </div>
                                    <div style="display:grid; grid-template-columns: 1fr; gap:0.6rem;">
                                        @foreach($groupPermissions as $p)
                                            <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-size:0.85rem; color:var(--text-secondary); transition: all 0.2s;" class="permission-item">
                                                <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="perm-checkbox" style="width:16px; height:16px; accent-color:var(--accent);">
                                                {{ $p->label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="display:flex; gap:0.75rem; margin-top:2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center; box-shadow: 0 4px 12px var(--accent-alpha);">
                            <i class="fa-solid fa-save"></i> Salvar Cargo
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()" style="width: 50px; justify-content: center; padding: 0;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .permission-item:hover { color: var(--accent); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    
    .badge {
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }
</style>

<script>
    // Event listener for edit buttons
    document.querySelectorAll('.edit-role-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-role-id');
            const name = this.getAttribute('data-role-name');
            const description = this.getAttribute('data-role-description');
            const permissionsStr = this.getAttribute('data-role-permissions');
            const permissions = JSON.parse(permissionsStr);
            
            editRole(id, name, description, permissions);
        });
    });

    function editRole(id, name, description, perms) {
        document.getElementById('form-title').innerText = 'Editar Cargo';
        document.getElementById('form-subtitle').innerText = 'Alterando o cargo: ' + name;
        document.getElementById('role-form').action = `/roles/${id}`;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('role-name').value = name;
        document.getElementById('role-description').value = description;
        
        // Reset and Set checkboxes
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = perms.includes(parseInt(cb.value));
        });

        document.getElementById('role-name').focus();
        document.querySelector('.card:last-child').scrollIntoView({ behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('form-title').innerText = 'Novo Cargo';
        document.getElementById('form-subtitle').innerText = 'Preencha os dados e selecione as permissões.';
        document.getElementById('role-form').action = "{{ route('roles.store') }}";
        document.getElementById('method-field').innerHTML = '';
        document.getElementById('role-form').reset();
        
        const checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
    }
</script>
@endsection
