@extends('layouts.app')

@section('title', 'Editar Fornecedor')
@section('page-title', 'Editar Fornecedor')
@section('page-subtitle', $supplier->name)

@section('content')
<div class="w-full">

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
                <h3 style="margin:0;">Ficha do Fornecedor</h3>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" style="display:flex; flex-direction:column; gap:2rem;">
                @csrf @method('PUT')

                {{-- Basic Info --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-info-circle" style="color:var(--accent);"></i> Informações Básicas
                        </h4>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="form-input" placeholder="Ex: Logística S.A.">
                    </div>

                    <div class="form-group">
                        <label class="form-label">CNPJ / Documento</label>
                        <input type="text" name="cnpj" value="{{ old('cnpj', $supplier->cnpj) }}" placeholder="00.000.000/0000-00" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Inscrição Estadual (IE)</label>
                        <input type="text" name="state_registration" value="{{ old('state_registration', $supplier->state_registration) }}" placeholder="Ex: 123.456.789.110" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone de Contato</label>
                        <input type="tel" name="phone" value="{{ old('phone', $supplier->phone) }}" placeholder="(11) 99999-9999" class="form-input">
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">E-mail Corporativo</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" placeholder="contato@empresa.com" class="form-input">
                    </div>
                </div>

                {{-- Address Info --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Endereço
                        </h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" placeholder="00000-000" class="form-input">
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Logradouro (Rua, Avenida)</label>
                        <input type="text" name="street" value="{{ old('street', $supplier->street) }}" placeholder="Ex: Rua das Flores" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" value="{{ old('number', $supplier->number) }}" placeholder="Ex: 123" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Complemento</label>
                        <input type="text" name="complement" value="{{ old('complement', $supplier->complement) }}" placeholder="Apto, Bloco, etc." class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="neighborhood" value="{{ old('neighborhood', $supplier->neighborhood) }}" placeholder="Ex: Centro" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="city" value="{{ old('city', $supplier->city) }}" placeholder="Ex: São Paulo" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado (UF)</label>
                        <input type="text" name="state" value="{{ old('state', $supplier->state) }}" placeholder="SP" maxlength="2" class="form-input">
                    </div>
                </div>

                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Descartar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
