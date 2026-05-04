<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LogiSync WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">

    <div class="min-h-screen flex">
        <!-- Sidebar (Barra Lateral) -->
        <aside class="w-64 bg-slate-900 text-white hidden md:flex flex-col">
            <!-- LOGO ADICIONADA AQUI -->
            <div class="p-6 border-b border-slate-800 flex justify-center">
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
                <a href="#" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-clipboard-list mr-3"></i> Relatórios
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
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-700">Visão Geral</h2>
                
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 uppercase">{{ Auth::user()->role }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Grid de Cards de Estatísticas -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card 1 -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Total em Estoque</p>
                                <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        <p class="text-xs text-green-500 mt-4"><i class="fa-solid fa-arrow-up"></i> Aguardando...</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Pedidos Pendentes</p>
                                <h3 class="text-2xl font-bold text-gray-800">0</h3>
                            </div>
                            <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-orange-500 mt-4">Aguardando separação</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Produtos em Alerta</p>
                                <h3 class="text-2xl font-bold text-red-600">0</h3>
                            </div>
                            <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-4">Estoque abaixo do mínimo</p>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Seu Cargo</p>
                                <h3 class="text-2xl font-bold text-gray-800 capitalize">{{ Auth::user()->role }}</h3>
                            </div>
                            <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-4 text-truncate">CPF: {{ Auth::user()->cpf }}</p>
                    </div>
                </div>

                <!-- Tabela de Atividade Recente -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Últimas Movimentações</h3>
                        <button class="text-sm text-blue-600 hover:underline">Ver tudo</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Produto</th>
                                    <th class="px-6 py-4 font-semibold">Tipo</th>
                                    <th class="px-6 py-4 font-semibold">Quantidade</th>
                                    <th class="px-6 py-4 font-semibold">Data</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">Palete de Plástico XP-200</td>
                                    <td class="px-6 py-4 italic">Entrada</td>
                                    <td class="px-6 py-4 text-blue-600 font-bold">+50</td>
                                    <td class="px-6 py-4">30/04/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Concluído</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">Caixa de Papelão Reforçada</td>
                                    <td class="px-6 py-4 italic">Saída</td>
                                    <td class="px-6 py-4 text-red-600 font-bold">-120</td>
                                    <td class="px-6 py-4">29/04/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Pendente</span>
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