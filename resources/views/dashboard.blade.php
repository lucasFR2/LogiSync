<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LogiSync WMS</title>
    <script>
        tailwindConfig = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    {{-- O script é carregado aqui para aplicar o tema ANTES do paint (evita flash) --}}
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-50 dark:bg-slate-950 font-sans transition-colors duration-300">

    <div class="min-h-screen flex">
        <!-- Sidebar (Barra Lateral) -->
        <aside class="w-64 bg-slate-900 dark:bg-slate-950 text-white hidden md:flex flex-col">
            <!-- LOGO -->
            <div class="p-6 border-b border-slate-800 dark:border-slate-700 flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('invoices.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-file-invoice mr-3"></i> Notas Fiscais
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
            <!-- Header Superior -->
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-chart-line text-blue-600 mr-2"></i>Dashboard
                </h1>
                <div class="flex items-center gap-4">
                    {{-- Botão de alternar tema --}}
                    <button
                        id="theme-toggle-btn"
                        onclick="toggleTheme()"
                        data-theme-toggle
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors duration-200 text-gray-600 dark:text-gray-400"
                        title="Alternar tema"
                        aria-label="Alternar entre tema claro e escuro"
                    >
                        <i class="fa-solid fa-moon text-lg"></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Grid de Cards de Estatísticas -->
            <div class="p-8">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-6">Visão Geral</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card 1 -->
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total em Estoque</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">0</h3>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        <p class="text-xs text-green-500 mt-4"><i class="fa-solid fa-arrow-up"></i> Aguardando...</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pedidos Pendentes</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">0</h3>
                            </div>
                            <div class="p-3 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-orange-500 mt-4">Aguardando separação</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Produtos em Alerta</p>
                                <h3 class="text-2xl font-bold text-red-600">0</h3>
                            </div>
                            <div class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Estoque abaixo do mínimo</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Seu Cargo</p>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-white capitalize">{{ Auth::user()->role }}</h3>
                            </div>
                            <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-truncate">CPF: {{ Auth::user()->cpf }}</p>
                    </div>
                </div>

                <!-- Tabela de Atividade Recente -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 dark:text-white">Últimas Movimentações</h3>
                        <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Ver tudo</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Produto</th>
                                    <th class="px-6 py-4 font-semibold">Tipo</th>
                                    <th class="px-6 py-4 font-semibold">Quantidade</th>
                                    <th class="px-6 py-4 font-semibold">Data</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                        <i class="fa-solid fa-inbox text-4xl mb-3 block opacity-40"></i>
                                        Nenhuma movimentação registrada
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
=======
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral das movimentações do armazém')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2.5rem;">

    {{-- Welcome Card --}}
    <div class="card" style="background: linear-gradient(135deg, #0F172A, #1E293B); color: white; border: none; padding: 2.5rem; position: relative; overflow: hidden; backdrop-filter: none; -webkit-backdrop-filter: none;">
        <div style="position: relative; z-index: 2;">
            <h2 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.5rem;">Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
            <p style="opacity: 0.8; font-size: 1.1rem; max-width: 600px;">
                O sistema LogiSync está operando normalmente. Você tem <strong>{{ $pendingOrders ?? 0 }}</strong> pedidos aguardando processamento hoje.
            </p>
            <div class="flex" style="margin-top: 1.5rem; gap: 1rem;">
                <a href="{{ route('inventory.index') }}" class="btn" style="background: #FFFFFF; color: #0F172A;">
                    <i class="fa-solid fa-plus"></i> Nova Entrada
                </a>
                <a href="{{ route('products.index') }}" class="btn" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.25);">
                    Gerenciar Produtos
                </a>
            </div>
        </div>
        {{-- Decorative element --}}
        <div style="position: absolute; right: -50px; bottom: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; right: 20px; top: 20px; font-size: 8rem; opacity: 0.05; pointer-events: none;">
            <i class="fa-solid fa-warehouse"></i>
        </div>
>>>>>>> Stashed changes
    </div>

    {{-- Stat Cards --}}
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
        {{-- Total em Estoque --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--blue-bg); color: var(--blue);">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="stat-label">Total em Estoque</div>
            <div class="stat-value">{{ $totalStock ?? 0 }}</div>
            <div class="badge badge-success" style="width: fit-content; margin-top: 0.5rem;">
                <i class="fa-solid fa-check"></i> Sincronizado
            </div>
        </div>

        {{-- Pedidos Pendentes --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--orange-bg); color: var(--orange);">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-label">Pedidos Pendentes</div>
            <div class="stat-value">{{ $pendingOrders ?? 0 }}</div>
            <div class="badge badge-warning" style="width: fit-content; margin-top: 0.5rem;">
                <i class="fa-solid fa-hourglass-half"></i> Pendente
            </div>
        </div>

        {{-- Produtos em Alerta --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--red-bg); color: var(--red);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-label">Produtos em Alerta</div>
            <div class="stat-value" style="color: var(--red);">{{ $lowStockCount ?? 0 }}</div>
            <div class="badge badge-danger" style="width: fit-content; margin-top: 0.5rem;">
                <i class="fa-solid fa-arrow-down"></i> Estoque Baixo
            </div>
        </div>

        {{-- Usuário --}}
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent-subtle); color: var(--accent);">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-label">Acesso</div>
            <div class="stat-value" style="font-size: 1.5rem;">{{ ucfirst(Auth::user()->role) }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: 0.5rem;">
                ID: {{ Auth::user()->id }} · CPF: {{ Auth::user()->cpf }}
            </div>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Últimas Movimentações</h3>
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                Ver Todas <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem;"></i>
            </a>
        </div>
        <div class="table-wrap" style="border:none; box-shadow:none;">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state" style="padding: 5rem 2rem;">
                                <div style="width:80px; height:80px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem;">
                                    <i class="fa-solid fa-folder-open" style="font-size:2rem; color:var(--text-muted);"></i>
                                </div>
                                <h4 style="font-family:'Outfit'; font-size:1.25rem;">Nenhuma movimentação recente</h4>
                                <p style="color:var(--text-muted); margin-bottom:1.5rem;">As novas entradas de mercadorias aparecerão listadas nesta tabela.</p>
                                <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Registrar Primeira Entrada
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
