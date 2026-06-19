@extends('layouts.app')

@section('title', 'Editar Transportadora')
@section('page-title', 'Editar Transportadora')
@section('page-subtitle', $carrier->name)

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
                <h3 style="margin:0;">Ficha da Transportadora</h3>
            </div>
            <a href="{{ route('carriers.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('carriers.update', $carrier) }}" method="POST" style="display:flex; flex-direction:column; gap:2rem;">
                @csrf @method('PUT')

                {{-- Identificação --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-info-circle" style="color:var(--accent);"></i> Identificação
                        </h4>
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $carrier->name) }}" required class="form-input" placeholder="Ex: Transportes Rápidos Ltda">
                    </div>

                    <div class="form-group">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" value="{{ old('cnpj', $carrier->cnpj) }}" placeholder="00.000.000/0001-00" class="form-input" style="font-family:monospace;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Inscrição Estadual (IE)</label>
                        <input type="text" name="state_registration" value="{{ old('state_registration', $carrier->state_registration) }}" placeholder="Ex: 123.456.789.110" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pessoa de Contato</label>
                        <input type="text" name="contact" value="{{ old('contact', $carrier->contact) }}" placeholder="Ex: João Silva" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $carrier->phone) }}" placeholder="(11) 99999-9999" class="form-input">
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">E-mail Corporativo</label>
                        <input type="email" name="email" value="{{ old('email', $carrier->email) }}" placeholder="contato@transportadora.com" class="form-input">
                    </div>
                </div>

                {{-- Dados de Transporte --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-truck" style="color:var(--accent);"></i> Dados de Transporte
                        </h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Registro ANTT / RNTRC</label>
                        <input type="text" name="antt" value="{{ old('antt', $carrier->antt) }}" placeholder="Ex: 12345678" class="form-input" style="font-family:monospace;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Veículo Padrão</label>
                        <select name="vehicle_type" class="form-select">
                            <option value="">— Selecione —</option>
                            @foreach(['Caminhão','Carreta','Van','Moto','Próprio'] as $vt)
                                <option value="{{ $vt }}" {{ old('vehicle_type', $carrier->vehicle_type) === $vt ? 'selected' : '' }}>{{ $vt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Placa Padrão</label>
                        <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate', $carrier->vehicle_plate) }}" placeholder="ABC-1234" class="form-input" style="font-family:monospace; text-transform:uppercase;" maxlength="8">
                    </div>

                    <div class="form-group">
                        <label class="form-label">UF da Placa</label>
                        <select name="vehicle_uf" class="form-select">
                            <option value="">— UF —</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('vehicle_uf', $carrier->vehicle_uf) === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Endereço --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Endereço
                        </h4>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <div style="display:flex; gap:0.5rem;">
                            <input type="text" name="zip_code" id="carrier-zip" value="{{ old('zip_code', $carrier->zip_code) }}" placeholder="00000-000" class="form-input" style="font-family:monospace; flex:1;" onblur="carrierFetchCep(this.value)">
                            <button type="button" onclick="carrierFetchCep(document.getElementById('carrier-zip').value)" class="btn btn-secondary">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Logradouro</label>
                        <input type="text" name="street" id="carrier-street" value="{{ old('street', $carrier->street) }}" placeholder="Ex: Rua das Flores" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" value="{{ old('number', $carrier->number) }}" placeholder="Ex: 123" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Complemento</label>
                        <input type="text" name="complement" value="{{ old('complement', $carrier->complement) }}" placeholder="Apto, Bloco, etc." class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="neighborhood" id="carrier-neighborhood" value="{{ old('neighborhood', $carrier->neighborhood) }}" placeholder="Ex: Centro" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="city" id="carrier-city" value="{{ old('city', $carrier->city) }}" placeholder="Ex: São Paulo" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado (UF)</label>
                        <select name="state" id="carrier-state" class="form-select">
                            <option value="">— UF —</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('state', $carrier->state) === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('carriers.index') }}" class="btn btn-secondary">Descartar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function carrierFetchCep(cep) {
    const raw = (cep || '').replace(/\D/g, '');
    if (raw.length !== 8) return;
    try {
        const res  = await fetch(`https://viacep.com.br/ws/${raw}/json/`);
        const data = await res.json();
        if (!data.erro) {
            document.getElementById('carrier-street').value       = data.logradouro || '';
            document.getElementById('carrier-neighborhood').value = data.bairro || '';
            document.getElementById('carrier-city').value         = data.localidade || '';
            const st = document.getElementById('carrier-state');
            if (st) st.value = data.uf || '';
        }
    } catch (_) {}
}
</script>
@endsection
