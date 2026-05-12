@extends('layouts.app')

@section('title', $customer->name)
@section('page-title', 'Detalhes do Cliente')
@section('page-subtitle', $customer->name)

@section('content')
<div style="max-width:800px;">
    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Informações Cadastrais</h3>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pencil"></i> Editar
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:2.5rem; padding:2.5rem;">

            {{-- Identity Info --}}
            <div class="grid grid-2" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-user-shield" style="color:var(--accent);"></i> Identificação
                    </h4>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Nome / Razão Social</div>
                    <div style="font-size:1.1rem; font-weight:600; color:var(--text-primary);">{{ $customer->name }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CPF / CNPJ</div>
                    <div style="font-size:1.1rem; font-family:monospace; color:var(--text-primary);">{{ $customer->document }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Tipo de Pessoa</div>
                    <div>
                        @if($customer->type == 'individual')
                            <span class="badge" style="background:var(--blue-bg); color:var(--blue);">Pessoa Física</span>
                        @else
                            <span class="badge" style="background:var(--purple-bg); color:var(--purple);">Pessoa Jurídica</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Inscrição Estadual</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->state_registration ?? '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Contact Info --}}
            <div class="grid grid-2" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-address-book" style="color:var(--accent);"></i> Contato
                    </h4>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">E-mail</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->email ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Telefone / Celular</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->phone ?? '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Address Info --}}
            <div class="grid grid-3" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Localização
                    </h4>
                </div>
                <div style="grid-column: 1 / span 2;">
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Endereço</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->address ?? '—' }}{{ $customer->number ? ', ' . $customer->number : '' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Bairro</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->neighborhood ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CEP</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->zip_code ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Cidade</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->city ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Estado</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $customer->state ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
