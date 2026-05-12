@extends('layouts.app')

@section('title', 'Editar Funcionário')
@section('page-title', 'Editar Funcionário')
@section('page-subtitle', 'Atualize as informações cadastrais do colaborador')

@section('content')
<div class="anim-entrance" style="max-width: 900px;">

    @if($errors->any())
        <div class="alert badge-danger" style="margin-bottom:1.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Dados de {{ $user->name }}</h3>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('employees.update', $user->id) }}" method="POST" class="auth-form" style="display:flex; flex-direction:column; gap:2rem;">
                @csrf
                @method('PUT')

                <div class="form-section-title" style="margin-top:0;">
                    <i class="fa-solid fa-id-card"></i> Informações Pessoais
                </div>

                <div class="grid grid-2">
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Nome Completo <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cargo <span style="color:var(--red);">*</span></label>
                        <select name="role" required class="form-select">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $user->role) == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CPF <span style="color:var(--red);">*</span></label>
                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $user->cpf) }}" required class="form-input" placeholder="000.000.000-00" maxlength="14">
                    </div>

                    <div class="form-group">
                        <label class="form-label">RG <span style="color:var(--red);">*</span></label>
                        <input type="text" name="rg" id="rg" value="{{ old('rg', $user->rg) }}" required class="form-input" placeholder="00.000.000-0" maxlength="12">
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-mail Corporativo <span style="color:var(--red);">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone / Celular <span style="color:var(--red);">*</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required class="form-input" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fa-solid fa-map-location-dot"></i> Endereço
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">CEP <span style="color:var(--red);">*</span></label>
                        <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $user->zip_code) }}" required class="form-input" placeholder="00000-000" maxlength="9">
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Logradouro <span style="color:var(--red);">*</span></label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número <span style="color:var(--red);">*</span></label>
                        <input type="text" name="number" value="{{ old('number', $user->number) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bairro <span style="color:var(--red);">*</span></label>
                        <input type="text" name="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cidade <span style="color:var(--red);">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado (UF) <span style="color:var(--red);">*</span></label>
                        <input type="text" name="state" value="{{ old('state', $user->state) }}" required class="form-input" placeholder="SP" maxlength="2">
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fa-solid fa-key"></i> Segurança
                </div>
                <p style="font-size:0.8rem; color:var(--text-muted); margin-top:-1.5rem;">Deixe em branco para manter a senha atual.</p>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Nova Senha</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fa-solid fa-shield-halved"></i> Permissões de Acesso
                </div>
                <p style="font-size:0.8rem; color:var(--text-muted); margin-top:-1.5rem;">Selecione os módulos que este colaborador pode acessar.</p>

                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    @foreach($permissions as $group => $groupPermissions)
                        <div style="background:var(--bg-base); border:1px solid var(--border); border-radius:var(--r-md); padding:1.25rem;">
                            <div style="font-weight:700; color:var(--accent); font-size:0.75rem; text-transform:uppercase; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
                                <i class="fa-solid fa-folder-open"></i> {{ $group }}
                            </div>
                            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                                @foreach($groupPermissions as $p)
                                    <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-size:0.875rem; color:var(--text-primary);">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" {{ in_array($p->id, $userPermissions) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:var(--accent);">
                                        {{ $p->label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:2rem; border-top:1px solid var(--border);">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:3rem; padding-right:3rem;">
                        <i class="fa-solid fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Masks
    document.getElementById('cpf').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4");
        else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3})$/, "$1.$2.$3");
        else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3})$/, "$1.$2");
        e.target.value = v;
    });

    document.getElementById('rg').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 9) v = v.slice(0, 9);
        if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{1})$/, "$1.$2.$3-$4");
        else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})$/, "$1.$2.$3");
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})$/, "$1.$2");
        e.target.value = v;
    });

    document.getElementById('zip_code').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 8) v = v.slice(0, 8);
        if (v.length > 5) v = v.replace(/^(\d{5})(\d{0,3})$/, "$1-$2");
        e.target.value = v;
    });

    document.getElementById('phone').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
        else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, "($1) $2-$3");
        else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
        e.target.value = v;
    });
</script>
@endpush
@endsection
