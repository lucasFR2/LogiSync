<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - LogiSync WMS</title>
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
                    <i class="fa-solid fa-plus-circle text-blue-600 mr-2"></i>Cadastro de Produtos
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
                        <span class="text-gray-600">Novo Produto</span>
                    </div>

                    <!-- Card do Formulário -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header do Card -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <h2 class="text-lg font-bold text-white">Informações do Produto</h2>
                            <p class="text-blue-100 text-sm mt-1">Preencha os dados abaixo para cadastrar um novo produto</p>
                        </div>

                        <!-- Formulário -->
                        <form method="POST" action="{{ route('products.store') }}" class="p-6">
                            @csrf

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

                            <!-- SEÇÃO 1: Informações Básicas -->
                            <div class="mb-8 pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
                                    <i class="fa-solid fa-info-circle text-blue-600"></i>Informações Básicas
                                </h3>
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
                                            value="{{ old('name') }}"
                                            placeholder="Ex: Notebook Dell Inspiron 15"
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
                                            value="{{ old('sku') }}"
                                            placeholder="Ex: NOTEB-DELL-001"
                                            class="w-full px-4 py-3 border @error('sku') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                        @error('sku')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Código de Barras -->
                                    <div>
                                        <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Código de Barras
                                        </label>
                                        <input 
                                            type="text" 
                                            name="barcode" 
                                            id="barcode"
                                            value="{{ old('barcode') }}"
                                            placeholder="Ex: 1234567890123"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Descrição -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Descrição Detalhada
                                        </label>
                                        <textarea 
                                            name="description" 
                                            id="description"
                                            placeholder="Descreva as características, especificações técnicas do produto..."
                                            rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 2: Preços e Estoque -->
                            <div class="mb-8 pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
                                    <i class="fa-solid fa-tag text-green-600"></i>Preços e Estoque
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Custo Unitário -->
                                    <div>
                                        <label for="cost_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Custo Unitário (R$)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="cost_price" 
                                            id="cost_price"
                                            value="{{ old('cost_price') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Preço Unitário -->
                                    <div>
                                        <label for="unit_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Preço de Venda (R$) <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="unit_price" 
                                            id="unit_price"
                                            value="{{ old('unit_price') }}"
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

                                    <!-- Quantidade Atual -->
                                    <div>
                                        <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Quantidade em Estoque <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="quantity" 
                                            id="quantity"
                                            value="{{ old('quantity', 0) }}"
                                            placeholder="0"
                                            min="0"
                                            class="w-full px-4 py-3 border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                        @error('quantity')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Estoque Máximo -->
                                    <div>
                                        <label for="max_stock" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Estoque Máximo
                                        </label>
                                        <input 
                                            type="number" 
                                            name="max_stock" 
                                            id="max_stock"
                                            value="{{ old('max_stock', 0) }}"
                                            placeholder="0"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
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
                                            value="{{ old('reorder_level', 0) }}"
                                            placeholder="0"
                                            min="0"
                                            class="w-full px-4 py-3 border @error('reorder_level') border-red-500 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required
                                        >
                                        @error('reorder_level')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Quantidade por Embalagem -->
                                    <div>
                                        <label for="package_quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Quantidade por Embalagem
                                        </label>
                                        <input 
                                            type="number" 
                                            name="package_quantity" 
                                            id="package_quantity"
                                            value="{{ old('package_quantity', 1) }}"
                                            placeholder="1"
                                            min="1"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 3: Dimensões e Peso -->
                            <div class="mb-8 pb-8 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
                                    <i class="fa-solid fa-ruler-combined text-purple-600"></i>Dimensões e Peso
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Peso (kg) -->
                                    <div>
                                        <label for="weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Peso (kg)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="weight" 
                                            id="weight"
                                            value="{{ old('weight') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Altura (cm) -->
                                    <div>
                                        <label for="height" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Altura (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="height" 
                                            id="height"
                                            value="{{ old('height') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Largura (cm) -->
                                    <div>
                                        <label for="width" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Largura (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="width" 
                                            id="width"
                                            value="{{ old('width') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Profundidade (cm) -->
                                    <div>
                                        <label for="depth" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Profundidade (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="depth" 
                                            id="depth"
                                            value="{{ old('depth') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 4: Categorização e Localização -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center gap-2">
                                    <i class="fa-solid fa-folder-open text-orange-600"></i>Categorização e Localização
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Categoria -->
                                    <div>
                                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Categoria
                                        </label>
                                        <select 
                                            name="category" 
                                            id="category"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="">-- Selecione uma categoria --</option>
                                            <option value="eletrônicos" {{ old('category') == 'eletrônicos' ? 'selected' : '' }}>Eletrônicos</option>
                                            <option value="informática" {{ old('category') == 'informática' ? 'selected' : '' }}>Informática</option>
                                            <option value="periféricos" {{ old('category') == 'periféricos' ? 'selected' : '' }}>Periféricos</option>
                                            <option value="acessórios" {{ old('category') == 'acessórios' ? 'selected' : '' }}>Acessórios</option>
                                            <option value="software" {{ old('category') == 'software' ? 'selected' : '' }}>Software</option>
                                            <option value="outros" {{ old('category') == 'outros' ? 'selected' : '' }}>Outros</option>
                                        </select>
                                    </div>

                                    <!-- Unidade de Medida -->
                                    <div>
                                        <label for="unit" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Unidade de Medida
                                        </label>
                                        <select 
                                            name="unit" 
                                            id="unit"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                            <option value="un" {{ old('unit') == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                                            <option value="caixa" {{ old('unit') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                                            <option value="dúzia" {{ old('unit') == 'dúzia' ? 'selected' : '' }}>Dúzia</option>
                                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Quilograma (kg)</option>
                                            <option value="l" {{ old('unit') == 'l' ? 'selected' : '' }}>Litro (l)</option>
                                            <option value="m" {{ old('unit') == 'm' ? 'selected' : '' }}>Metro (m)</option>
                                        </select>
                                    </div>

                                    <!-- Localização no Armazém -->
                                    <div>
                                        <label for="warehouse_location" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Localização no Armazém
                                        </label>
                                        <input 
                                            type="text" 
                                            name="warehouse_location" 
                                            id="warehouse_location"
                                            value="{{ old('warehouse_location') }}"
                                            placeholder="Ex: Prateleira A-10"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Fornecedor -->
                                    <div>
                                        <label for="supplier" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Fornecedor Principal
                                        </label>
                                        <input 
                                            type="text" 
                                            name="supplier" 
                                            id="supplier"
                                            value="{{ old('supplier') }}"
                                            placeholder="Ex: Fornecedor XYZ"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        >
                                    </div>

                                    <!-- Status -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Status do Produto
                                        </label>
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }} class="w-4 h-4">
                                                <span class="text-sm text-gray-700">
                                                    <i class="fa-solid fa-check text-green-600 mr-1"></i>Ativo
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }} class="w-4 h-4">
                                                <span class="text-sm text-gray-700">
                                                    <i class="fa-solid fa-ban text-red-600 mr-1"></i>Inativo
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="status" value="descontinuado" {{ old('status') == 'descontinuado' ? 'checked' : '' }} class="w-4 h-4">
                                                <span class="text-sm text-gray-700">
                                                    <i class="fa-solid fa-pause text-yellow-600 mr-1"></i>Descontinuado
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="mt-8 flex gap-4">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-save"></i> Cadastrar Produto
                                </button>
                                <a href="{{ route('products.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
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
