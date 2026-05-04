<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - LogiSync WMS</title>
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
                    <i class="fa-solid fa-pencil text-blue-600 mr-2"></i>Editar Produto
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
                <div class="max-w-3xl">
                    <!-- Barra de Navegação -->
                    <div class="mb-6 flex items-center gap-2 text-sm">
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                        </a>
                        <i class="fa-solid fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $product->name }}
                        </a>
                        <i class="fa-solid fa-chevron-right text-gray-400"></i>
                        <span class="text-gray-600">Editar</span>
                    </div>

                    <!-- Card do Formulário -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header do Card -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">Editar Informações do Produto</h2>
                            <p class="text-blue-100 text-sm mt-1">Atualize os dados do produto {{ $product->name }}</p>
                        </div>

                        <!-- Formulário -->
                        <form method="POST" action="{{ route('products.update', $product) }}" class="p-6">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-red-700 font-semibold mb-2">
                                        <i class="fa-solid fa-exclamation-circle mr-2"></i>Erro ao validar o formulário:
                                    </p>
                                    <ul class="text-red-600 text-sm space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Grid de Campos -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome do Produto -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nome do Produto <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name"
                                        value="{{ old('name', $product->name) }}"
                                        placeholder="Ex: Notebook Dell Inspiron"
                                        class="w-full px-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    >
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="sku" class="block text-sm font-semibold text-gray-700 mb-2">
                                        SKU <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="sku" 
                                        id="sku"
                                        value="{{ old('sku', $product->sku) }}"
                                        placeholder="Ex: SKU-001"
                                        class="w-full px-4 py-3 border @error('sku') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    >
                                    @error('sku')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Preço Unitário -->
                                <div>
                                    <label for="unit_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Preço Unitário (R$) <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="unit_price" 
                                        id="unit_price"
                                        value="{{ old('unit_price', $product->unit_price) }}"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        class="w-full px-4 py-3 border @error('unit_price') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    >
                                    @error('unit_price')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Quantidade -->
                                <div>
                                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Quantidade em Estoque <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity"
                                        value="{{ old('quantity', $product->quantity) }}"
                                        placeholder="0"
                                        min="0"
                                        class="w-full px-4 py-3 border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    >
                                    @error('quantity')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Nível de Ressuprimento -->
                                <div>
                                    <label for="reorder_level" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nível de Ressuprimento <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="reorder_level" 
                                        id="reorder_level"
                                        value="{{ old('reorder_level', $product->reorder_level) }}"
                                        placeholder="0"
                                        min="0"
                                        class="w-full px-4 py-3 border @error('reorder_level') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required
                                    >
                                    @error('reorder_level')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Descrição -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descrição (Opcional)
                                    </label>
                                    <textarea 
                                        name="description" 
                                        id="description"
                                        placeholder="Descreva as características do produto..."
                                        rows="4"
                                        class="w-full px-4 py-3 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="mt-8 flex gap-4">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-save"></i> Atualizar Produto
                                </button>
                                <a href="{{ route('products.show', $product) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
