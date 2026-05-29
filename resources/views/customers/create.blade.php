@extends('layouts.app')

@section('title', 'Novo Cliente')
@section('page-title', 'Novo Cliente')
@section('page-subtitle', 'Preencha os dados cadastrais do cliente')

@section('content')
<div style="max-width:800px;">

    @if($errors->any())
        <div class="alert badge-danger" style="margin-bottom:1.5rem; padding:1.25rem; border-radius:var(--r-md); display:flex; align-items:flex-start; gap:1rem;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-top:3px;"></i>
            <div>
                <div style="font-weight:700; margin-bottom:0.25rem;">Verifique os erros abaixo:</div>
                <ul style="margin:0; padding-left:1.25rem; font-size:0.9rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Cadastro de Cliente</h3>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST" style="display:flex; flex-direction:column; gap:2.5rem;">
                @csrf

                {{-- Basic Info --}}
                <div class="form-grid">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:0.5rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-user-tag" style="color:var(--accent);"></i> Informações de Identificação
                        </h4>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Nome Completo / Razão Social <span class="required-mark">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ex: João da Silva ou Empresa LTDA">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Pessoa <span class="required-mark">*</span></label>
                        <select name="type" required class="form-select">
                            <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Pessoa Física</option>
                            <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Pessoa Jurídica</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CPF / CNPJ <span class="required-mark">*</span></label>
                        <input type="text" name="document" value="{{ old('document') }}" required data-mask="cpf" placeholder="000.000.000-00" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Inscrição Estadual (se PJ)</label>
                        <input type="text" name="state_registration" value="{{ old('state_registration') }}" placeholder="Isento se não possuir" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">E-mail Corporativo</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="cliente@email.com" class="form-input">
                    </div>
                </div>

                {{-- Contact & Address --}}
                <div class="form-grid">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:0.5rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Contato e Endereço
                        </h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone / Celular</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" data-mask="phone" placeholder="(00) 00000-0000" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code') }}" data-mask="cep" placeholder="00000-000" class="form-input">
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Logradouro (Rua, Avenida)</label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Ex: Rua das Flores, 123" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" value="{{ old('number') }}" placeholder="Ex: 123" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="neighborhood" value="{{ old('neighborhood') }}" placeholder="Ex: Centro" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Ex: São Paulo" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado (UF)</label>
                        <input type="text" name="state" value="{{ old('state') }}" placeholder="SP" maxlength="2" class="form-input">
                    </div>
                </div>

                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:2rem; border-top:1px solid var(--border);">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                        <i class="fa-solid fa-save"></i> Salvar Cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
