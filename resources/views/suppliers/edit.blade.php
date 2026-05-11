<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fornecedor - LogiSync WMS</title>
    <script>
        tailwindConfig = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-50 dark:bg-slate-950 font-sans transition-colors">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 dark:bg-slate-950 text-white hidden md:flex flex-col">
            <div class="p-6 border-b border-slate-800 dark:border-slate-700 flex justify-center">
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

@section('title', 'Editar Fornecedor')
@section('page-title', 'Editar Fornecedor')
@section('page-subtitle', $supplier->name)

@section('content')
<div style="max-width:720px;">

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
>>>>>>> Stashed changes
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" style="display:flex; flex-direction:column; gap:2rem;">
                @csrf @method('PUT')

<<<<<<< Updated upstream
        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <!-- Header -->
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-pencil text-rose-600 mr-2"></i>Editar Fornecedor
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
=======
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
>>>>>>> Stashed changes
                    </div>
                </div>

<<<<<<< Updated upstream
            <!-- Conteúdo -->
            <section class="p-8">
                <div class="max-w-2xl mx-auto">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-600 font-semibold mb-2">Erros ao validar:</p>
                            <ul class="text-red-600 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
=======
                {{-- Address Info --}}
                <div class="grid grid-2">
                    <div style="grid-column: 1/-1;">
                        <h4 style="font-family:'Outfit'; font-size:1.1rem; margin-bottom:1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem;">
                            <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Endereço
                        </h4>
                    </div>
>>>>>>> Stashed changes

                    <div class="form-group">
                        <label class="form-label">CEP</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" placeholder="00000-000" class="form-input">
                    </div>

<<<<<<< Updated upstream
                            <!-- Informações Básicas -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Informações Básicas</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome <span class="text-red-600">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">CNPJ</label>
                                    <input type="text" name="cnpj" value="{{ old('cnpj', $supplier->cnpj) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Contato -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contato</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                                        <input type="tel" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Endereço</h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rua/Avenida</label>
                                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cidade</label>
                                        <input type="text" name="city" value="{{ old('city', $supplier->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                                        <input type="text" name="state" value="{{ old('state', $supplier->state) }}" maxlength="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex gap-4 pt-6 border-t">
                                <a href="{{ route('suppliers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold text-center">
                                    Cancelar
                                </a>
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                                    Atualizar
                                </button>
                            </div>
                        </form>
=======
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label class="form-label">Logradouro (Rua, Avenida)</label>
                        <input type="text" name="street" value="{{ old('street', $supplier->street) }}" placeholder="Ex: Rua das Flores" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Número</label>
                        <input type="text" name="number" value="{{ old('number', $supplier->number) }}" placeholder="Ex: 123" class="form-input">
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
>>>>>>> Stashed changes
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


