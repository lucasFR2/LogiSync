<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Produtos - LogiSync WMS</title>
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
                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
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
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-boxes-stacked text-blue-600 mr-2"></i>Consulta de Produtos
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

            <!-- Conteúdo da Página -->
            <section class="p-8 bg-gray-50 dark:bg-slate-950">
                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-900 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400"></i>
                        <span class="text-green-700 dark:text-green-300">{{ $message }}</span>
                    </div>
                @endif

                <!-- Controles Superiores -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Barra de Pesquisa -->
                    <form method="GET" action="{{ route('products.index') }}" class="flex-1 w-full md:w-auto">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Pesquisar por nome ou código de barras..." 
                                value="{{ $search ?? '' }}"
                                class="w-full px-4 py-3 pl-10 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors"
                            >
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </form>

                    <!-- Botão Novo Produto -->
                    <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition w-full md:w-auto justify-center">
                        <i class="fa-solid fa-plus"></i> Novo Produto
                    </a>
                </div>

                <!-- Tabela de Produtos -->
                <div class="bg-white dark:bg-slate-900 rounded-lg shadow-md overflow-hidden">
                    @if ($products->count() > 0)
                        <table class="w-full">
                            <thead class="bg-gray-100 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Produto</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Código de Barras</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Quantidade</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">Valor Unit.</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Nível Ressupr.</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr class="border-b border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($product->description, 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $product->barcode ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="
                                                px-3 py-1 rounded-lg text-sm font-semibold
                                                @if ($product->quantity <= $product->reorder_level)
                                                    bg-red-100 text-red-800
                                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-green-100 text-green-800
                                                @endif
                                            ">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-900 dark:text-white font-semibold">
                                            R$ {{ number_format($product->unit_price, 2, '.', '') }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $product->reorder_level }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition" title="Ver Detalhes">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded transition" title="Editar">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded transition" title="Deletar">
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
                        <div class="px-6 py-12 text-center bg-white dark:bg-slate-900">
                            <i class="fa-solid fa-inbox text-6xl text-gray-300 dark:text-slate-700 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Nenhum produto encontrado</p>
                            <a href="{{ route('products.create') }}" class="mt-4 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>Cadastre um novo produto
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </section>
        </main>
    </div>

</body>
</html>
