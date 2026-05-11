<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - LogiSync WMS</title>

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
                    <i class="fa-solid fa-plus-circle text-blue-600 mr-2"></i>Cadastro de Produtos
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
            <section class="p-0 h-full overflow-y-auto bg-gray-50 dark:bg-slate-950">
                <div class="w-full h-full flex flex-col">
                    <!-- Barra de Navegação -->
                    <div class="px-8 py-4 flex items-center gap-2 text-sm border-b border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                        </a>
                        <i class="fa-solid fa-chevron-right text-gray-400"></i>
                        <span class="text-gray-600">Novo Produto</span>
                    </div>

                    <!-- Card do Formulário -->
                    <div class="flex-1 bg-gray-50 dark:bg-slate-950 overflow-y-auto">
                        <div class="w-full h-full bg-white dark:bg-slate-900 rounded-none shadow-none overflow-hidden border-0 flex flex-col">
                        <!-- Header do Card -->
                        <div class="bg-gradient-to-r from-blue-600 via-blue-650 to-blue-700 px-8 py-6 border-b-4 border-blue-800 flex-shrink-0">
                            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                                <i class="fa-solid fa-clipboard-list text-blue-100"></i>
                                Cadastro de Novo Produto
                            </h2>
                            <p class="text-blue-50 text-sm mt-2">Preencha todos os campos obrigatórios para registrar um novo produto no sistema</p>
                        </div>

                        <!-- Formulário -->
                        <form method="POST" action="{{ route('products.store') }}" class="flex-1 overflow-y-auto p-8 bg-white dark:bg-slate-900">
                            @csrf

                            @if ($errors->any())
                                <div class="mb-8 p-5 bg-red-50 dark:bg-red-950 border-l-4 border-red-500 rounded-lg backdrop-blur">
                                    <p class="text-red-700 dark:text-red-300 font-bold mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-circle-exclamation text-lg"></i>Erro ao validar o formulário:
                                    </p>
                                    <ul class="text-red-600 dark:text-red-400 text-sm space-y-1 ml-6">
                                        @foreach ($errors->all() as $error)
                                            <li class="flex items-center gap-2"><i class="fa-solid fa-times-circle text-xs"></i>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- SEÇÃO 1: Informações Básicas -->
                            <div class="mb-10 pb-10 border-b-2 border-gray-200">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 pb-4 border-b-2 border-blue-400">
                                    <span class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-info-circle text-blue-600 text-lg"></i>
                                    </span>
                                    Informações Básicas
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nome do Produto -->
                                    <div class="md:col-span-2">
                                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-box text-blue-500"></i>Nome do Produto <span class="text-red-500 text-lg">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            id="name"
                                            value="{{ old('name') }}"
                                            placeholder="Ex: Notebook Dell Inspiron 15"
                                            class="w-full px-4 py-3 border-2 @error('name') border-red-400 bg-red-50 @else border-gray-300 hover:border-blue-400 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                            required
                                        >
                                        @error('name')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Código de Barras -->
                                    <div>
                                        <label for="barcode" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-qrcode text-purple-500"></i>Código de Barras
                                        </label>
                                        <input 
                                            type="text" 
                                            name="barcode" 
                                            id="barcode"
                                            value="{{ old('barcode') }}"
                                            placeholder="Ex: 1234567890123"
                                            pattern="[0-9]{1,20}"
                                            title="Código de barras deve conter apenas números"
                                            inputmode="numeric"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-purple-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Descrição -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-align-left text-orange-500"></i>Descrição Detalhada
                                        </label>
                                        <textarea 
                                            name="description" 
                                            id="description"
                                            placeholder="Descreva as características, especificações técnicas do produto..."
                                            rows="3"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-orange-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white resize-none"
                                        >{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 2: Preços e Estoque -->
                            <div class="mb-10 pb-10 border-b-2 border-gray-200">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 pb-4 border-b-2 border-green-400">
                                    <span class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-tag text-green-600 text-lg"></i>
                                    </span>
                                    Preços e Estoque
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Custo Unitário -->
                                    <div>
                                        <label for="cost_price" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-calculator text-indigo-500"></i>Custo Unitário (R$)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="cost_price" 
                                            id="cost_price"
                                            value="{{ old('cost_price') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0.01"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-indigo-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Preço Unitário -->
                                    <div>
                                        <label for="unit_price" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-dollar-sign text-green-600"></i>Preço de Venda (R$) <span class="text-red-500 text-lg">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="unit_price" 
                                            id="unit_price"
                                            value="{{ old('unit_price') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0.01"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 @error('unit_price') border-red-400 bg-red-50 @else border-gray-300 hover:border-green-400 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                            required
                                        >
                                        @error('unit_price')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Preço de Venda Especial -->
                                    <div>
                                        <label for="selling_price" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-tag text-emerald-600"></i>Preço Especial (R$)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="selling_price" 
                                            id="selling_price"
                                            value="{{ old('selling_price') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0.01"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-emerald-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                        <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-info-circle"></i> Preço opcional para promoções ou vendas especiais</p>
                                    </div>

                                    <!-- Quantidade Atual -->
                                    <div>
                                        <label for="quantity" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-boxes text-cyan-500"></i>Quantidade em Estoque <span class="text-red-500 text-lg">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="quantity" 
                                            id="quantity"
                                            value="{{ old('quantity', 0) }}"
                                            placeholder="0"
                                            min="0"
                                            step="1"
                                            max="9999999"
                                            class="w-full px-4 py-3 border-2 @error('quantity') border-red-400 bg-red-50 @else border-gray-300 hover:border-cyan-400 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                            required
                                        >
                                        @error('quantity')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Estoque Máximo -->
                                    <div>
                                        <label for="max_stock" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-up text-blue-500"></i>Estoque Máximo
                                        </label>
                                        <input 
                                            type="number" 
                                            name="max_stock" 
                                            id="max_stock"
                                            value="{{ old('max_stock', 1) }}"
                                            placeholder="1"
                                            min="1"
                                            step="1"
                                            max="9999999"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-blue-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Nível de Ressuprimento -->
                                    <div>
                                        <label for="reorder_level" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>Nível de Ressuprimento <span class="text-red-500 text-lg">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="reorder_level" 
                                            id="reorder_level"
                                            value="{{ old('reorder_level', 0) }}"
                                            placeholder="0"
                                            min="0"
                                            step="1"
                                            max="9999999"
                                            class="w-full px-4 py-3 border-2 @error('reorder_level') border-red-400 bg-red-50 @else border-gray-300 hover:border-yellow-400 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                            required
                                        >
                                        @error('reorder_level')
                                            <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Quantidade por Embalagem -->
                                    <div>
                                        <label for="package_quantity" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-cube text-red-500"></i>Quantidade por Embalagem
                                        </label>
                                        <input 
                                            type="number" 
                                            name="package_quantity" 
                                            id="package_quantity"
                                            value="{{ old('package_quantity', 1) }}"
                                            placeholder="1"
                                            min="1"
                                            step="0.01"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-red-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 3: Dimensões e Peso -->
                            <div class="mb-10 pb-10 border-b-2 border-gray-200">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 pb-4 border-b-2 border-purple-400">
                                    <span class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-ruler-combined text-purple-600 text-lg"></i>
                                    </span>
                                    Dimensões e Peso
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Peso (kg) -->
                                    <div>
                                        <label for="weight" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-weight text-orange-500"></i>Peso (kg)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="weight" 
                                            id="weight"
                                            value="{{ old('weight') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-orange-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Altura (cm) -->
                                    <div>
                                        <label for="height" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-up text-pink-500"></i>Altura (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="height" 
                                            id="height"
                                            value="{{ old('height') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-pink-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Largura (cm) -->
                                    <div>
                                        <label for="width" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-right text-teal-500"></i>Largura (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="width" 
                                            id="width"
                                            value="{{ old('width') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-teal-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Profundidade (cm) -->
                                    <div>
                                        <label for="depth" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-left text-sky-500"></i>Profundidade (cm)
                                        </label>
                                        <input 
                                            type="number" 
                                            name="depth" 
                                            id="depth"
                                            value="{{ old('depth') }}"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            max="999999.99"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-sky-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- SEÇÃO 4: Categorização e Localização -->
                            <div class="mb-10">
                                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 pb-4 border-b-2 border-orange-400">
                                    <span class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-folder-open text-orange-600 text-lg"></i>
                                    </span>
                                    Categorização e Localização
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Categoria -->
                                    <div>
                                        <label for="category" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-list text-indigo-500"></i>Categoria
                                        </label>
                                        <select 
                                            name="category" 
                                            id="category"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-indigo-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white cursor-pointer"
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
                                        <label for="unit" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-measure text-violet-500"></i>Unidade de Medida
                                        </label>
                                        <select 
                                            name="unit" 
                                            id="unit"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-violet-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white cursor-pointer"
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
                                        <label for="warehouse_location" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-lime-500"></i>Localização no Armazém
                                        </label>
                                        <input 
                                            type="text" 
                                            name="warehouse_location" 
                                            id="warehouse_location"
                                            value="{{ old('warehouse_location') }}"
                                            placeholder="Ex: Prateleira A-10"
                                            class="w-full px-4 py-3 border-2 border-gray-300 hover:border-lime-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-400 focus:border-transparent transition-all bg-gray-50 hover:bg-white"
                                        >
                                    </div>

                                    <!-- Fornecedor -->
                                    <div class="md:col-span-2 bg-gradient-to-br from-rose-50 to-orange-50 p-6 rounded-lg border-2 border-rose-200 shadow-sm">
                                        <div class="flex items-center justify-between mb-4">
                                            <label for="supplier_id" class="block text-base font-bold text-gray-900 flex items-center gap-3">
                                                <span class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                                                    <i class="fa-solid fa-handshake text-rose-600 text-lg"></i>
                                                </span>Fornecedor Principal
                                            </label>
                                            <button type="button" onclick="openSupplierModal()" class="bg-gradient-to-r from-rose-500 to-orange-500 hover:from-rose-600 hover:to-orange-600 text-white px-5 py-2 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm">
                                                <i class="fa-solid fa-plus-circle"></i> Novo Fornecedor
                                            </button>
                                        </div>
                                        <select 
                                            name="supplier_id" 
                                            id="supplier_id"
                                            class="w-full px-4 py-3 border-2 border-rose-300 hover:border-rose-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all bg-white cursor-pointer font-medium text-gray-700"
                                        >
                                            <option value="" class="text-gray-600">-- Selecione um fornecedor --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-toggle-on text-fuchsia-500"></i>Status do Produto
                                        </label>
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-green-300 hover:bg-green-50 transition">
                                                <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }} class="w-5 h-5 text-green-600 cursor-pointer">
                                                <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-check text-green-600"></i>Ativo
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-red-300 hover:bg-red-50 transition">
                                                <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }} class="w-5 h-5 text-red-600 cursor-pointer">
                                                <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-xmark text-red-600"></i>Inativo
                                                </span>
                                            </label>
                                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-yellow-300 hover:bg-yellow-50 transition">
                                                <input type="radio" name="status" value="descontinuado" {{ old('status') == 'descontinuado' ? 'checked' : '' }} class="w-5 h-5 text-yellow-600 cursor-pointer">
                                                <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                                    <i class="fa-solid fa-circle-pause text-yellow-600"></i>Descontinuado
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="mt-10 flex gap-4">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-lg font-bold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 transform hover:scale-105">
                                    <i class="fa-solid fa-floppy-disk text-lg"></i> Cadastrar Produto
                                </button>
                                <a href="{{ route('products.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 px-8 py-4 rounded-lg font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                                    <i class="fa-solid fa-xmark text-lg"></i> Cancelar
                                </a>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para Novo Fornecedor -->
    <div id="supplierModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Header do Modal -->
            <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>Novo Fornecedor
                </h3>
                <button type="button" onclick="closeSupplierModal()" class="text-white hover:text-rose-100 transition">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Formulário -->
            <form id="supplierForm" class="p-6 space-y-4">
                @csrf
                
                <!-- Nome -->
                <div>
                    <label for="supplier_name" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-store text-rose-500"></i> Nome do Fornecedor *
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="supplier_name"
                        placeholder="Ex: Distribuidora XYZ"
                        required
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="supplier_email" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-envelope text-blue-500"></i> Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="supplier_email"
                        placeholder="contato@fornecedor.com"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Telefone -->
                <div>
                    <label for="supplier_phone" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-phone text-green-500"></i> Telefone
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="supplier_phone"
                        placeholder="(00) 00000-0000"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="supplier_cnpj" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-id-card text-purple-500"></i> CNPJ
                    </label>
                    <input 
                        type="text" 
                        name="cnpj" 
                        id="supplier_cnpj"
                        placeholder="00.000.000/0000-00"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Endereço -->
                <div>
                    <label for="supplier_address" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-map-location-dot text-orange-500"></i> Endereço
                    </label>
                    <input 
                        type="text" 
                        name="address" 
                        id="supplier_address"
                        placeholder="Rua, número, complemento..."
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Cidade -->
                <div>
                    <label for="supplier_city" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-city text-green-500"></i> Cidade
                    </label>
                    <input 
                        type="text" 
                        name="city" 
                        id="supplier_city"
                        placeholder="São Paulo"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Estado -->
                <div>
                    <label for="supplier_state" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-map text-red-500"></i> Estado (UF)
                    </label>
                    <input 
                        type="text" 
                        name="state" 
                        id="supplier_state"
                        placeholder="SP"
                        maxlength="2"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Mensagem de erro -->
                <div id="supplierError" class="hidden bg-red-50 border-l-4 border-red-500 p-3 rounded text-sm text-red-700"></div>

                <!-- Mensagem de sucesso -->
                <div id="supplierSuccess" class="hidden bg-green-50 border-l-4 border-green-500 p-3 rounded text-sm text-green-700"></div>

                <!-- Botões -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeSupplierModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 py-2 rounded-lg font-semibold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Salvar
