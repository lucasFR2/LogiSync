<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - LogiSync WMS</title>
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
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <!-- Header -->
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-handshake text-rose-600 mr-2"></i>Fornecedores
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <section class="p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-600"></i>
                        <span class="text-green-700">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Controles -->
                <div class="mb-6 flex justify-between items-center">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Pesquisar por nome..." 
                            class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </form>
                    <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Novo
=======
@extends('layouts.app')

@section('title', 'Fornecedores')
@section('page-title', 'Fornecedores')
@section('page-subtitle', 'Gerencie seus fornecedores e parceiros comerciais')

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:1.5rem; padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Controls bar --}}
    <div class="card anim-entrance" style="padding:1.5rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
            <form method="GET" action="{{ route('suppliers.index') }}" style="display:flex; gap:0.75rem; flex:1; min-width:300px;">
                <div style="position:relative; flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou CNPJ..." class="form-input" style="padding-left:2.75rem; width:100%;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary" title="Limpar">
                        <i class="fa-solid fa-times"></i>
>>>>>>> Stashed changes
                    </a>
                @endif
            </form>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Novo Fornecedor
            </a>
        </div>
    </div>

<<<<<<< Updated upstream
                <!-- Tabela -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if ($suppliers->count() > 0)
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nome</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">CNPJ</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">E-mail</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Telefone</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-6 py-3">{{ $supplier->name }}</td>
                                        <td class="px-6 py-3">{{ $supplier->cnpj ?? '-' }}</td>
                                        <td class="px-6 py-3">{{ $supplier->email ?? '-' }}</td>
                                        <td class="px-6 py-3">{{ $supplier->phone ?? '-' }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline;" onsubmit="return confirm('Tem certeza?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="px-6 py-12 text-center text-gray-500">
                            Nenhum fornecedor encontrado
                        </div>
                    @endif
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $suppliers->links() }}
                </div>
            </section>
        </main>
=======
    {{-- Table --}}
    <div class="card anim-entrance" style="animation-delay:0.1s;">
        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($suppliers->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nome / Razão Social</th>
                            <th>CNPJ / Documento</th>
                            <th>E-mail de Contato</th>
                            <th>Telefone</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $supplier->name }}</div>
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem; color:var(--text-secondary);">
                                    {{ $supplier->cnpj ?? '—' }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $supplier->email ?? '—' }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $supplier->phone ?? '—' }}
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; justify-content:center; gap:0.5rem;">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline;" onsubmit="return confirm('Excluir este fornecedor?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="color:var(--red);" title="Excluir">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($suppliers->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state" style="padding:6rem 2rem;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-building" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhum fornecedor encontrado</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Adicione seus fornecedores para vinculá-los às entradas de estoque.</p>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary" style="padding:1rem 2.5rem;">
                        <i class="fa-solid fa-plus"></i> Adicionar Fornecedor
                    </a>
                </div>
            @endif
        </div>
>>>>>>> Stashed changes
    </div>

</div>
@endsection

