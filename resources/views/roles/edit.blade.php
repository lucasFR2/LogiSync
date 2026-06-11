@extends('layouts.app')

@section('title', 'Editar Cargo')
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
                                    <i class="fa-solid fa-folder-open" style="opacity:0.7;"></i> {{ $group }}
                                </div>
                                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                                    @foreach($groupPermissions as $p)
                                        <label style="display:flex; align-items:flex-start; gap:0.75rem; cursor:pointer; font-size:0.875rem; color:var(--text-secondary); transition: all 0.2s;" class="permission-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" 
                                                {{ in_array($p->id, old('permissions', $rolePermissions)) ? 'checked' : '' }} 
                                                class="perm-checkbox" style="width:17px; height:17px; accent-color:var(--accent); margin-top:2px;">
                                            <span>
                                                <span style="font-weight:600; display:block; color:var(--text-primary);">{{ $p->label }}</span>
                                                <small style="color:var(--text-muted); font-size:0.75rem; display:block; margin-top:0.1rem;">{{ $p->description ?? 'Concede acesso a este recurso' }}</small>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

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
@endsection
