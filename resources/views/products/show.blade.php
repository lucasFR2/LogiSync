<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto - LogiSync WMS</title>
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
                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
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
                <h1 class="text-2xl font-bold text-slate-900">
                    <i class="fa-solid fa-box text-blue-600 mr-2"></i>Detalhes do Produto
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
                <!-- Barra de Navegação -->
                <div class="mb-6 flex items-center gap-2 text-sm">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-600">{{ $product->name }}</span>
                </div>

                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-600"></i>
                        <span class="text-green-700">{{ $message }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informações do Produto -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                <!-- SKU e Status -->
                                <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">SKU</label>
                                        <p class="text-lg text-gray-900 font-semibold">{{ $product->sku }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">Status</label>
                                        <p class="text-lg">
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
                                                @if ($product->quantity <= $product->reorder_level)
                                                    Abaixo do limite
                                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    Atenção
                                                @else
                                                    Em estoque
                                                @endif
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Preço e Quantidade -->
                                <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">Preço Unitário</label>
                                        <p class="text-2xl text-gray-900 font-bold">R$ {{ number_format($product->unit_price, 2, '.', '') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">Quantidade em Estoque</label>
                                        <p class="text-2xl text-blue-600 font-bold">{{ $product->quantity }}</p>
                                    </div>
                                </div>

                                <!-- Níveis -->
                                <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">Nível de Ressuprimento</label>
                                        <p class="text-lg text-gray-900">{{ $product->reorder_level }} unidades</p>
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-600 font-semibold">Valor Total em Estoque</label>
                                        <p class="text-lg text-gray-900 font-semibold">R$ {{ number_format($product->quantity * $product->unit_price, 2, '.', '') }}</p>
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div>
                                    <label class="text-sm text-gray-600 font-semibold">Descrição</label>
                                    <p class="text-gray-700 mt-2">
                                        {{ $product->description ?? 'Sem descrição informada' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Entrada (Controle de Entradas) -->
                    <div>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-8">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                                <h2 class="text-lg font-bold text-white">
                                    <i class="fa-solid fa-arrow-up-to-line mr-2"></i>Registrar Entrada
                                </h2>
                            </div>

                            <!-- Formulário -->
                            <form method="POST" action="{{ route('products.add-inventory', $product) }}" class="p-6 space-y-4">
                                @csrf

                                <!-- Quantidade -->
                                <div>
                                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Quantidade <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity"
                                        placeholder="Digite a quantidade"
                                        min="1"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required
                                    >
                                </div>

                                <!-- Notas -->
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Observações (Opcional)
                                    </label>
                                    <textarea 
                                        name="notes" 
                                        id="notes"
                                        placeholder="Ex: Lote 123, Fornecedor ABC..."
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    ></textarea>
                                </div>

                                <!-- Botão -->
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-arrow-up-to-line"></i> Confirmar Entrada
                                </button>
                            </form>

                            <!-- Info Box -->
                            <div class="px-6 py-4 bg-blue-50 border-t border-gray-200">
                                <p class="text-sm text-blue-700">
                                    <i class="fa-solid fa-info-circle mr-2"></i>
                                    <strong>Quantidade atual:</strong> {{ $product->quantity }} unidades
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Entradas -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fa-solid fa-history mr-2 text-blue-600"></i>Histórico de Entradas
                            </h3>
                        </div>

                        <!-- Tabela -->
                        @if ($inventories->count() > 0)
                            <table class="w-full">
                                <thead class="border-b border-gray-200">
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $inventory->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-semibold">
                                                    +{{ $inventory->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $inventory->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Paginação -->
                            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
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
