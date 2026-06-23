@extends('layouts.app')

@section('title', $supplier->name)
@section('page-title', $supplier->name)
@section('page-subtitle', 'Detalhes do fornecedor')

@section('content')
<div class="w-full">
    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Detalhes do Fornecedor</h3>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pencil"></i> Editar
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:2.5rem; padding:2.5rem;">

            {{-- Basic Info --}}
            <div class="grid grid-2" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-info-circle" style="color:var(--accent);"></i> Informações Básicas
                    </h4>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Nome / Razão Social</div>
                    <div style="font-size:1.1rem; font-weight:600; color:var(--text-primary);">{{ $supplier->name }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CNPJ / Documento</div>
                    <div style="font-size:1.1rem; font-family:monospace; color:var(--text-primary);">{{ $supplier->cnpj ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Inscrição Estadual</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->state_registration ?? '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Contact --}}
            <div class="grid grid-2" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-address-book" style="color:var(--accent);"></i> Contato e Comunicação
                    </h4>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">E-mail Corporativo</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->email ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Telefone / WhatsApp</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->phone ?? '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Address --}}
            <div class="grid grid-3" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Localização
                    </h4>
                </div>
                <div style="grid-column: 1 / span 2;">
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Logradouro</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->street ?? '—' }}{{ $supplier->number ? ', ' . $supplier->number : '' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Complemento</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-style:{{ $supplier->complement ? 'normal' : 'italic' }};">{{ $supplier->complement ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Bairro</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->neighborhood ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Cidade / Estado</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">
                        {{ $supplier->city ?? '—' }}@if($supplier->state), {{ $supplier->state }}@endif
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CEP</div>
                    <div style="font-size:1.1rem; color:var(--text-primary);">{{ $supplier->zip_code ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