=======
@extends('layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Novo Produto')
@section('page-subtitle', 'Preencha os dados do produto')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div style="max-width:960px;">

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        </div>
    @endif

    <div class="card anim-fade-up">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-box"></i> Dados do Produto</span>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf

                {{-- Informações Básicas --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-bottom:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-info-circle" style="margin-right:.5rem;color:var(--accent);"></i>Informações Básicas
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Nome do Produto <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Ex: Notebook Dell Inspiron 15" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código de Barras</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}"
                               placeholder="Ex: 1234567890123" pattern="[0-9]{1,13}" maxlength="13" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Descrição Detalhada</label>
                        <textarea name="description" placeholder="Descreva as características..." rows="3" class="form-textarea">{{ old('description') }}</textarea>
                    </div>
                              {{-- Preços e Composição --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-calculator" style="margin-right:.5rem;color:var(--accent);"></i>Composição de Preço
                    </h3>
                </div>

                <div class="card" style="background:var(--bg-hover); border:1px dashed var(--border); padding:1.5rem;">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1.25rem;">
                        <div class="form-group">
                            <label class="form-label">Preço de Compra (R$)</label>
                            <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Frete / Encargos (R$)</label>
                            <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Impostos (%)</label>
                            <input type="number" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Margem de Lucro (%)</label>
                            <input type="number" name="margin_percent" id="margin_percent" value="{{ old('margin_percent', 0) }}" step="0.01" min="0" class="form-input price-calc">
                        </div>
                    </div>
                    <div style="margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-weight:600; color:var(--text-secondary);">Preço Final Calculado:</span>
                        <span id="calculated_price_display" style="font-size:1.5rem; font-weight:800; color:var(--accent); font-family:'Outfit';">R$ 0,00</span>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Preço de Venda Final (R$) <span style="color:var(--red);">*</span></label>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}"
                               placeholder="0.00" step="0.01" min="0" required class="form-input">
                        <small style="color:var(--text-muted);">Este é o preço que será usado no sistema.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Especial / Promoção (R$)</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantidade Inicial em Estoque</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}"
                               placeholder="0" step="1" min="0" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nível de Ressuprimento <span style="color:var(--red);">*</span></label>
                        <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}"
                               placeholder="0" step="1" min="0" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estoque Máximo Sugerido</label>
                        <input type="number" name="max_stock" value="{{ old('max_stock', 1) }}"
                               placeholder="1" step="1" min="1" class="form-input">
                    </div>
                </div>
          </div>

                {{-- Dimensões e Peso --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-ruler-combined" style="margin-right:.5rem;color:var(--purple);"></i>Dimensões e Peso
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Peso (kg)</label>
                        <input type="number" name="weight" value="{{ old('weight') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Altura (cm)</label>
                        <input type="number" name="height" value="{{ old('height') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Largura (cm)</label>
                        <input type="number" name="width" value="{{ old('width') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profundidade (cm)</label>
                        <input type="number" name="depth" value="{{ old('depth') }}"
                               placeholder="0.00" step="0.01" min="0" class="form-input">
                    </div>
                </div>

                {{-- Categorização e Localização --}}
                <div style="padding-bottom:.875rem;border-bottom:1px solid var(--border);margin-top:.25rem;">
                    <h3 style="font-size:.95rem;font-weight:600;color:var(--text-secondary);">
                        <i class="fa-solid fa-folder-open" style="margin-right:.5rem;color:var(--orange);"></i>Categorização e Localização
                    </h3>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Categoria</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">-- Selecione uma categoria --</option>
                            @if(isset($categories) && $categories->count())
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Eletrônicos" {{ old('category') == 'Eletrônicos' ? 'selected' : '' }}>Eletrônicos</option>
                                <option value="Informática" {{ old('category') == 'Informática' ? 'selected' : '' }}>Informática</option>
                                <option value="Periféricos" {{ old('category') == 'Periféricos' ? 'selected' : '' }}>Periféricos</option>
                                <option value="Acessórios" {{ old('category') == 'Acessórios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="Software" {{ old('category') == 'Software' ? 'selected' : '' }}>Software</option>
                                <option value="Outros" {{ old('category') == 'Outros' ? 'selected' : '' }}>Outros</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unidade de Medida</label>
                        <select name="unit" id="unit" class="form-select">
                            <option value="un" {{ old('unit') == 'un' ? 'selected' : '' }}>Unidade (un)</option>
                            <option value="caixa" {{ old('unit') == 'caixa' ? 'selected' : '' }}>Caixa</option>
                            <option value="dúzia" {{ old('unit') == 'dúzia' ? 'selected' : '' }}>Dúzia</option>
                            <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Quilograma (kg)</option>
                            <option value="l" {{ old('unit') == 'l' ? 'selected' : '' }}>Litro (l)</option>
                            <option value="m" {{ old('unit') == 'm' ? 'selected' : '' }}>Metro (m)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Endereço no Estoque (WMS)</label>
                        <div style="display:flex; gap:0.5rem;">
                            <input type="hidden" name="warehouse_location_id" id="warehouse_location_id" value="{{ old('warehouse_location_id') }}">
                            <input type="text" name="warehouse_location" id="warehouse_location_display" 
                                   value="{{ old('warehouse_location') }}"
                                   readonly placeholder="Selecione no mapa..." 
                                   class="form-input" style="background:var(--bg-hover); cursor:pointer;">
                            <button type="button" id="btn-open-location-picker" class="btn btn-secondary" style="padding:0 .75rem;">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </button>
                        </div>
                        <small style="color:var(--text-muted);">Clique para selecionar a posição exata.</small>
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Fornecedor Principal</label>
                        <select name="supplier_id" id="supplier_id" class="form-select">
                            <option value="">-- Selecione um fornecedor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Status do Produto</label>
                        <div style="display:flex;gap:1.5rem;align-items:center;">
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }}>
                                Ativo
                            </label>
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }}>
                                Inativo
                            </label>
                            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                                <input type="radio" name="status" value="descontinuado" {{ old('status') == 'descontinuado' ? 'checked' : '' }}>
                                Descontinuado
                            </label>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:.5rem;border-top:1px solid var(--border);margin-top:.5rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Cadastrar Produto
>>>>>>> Stashed changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@include('partials.location_picker')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Price Calculation Logic
        const inputs = document.querySelectorAll('.price-calc');
        const purchaseInput = document.getElementById('purchase_price');
        const shippingInput = document.getElementById('shipping_cost');
        const taxInput = document.getElementById('tax_percent');
        const marginInput = document.getElementById('margin_percent');
        const finalInput = document.getElementById('unit_price');
        const display = document.getElementById('calculated_price_display');

        function calculate() {
            const purchase = parseFloat(purchaseInput.value) || 0;
            const shipping = parseFloat(shippingInput.value) || 0;
            const tax = parseFloat(taxInput.value) || 0;
            const margin = parseFloat(marginInput.value) || 0;

            const baseCost = purchase + shipping;
            const withTax = baseCost * (1 + (tax / 100));
            const finalPrice = withTax * (1 + (margin / 100));

            display.innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(finalPrice);
            
            if (finalPrice > 0) {
                finalInput.value = finalPrice.toFixed(2);
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculate);
        });

        calculate();

        // Location Picker Logic
        const modal = document.getElementById('location-picker-modal');
        const openBtn = document.getElementById('btn-open-location-picker');
        const closeBtn = document.getElementById('close-location-picker');
        const displayInput = document.getElementById('warehouse_location_display');
        const idInput = document.getElementById('warehouse_location_id');
        
        const aisleSelect = document.getElementById('filter-aisle');
        const searchInput = document.getElementById('location-search');
        const searchBtn = document.getElementById('btn-search-locations');
        const resultsGrid = document.getElementById('location-grid');
        const loading = document.getElementById('location-loading');
        const emptyState = document.getElementById('location-empty');

        openBtn.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            fetchLocations(); // Fetch initial suggestions
        });

        displayInput.addEventListener('click', () => openBtn.click());

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        searchBtn.addEventListener('click', fetchLocations);
        aisleSelect.addEventListener('change', fetchLocations);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') fetchLocations();
        });

        function fetchLocations() {
            const aisle = aisleSelect.value;
            const q = searchInput.value;
            
            loading.style.display = 'block';
            emptyState.style.display = 'none';
            resultsGrid.innerHTML = '';

            fetch(`{{ route('locations.search') }}?q=${q}&aisle=${aisle}`)
                .then(res => res.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (data.length === 0) {
                        emptyState.style.display = 'block';
                        return;
                    }

                    data.forEach(loc => {
                        const card = document.createElement('div');
                        card.className = `location-card ${loc.is_occupied ? 'occupied' : ''}`;
                        card.innerHTML = `
                            <span class="location-card-code">${loc.full_code}</span>
                            <span class="location-card-info">
                                <span class="location-status status-free"></span>Disponível
                            </span>
                        `;
                        
                        if (!loc.is_occupied) {
                            card.addEventListener('click', () => {
                                idInput.value = loc.id;
                                displayInput.value = loc.full_code;
                                modal.style.display = 'none';
                                document.body.style.overflow = 'auto';
                            });
                        }

                        resultsGrid.appendChild(card);
                    });
<<<<<<< Updated upstream
                } else if (error.message) {
                    messages.push(error.message);
                } else {
                    messages.push('Erro desconhecido ao processar a requisição');
                }
                
                errorDiv.innerHTML = '<i class="fa-solid fa-exclamation-circle mr-2"></i>' + messages.join('<br>');
                errorDiv.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar';
            }
        });

        // Fechar modal ao clicar fora
        document.getElementById('supplierModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSupplierModal();
            }
        });
    </script>

</body>
</html>
=======
                })
                .catch(err => {
                    console.error(err);
                    loading.style.display = 'none';
                    emptyState.style.display = 'block';
                });
        }
    });
</script>
@endpush
>>>>>>> Stashed changes
