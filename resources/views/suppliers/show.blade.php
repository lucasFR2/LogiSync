<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $supplier->name }} - LogiSync WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
            <div class="p-6 border-b border-slate-800 flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket mr-3"></i> Sair
                    </button>
                </form>
=======
@extends('layouts.app')

@section('title', $supplier->name)
@section('page-title', $supplier->name)
@section('page-subtitle', 'Detalhes do fornecedor')

@section('content')
<div style="max-width:720px;">
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
>>>>>>> Stashed changes
            </div>

<<<<<<< Updated upstream
        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-slate-900">
                    <i class="fa-solid fa-handshake text-blue-600 mr-2"></i>{{ $supplier->name }}
                </h1>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <section class="p-8">
                <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
                    <!-- Informações Básicas -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Informações Básicas</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Nome</p>
                                <p class="text-gray-900">{{ $supplier->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">CNPJ</p>
                                <p class="text-gray-900">{{ $supplier->cnpj ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contato</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">E-mail</p>
                                <p class="text-gray-900">{{ $supplier->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Telefone</p>
                                <p class="text-gray-900">{{ $supplier->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Endereço</h3>
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-1">Logradouro</p>
                            <p class="text-gray-900 mb-4">{{ $supplier->address ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Cidade</p>
                                <p class="text-gray-900">{{ $supplier->city ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Estado</p>
                                <p class="text-gray-900">{{ $supplier->state ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-4 pt-6 border-t">
                        <a href="{{ route('suppliers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold text-center">
                            Voltar
                        </a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-center">
                            Editar
                        </a>
                    </div>
=======
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
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


