@extends('layouts.app')

@section('title', 'Novo Cargo')
@section('page-title', 'Novo Cargo')
@section('page-subtitle', 'Cadastre uma nova função no sistema e configure suas permissões de acesso')

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

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

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
                            <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ex: Analista de Logística Sênior" style="height: 48px; font-weight: 500;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descrição Breve</label>
                            <input type="text" name="description" value="{{ old('description') }}" class="form-input" placeholder="Ex: Responsável por gerenciar inventário e expedição" style="height: 48px;">
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
                    <div style="display:flex; gap:0.5rem;">
                        <button type="button" class="btn btn-secondary" onclick="toggleAllPermissions(true)" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius:8px;">
                            Marcar Todas
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleAllPermissions(false)" style="padding: 0.5rem 1rem; font-size: 0.75rem; border-radius:8px;">
                            Desmarcar Todas
                        </button>
                    </div>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $group => $groupPermissions)
                            <div style="background: var(--bg-base); border: 1px solid var(--border); border-radius: var(--r-md); padding: 1.5rem; display:flex; flex-direction:column; gap:1rem;">
                                <div style="font-size:0.75rem; font-weight:800; color:var(--accent); text-transform:uppercase; letter-spacing:0.075em; border-bottom:1px solid var(--border); padding-bottom:0.5rem; display:flex; align-items:center; gap:0.5rem;">
                                    <i class="fa-solid fa-folder-open" style="opacity:0.7;"></i> {{ $group }}
                                </div>
                                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                                    @foreach($groupPermissions as $p)
                                        <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-size:0.875rem; color:var(--text-secondary); transition: color 0.2s;" class="permission-label">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="perm-checkbox" style="width:18px; height:18px; accent-color:var(--accent);">
                                            {{ $p->label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; justify-content:flex-end; gap:1rem; padding: 1.5rem; background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-sm);">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary px-8">Cancelar</a>
                <button type="submit" class="btn btn-primary px-12">
                    <i class="fa-solid fa-save mr-2"></i> Criar Cargo
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
@endsection
