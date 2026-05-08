<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto - LogiSync WMS</title>
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
                    <i class="fa-solid fa-box text-[#2563EB] mr-2"></i>Detalhes do Produto
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
                <!-- Barra de Navegação -->
                <div class="mb-6 flex items-center gap-2 text-sm">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                    </a>
                    <i class="fa-solid fa-chevron-right text-[#1E293B]"></i>
                    <span class="text-[#94A3B8]">{{ $product->name }}</span>
                </div>

                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-600 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-400"></i>
                        <span class="text-green-300">{{ $message }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informações do Produto -->
                    <div class="lg:col-span-2">
                        <div class="bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                                <h2 class="text-lg font-bold text-white">{{ $product->name }}</h2>
                                <div class="flex gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center gap-2">
                                        <i class="fa-solid fa-pencil"></i> Editar
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                                            <i class="fa-solid fa-trash"></i> Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Detalhes -->
                            <div class="p-6">
                                <!-- SEÇÃO 1: Informações Básicas -->
                                <div class="mb-8 pb-8 border-b border-[#1E293B]">
                                    <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-info-circle text-blue-600"></i>Informações Básicas
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Código de Barras</label>
                                            <p class="text-sm text-gray-500">{{ $product->barcode ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Categoria</label>
                                            <p class="text-sm text-gray-500">{{ ucfirst($product->category ?? 'Não informada') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Unidade de Medida</label>
                                            <p class="text-sm text-gray-500">{{ strtoupper($product->unit ?? 'un') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Fornecedor</label>
                                            <p class="text-sm text-gray-500">{{ $product->supplier?->name ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Status</label>
                                            <p class="text-sm">
                                                @if ($product->status == 'ativo')
                                                    <span class="px-2 py-1  text-green-500  text-xs font-bold"><i class="fa-solid fa-check mr-1"></i>Ativo</span>
                                                @elseif ($product->status == 'inativo')
                                                    <span class="px-2 py-1  text-red-500  text-xs font-bold"><i class="fa-solid fa-ban mr-1"></i>Inativo</span>
                                                @else
                                                    <span class="px-2 py-1  text-[#FFFFFF]  text-xs font-bold"><i class="fa-solid fa-pause mr-1"></i>Descontinuado</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4">
                                        <label class="text-xs text-gray-200 font-bold">Descrição</label>
                                        <p class="text-gray-500 mt-1">{{ $product->description ?? '—' }}</p>
                                    </div>
                                </div>

                                <!-- SEÇÃO 2: Preços e Estoque -->
                                <div class="mb-8 pb-8 border-b border-[#1E293B]">
                                    <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-green-600"></i>Preços e Estoque
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class=" p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Preço de Venda</label>
                                            <p class="text-2xl text-gray-500 font-bold">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class=" p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Custo Unitário</label>
                                            <p class="text-xl text-gray-500 font-bold">R$ {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}</p>
                                        </div>
                                        <div class=" p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Quantidade em Estoque</label>
                                            <p class="text-2xl text-gray-500 font-bold">{{ $product->quantity }}</p>
                                        </div>
                                        <div class=" p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Valor Total</label>
                                            <p class="text-lg text-gray-500 font-bold">R$ {{ number_format($product->quantity * $product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div class="bg-[#FFFFFF] p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Nível de Ressuprimento</label>
                                            <p class="text-xl text-gray-500 font-bold">{{ $product->reorder_level }}</p>
                                        </div>
                                        <div class=" p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Estoque Máximo</label>
                                            <p class="text-xl text-gray-500 font-bold">{{ $product->max_stock ?? '—' }}</p>
                                        </div>
                                        <div class="p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Qtd. por Embalagem</label>
                                            <p class="text-xl text-gray-500 font-bold">{{ $product->package_quantity ?? 1 }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-semibold">Status de Estoque</label>
                                            <p class="text-sm mt-1">
                                                @if ($product->quantity <= $product->reorder_level)
                                                    <span class="px-2 py-1  text-red-500 rounded text-xs font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Abaixo do limite</span>
                                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    <span class="px-2 py-1  text-yellow-500 rounded text-xs font-semibold"><i class="fa-solid fa-exclamation mr-1"></i>Atenção</span>
                                                @else
                                                    <span class="px-2 py-1  text-green-500 rounded text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i>Em estoque</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEÇÃO 3: Dimensões e Peso -->
                                @if ($product->weight || $product->height || $product->width || $product->depth)
                                    <div class="mb-8 pb-8 border-b border-[#1E293B]">
                                        <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-combined text-purple-600"></i>Dimensões e Peso
                                        </h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Peso</label>
                                                <p class="text-lg text-gray-500 font-semibold">{{ $product->weight ?? '—' }} kg</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Altura</label>
                                                <p class="text-lg text-gray-500 font-semibold">{{ $product->height ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Largura</label>
                                                <p class="text-lg text-gray-500 font-semibold">{{ $product->width ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Profundidade</label>
                                                <p class="text-lg text-gray-500 font-semibold">{{ $product->depth ?? '—' }} cm</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- SEÇÃO 4: Localização no Armazém -->
                                @if ($product->warehouse_location)
                                    <div>
                                        <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-warehouse text-orange-600"></i>Localização no Armazém
                                        </h3>
                                        <div class="bg-gray-800 p-4 rounded-lg">
                                            <p class="text-lg text-gray-500 font-bold">{{ $product->warehouse_location }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                <!-- Histórico de Entradas -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gray-100 border-b border-[#1E293B] px-6 py-4">
                            <h3 class="text-lg font-bold text-[#FFFFFF]">
                                <i class="fa-solid fa-history mr-2 text-blue-600"></i>Histórico de Entradas
                            </h3>
                        </div>

                        <!-- Tabela -->
                        @if ($inventories->count() > 0)
                            <table class="w-full">
                                <thead class="border-b border-[#1E293B]">
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#FFFFFF]">Data</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-[#FFFFFF]">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-[#FFFFFF]">Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr class="border-b border-[#1E293B] hover:bg-gray-800 transition">
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $inventory->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1  text-green-500 rounded-lg text-sm font-semibold">
                                                    +{{ $inventory->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $inventory->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Paginação -->
                            <div class="px-6 py-4 border-t border-b border-[#1E293B]">
                                {{ $inventories->links() }}
                            </div>
                        @else
                            <div class="px-6 py-12 text-center">
                                <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">Nenhuma entrada registrada para este produto</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
