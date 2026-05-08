<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LogiSync WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-dark.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <style>
        :root {
            --color-bg-primary: #020617;
            --color-bg-card: #0F172A;
            --color-border: #1E293B;
            --color-text-primary: #FFFFFF;
            --color-text-secondary: #94A3B8;
        }
        
        .dark {
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
        }
        
        .card-dark {
            background-color: var(--color-bg-card);
            border-color: var(--color-border);
            color: var(--color-text-primary);
        }
        
        .card-dark:hover {
            background-color: #111927;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-[#020617] dark:bg-[#020617] font-sans transition-colors text-[#FFFFFF]">

    <div class="min-h-screen flex bg-[#020617]">
        <!-- Sidebar (Barra Lateral) -->
        <aside class="w-64 bg-[#0F172A] text-white hidden md:flex flex-col border-r border-[#1E293B] shadow-xl">
            <!-- LOGO ADICIONADA AQUI -->
            <div class="p-6 border-b border-[#1E293B] flex justify-center">
                <a href="/" class="hover:opacity-80 transition-opacity">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-6 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 bg-blue-600 hover:bg-blue-700 rounded-xl text-white transition-all shadow-md">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
                </a>
            </nav>

            <div class="p-4 border-t border-[#1E293B]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full p-3 text-red-400 hover:bg-red-900/20 rounded-xl transition-colors">
                        <i class="fa-solid fa-right-from-bracket mr-3"></i> Sair
                    </button>
                </form>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="flex-1 bg-[#020617]">
            <!-- Header Superior -->
            <header class="bg-[#0F172A] shadow-lg px-8 py-6 flex justify-between items-center border-b border-[#1E293B]">
                <div>
                    <h1 class="text-3xl font-bold text-[#FFFFFF]">
                        <i class="fa-solid fa-chart-line text-blue-500 mr-3"></i>Dashboard
                    </h1>
                    <p class="text-[#94A3B8] text-sm mt-1">Bem-vindo à sua visão geral de movimentações</p>
                </div>
                <div class="flex items-center gap-6">
                    
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-[#FFFFFF]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#94A3B8] uppercase tracking-wider">{{ Auth::user()->role }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Grid de Cards de Estatísticas -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card 1 -->
                    <div class="card-dark p-6 rounded-2xl border transition-all duration-300 hover:shadow-xl hover:border-blue-500/30">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <p class="text-[#94A3B8] text-sm font-medium mb-2">Total em Estoque</p>
                                <h3 class="text-4xl font-bold text-[#FFFFFF] mb-1">0</h3>
                            </div>
                            <div class="p-4 bg-blue-600/20 text-blue-400 rounded-xl">
                                <i class="fa-solid fa-box text-xl"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-green-400 text-xs">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                            <span>Aguardando sincronização</span>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="card-dark p-6 rounded-2xl border transition-all duration-300 hover:shadow-xl hover:border-orange-500/30">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <p class="text-[#94A3B8] text-sm font-medium mb-2">Pedidos Pendentes</p>
                                <h3 class="text-4xl font-bold text-[#FFFFFF] mb-1">0</h3>
                            </div>
                            <div class="p-4 bg-orange-600/20 text-orange-400 rounded-xl">
                                <i class="fa-solid fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-orange-400 text-xs">
                            <i class="fa-solid fa-hourglass-end"></i>
                            <span>Aguardando separação</span>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="card-dark p-6 rounded-2xl border transition-all duration-300 hover:shadow-xl hover:border-red-500/30">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <p class="text-[#94A3B8] text-sm font-medium mb-2">Produtos em Alerta</p>
                                <h3 class="text-4xl font-bold text-red-400 mb-1">0</h3>
                            </div>
                            <div class="p-4 bg-red-600/20 text-red-400 rounded-xl">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-red-400 text-xs">
                            <i class="fa-solid fa-exclamation-circle"></i>
                            <span>Estoque abaixo do mínimo</span>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="card-dark p-6 rounded-2xl border transition-all duration-300 hover:shadow-xl hover:border-green-500/30">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <p class="text-[#94A3B8] text-sm font-medium mb-2">Seu Cargo</p>
                                <h3 class="text-3xl font-bold text-[#FFFFFF] mb-1 capitalize">{{ Auth::user()->role }}</h3>
                            </div>
                            <div class="p-4 bg-green-600/20 text-green-400 rounded-xl">
                                <i class="fa-solid fa-user-shield text-xl"></i>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-[#94A3B8] text-xs">
                            <i class="fa-solid fa-id-card"></i>
                            <span class="truncate">CPF: {{ Auth::user()->cpf }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Atividade Recente -->
                <div class="card-dark rounded-2xl border overflow-hidden shadow-lg">
                    <div class="p-6 border-b border-[#1E293B] flex justify-between items-center">
                        <h3 class="font-bold text-[#FFFFFF] text-lg flex items-center gap-2">
                            <i class="fa-solid fa-history text-blue-400"></i>
                            Últimas Movimentações
                        </h3>
                        <button class="text-sm text-blue-400 hover:text-blue-300 transition flex items-center gap-1">
                            Ver tudo <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-[#1E293B]/50 text-[#94A3B8] text-sm font-semibold uppercase tracking-wider">
                                <tr class="border-b border-[#1E293B]">
                                    <th class="px-6 py-4">Produto</th>
                                    <th class="px-6 py-4">Tipo</th>
                                    <th class="px-6 py-4">Quantidade</th>
                                    <th class="px-6 py-4">Data</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-[#1E293B]/50 hover:bg-[#111927] transition-colors">
                                    <td colspan="5" class="px-6 py-8 text-center text-[#94A3B8]">
                                        <i class="fa-solid fa-inbox text-2xl mb-2 block opacity-50"></i>
                                        Nenhuma movimentação registrada
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>