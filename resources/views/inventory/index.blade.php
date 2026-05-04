<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Entradas - LogiSync WMS</title>
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
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
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
                <h1 class="text-2xl font-bold text-slate-900">
                    <i class="fa-solid fa-truck-ramp-box text-blue-600 mr-2"></i>Controle de Entradas
                </h1>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <section class="p-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total de Entradas -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-semibold">Total de Entradas</p>
                                <p class="text-3xl font-bold mt-2">{{ $inventories->total() }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-up-to-line text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Entradas este mês -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-100 text-sm font-semibold">Este Mês</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->where('created_at', '>=', now()->startOfMonth())->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-calendar text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Entradas hoje -->
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-yellow-100 text-sm font-semibold">Hoje</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->where('created_at', '>=', now()->startOfDay())->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-sun text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Produtos com entrada -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold">Produtos Movimentados</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->groupBy('product_id')->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-cubes text-4xl opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Entradas -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fa-solid fa-history mr-2 text-blue-600"></i>Histórico de Entradas
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">Últimas movimentações de entrada no estoque</p>
                    </div>

                    @if ($inventories->count() > 0)
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Produto</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">SKU</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Observações</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $inventory)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-600 text-sm">
                                            <div class="font-semibold">{{ $inventory->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs">{{ $inventory->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ $inventory->product->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $inventory->product->sku }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-semibold">
                                                +{{ $inventory->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-sm">
                                            {{ $inventory->notes ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('products.show', $inventory->product) }}" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition inline-block" title="Ver Detalhes do Produto">
                                                <i class="fa-solid fa-arrow-up-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="px-6 py-12 text-center">
                            <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Nenhuma entrada registrada</p>
                            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-semibold">
                                <i class="fa-solid fa-arrow-left mr-2"></i>Ir para Produtos
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Paginação -->
                @if ($inventories->count() > 0)
                    <div class="mt-6">
                        {{ $inventories->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>

</body>
</html>
