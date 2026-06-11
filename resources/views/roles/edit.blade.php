@extends('layouts.app')

@section('title', 'Editar Cargo')
<<<<<<< HEAD
@section('page-title', 'Editar Cargo: ' . $role->name)
@section('page-subtitle', 'Modifique os dados e as permissões de acesso associadas a este cargo')

@section('content')
<div class="anim-entrance">
    <div style="display:flex; justify-content:flex-start; margin-bottom:1.5rem;">
        <a href="{{ route('roles.index') }}" class="btn btn-secondary" style="padding:0.625rem 1.25rem; font-size:0.875rem; border-radius:12px; box-shadow: var(--shadow-sm);">
            <i class="fa-solid fa-arrow-left mr-2"></i> Voltar para Listagem
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.5rem; font-size:0.875rem; flex-direction:column; align-items:flex-start;">
            <div style="font-weight:700;"><i class="fa-solid fa-circle-exclamation"></i> Ocorreram erros de validação:</div>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display:flex; flex-direction:column; gap:2rem;">
            
            {{-- Identificação Card --}}
            <div class="card">
                <div class="card-header" style="padding: 1.5rem;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:36px; height:36px; background:var(--accent-subtle); color:var(--accent); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <h3 style="margin:0; font-size: 1.15rem;">Identificação do Cargo</h3>
                    </div>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Nome da Função <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="form-input" placeholder="Ex: Analista de Logística Sênior" style="height: 48px; font-weight: 500;" @if($role->name === 'Administrador') readonly @endif>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descrição Breve</label>
                            <input type="text" name="description" value="{{ old('description', $role->description) }}" class="form-input" placeholder="Ex: Responsável por gerenciar inventário e expedição" style="height: 48px;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissões Card --}}
            <div class="card">
                <div class="card-header" style="padding: 1.5rem; display:flex; justify-content:between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:36px; height:36px; background:var(--blue-bg); color:var(--blue); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h3 style="margin:0; font-size: 1.15rem;">Definição de Permissões</h3>
                    </div>
                    @if($role->name !== 'Administrador')
                        <div style="display:flex; gap:0.5rem;">
                            <button type="button" class="btn btn-secondary" onclick="toggleAllPermissions(true)" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius:8px;">
                                Marcar Todas
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleAllPermissions(false)" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius:8px;">
                                Desmarcar Todas
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body" style="padding: 2rem;">
                    @if($role->name === 'Administrador')
                        <div class="alert" style="font-size:0.9rem; font-weight:700; background: var(--blue-bg); border-color: var(--blue); color: var(--blue);">
                            <i class="fa-solid fa-info-circle"></i> O cargo de Administrador possui acesso total a todos os recursos do sistema por padrão.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="@if($role->name === 'Administrador') opacity: 0.6; pointer-events: none; @endif">
                        @foreach($permissions as $group => $groupPermissions)
                            <div style="background: var(--bg-base); border: 1px solid var(--border); border-radius: var(--r-md); padding: 1.5rem; display:flex; flex-direction:column; gap:1rem;">
                                <div style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.075em; border-bottom:1px solid var(--border); padding-bottom:0.5rem; display:flex; align-items:center; gap:0.5rem;">
=======
@section('page-title', 'Editar Cargo')
@section('page-subtitle', 'Edite as definições e permissões de acesso do cargo')

@section('content')
<div class="w-full anim-entrance">

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header" style="padding: 1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:12px; height:24px; background:var(--accent); border-radius:4px; box-shadow: 0 0 15px var(--accent-alpha);"></div>
                <h3 style="margin:0; font-size: 1.25rem;">Editar Cargo: {{ $role->name }}</h3>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body" style="padding: 2rem;">
            <form action="{{ route('roles.update', $role->id) }}" method="POST" style="display:flex; flex-direction:column; gap:2rem;">
                @csrf
                @method('PUT')

                {{-- Basic Information --}}
                <div class="grid grid-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Nome da Função / Cargo <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required class="form-input" placeholder="Ex: Analista de Logística">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição Breve</label>
                        <input type="text" name="description" value="{{ old('description', $role->description) }}" class="form-input" placeholder="O que este cargo faz no sistema?">
                    </div>
                </div>

                {{-- Permissions Section --}}
                <div style="border-top: 1px solid var(--border); padding-top: 2rem;">
                    <h4 style="font-family:'Outfit'; font-size:1.15rem; color:var(--text-primary); margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fa-solid fa-shield-halved" style="color:var(--accent);"></i> Definir Permissões de Acesso
                    </h4>

                    <div class="grid grid-3 gap-6" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                        @foreach($permissions as $group => $groupPermissions)
                            <div style="background: var(--bg-hover); border: 1px solid var(--border); border-radius: var(--r-md); padding: 1.25rem; display:flex; flex-direction:column; gap:1rem;">
                                <div style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.06em; display:flex; align-items:center; gap:0.5rem; border-bottom:1px solid var(--border); padding-bottom:0.75rem; margin-bottom:0.25rem;">
>>>>>>> origin/LUCAS
                                    <i class="fa-solid fa-folder-open" style="opacity:0.7;"></i> {{ $group }}
                                </div>
                                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                                    @foreach($groupPermissions as $p)
<<<<<<< HEAD
                                        <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-size:0.875rem; color:var(--text-secondary); transition: color 0.2s;" class="permission-label">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="perm-checkbox" style="width:18px; height:18px; accent-color:var(--accent);"
                                                @if($role->name === 'Administrador' || in_array($p->id, $rolePermissions)) checked @endif
                                                @if($role->name === 'Administrador') disabled @endif>
                                            {{ $p->label }}
=======
                                        <label style="display:flex; align-items:flex-start; gap:0.75rem; cursor:pointer; font-size:0.875rem; color:var(--text-secondary); transition: all 0.2s;" class="permission-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" 
                                                {{ in_array($p->id, old('permissions', $rolePermissions)) ? 'checked' : '' }} 
                                                class="perm-checkbox" style="width:17px; height:17px; accent-color:var(--accent); margin-top:2px;">
                                            <span>
                                                <span style="font-weight:600; display:block; color:var(--text-primary);">{{ $p->label }}</span>
                                                <small style="color:var(--text-muted); font-size:0.75rem; display:block; margin-top:0.1rem;">{{ $p->description ?? 'Concede acesso a este recurso' }}</small>
                                            </span>
>>>>>>> origin/LUCAS
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
<<<<<<< HEAD
            </div>

            {{-- Actions --}}
            <div style="display:flex; justify-content:flex-end; gap:1rem; padding: 1.5rem; background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-sm);">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary px-8">Cancelar</a>
                <button type="submit" class="btn btn-primary px-12">
                    <i class="fa-solid fa-save mr-2"></i> Salvar Alterações
                </button>
            </div>

        </div>
    </form>
</div>

<style>
    .permission-label:hover {
        color: var(--text-primary) !important;
    }
</style>

<script>
    function toggleAllPermissions(value) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = value;
        });
    }
</script>
=======

                {{-- Action Buttons --}}
                <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:3rem; padding-right:3rem; font-weight:700; box-shadow: 0 4px 12px var(--accent-alpha);">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .permission-item:hover { color: var(--accent); }
    .permission-item:hover span span { color: var(--accent); }
</style>
>>>>>>> origin/LUCAS
@endsection
