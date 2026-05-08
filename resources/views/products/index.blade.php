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
    <link rel="stylesheet" href="{{ asset('css/dashboard-dark.css') }}">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-[#020617] font-sans text-[#FFFFFF]">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0F172A] text-white hidden md:flex flex-col border-r border-[#1E293B] shadow-xl">
            <div class="p-6 border-b border-[#1E293B] flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-[#2563EB] rounded-lg text-white">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
                </a>
            </nav>

            <div class="p-4 border-t border-[#1E293B]">
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
            <header class="bg-[#0F172A] shadow-lg px-8 py-4 flex justify-between items-center border-b border-[#1E293B]">
                <h1 class="text-2xl font-bold text-[#FFFFFF]">
                    <i class="fa-solid fa-boxes-stacked text-[#2563EB] mr-2"></i>Consulta de Produtos
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-[#1A2438] transition text-[#94A3B8]" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <span class="text-[#94A3B8]">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-[#2563EB] rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <section class="p-8 bg-[#020617]">
                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-600 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-400"></i>
                        <span class="text-green-300">{{ $message }}</span>
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
                                class="w-full px-4 py-3 pl-10 border border-[#1E293B] bg-[#0F172A] text-[#FFFFFF] placeholder-[#94A3B8] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                            >
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-[#1E293B]"></i>
                        </div>
                    </form>

                    <!-- Botão Novo Produto -->
                    <a href="{{ route('products.create') }}" class="bg-[#2563EB] hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition w-full md:w-auto justify-center">
                        <i class="fa-solid fa-plus"></i> Novo Produto
                    </a>
                </div>

                <!-- Tabela de Produtos -->
                <div class="bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] overflow-hidden">
                    @if ($products->count() > 0)
                        <table class="w-full">
                            <thead class="bg-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-[#FFFFFF]">Produto</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-[#FFFFFF]">Código de Barras</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-[#FFFFFF]">Quantidade</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-[#FFFFFF]">Valor Unit.</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-[#FFFFFF]">Nível Ressupr.</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-[#FFFFFF]">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr class="border-b border-[#1E293B] hover:bg-[#1A2438] transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-[#FFFFFF]">{{ $product->name }}</div>
                                            <div class="text-sm text-[#94A3B8]">{{ Str::limit($product->description, 40) }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-[#94A3B8]">{{ $product->barcode ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="
                                                px-3 py-1 rounded-lg text-sm font-semibold
                                                @if ($product->quantity <= $product->reorder_level)
                                                    bg-red-900/20 text-red-400
                                                    @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    bg-[#1A2438] text-[#FFFFFF]
                                                @else
                                                    bg-green-900/20 text-green-400
                                                @endif
                                            ">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-[#FFFFFF] font-semibold">
                                            R$ {{ number_format($product->unit_price, 2, '.', '') }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-[#94A3B8]">{{ $product->reorder_level }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('products.show', $product) }}" class="text-[#2563EB] hover:text-blue-400 p-2 hover:bg-[#1A2438] rounded transition" title="Ver Detalhes">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="text-[#2563EB] hover:text-blue-400 p-2 hover:bg-[#1A2438] rounded transition" title="Editar">
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
