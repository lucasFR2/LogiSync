<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - LogiSync WMS</title>

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
        
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select {
            background-color: #0F172A !important;
            border-color: #1E293B !important;
            color: #FFFFFF !important;
        }
        
        input::placeholder, textarea::placeholder {
            color: #94A3B8 !important;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #2563EB !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }
    </style>
</head>
<body class="bg-[#020617] dark:bg-[#020617] font-sans transition-colors text-[#FFFFFF]">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0F172A] text-white hidden md:flex flex-col border-r border-[#1E293B] shadow-xl">
            <div class="p-6 border-b border-[#1E293B] flex justify-center">
                <a href="/" class="hover:opacity-80 transition-opacity">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-6 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-blue-600 hover:bg-blue-700 rounded-xl text-white transition-all shadow-md">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
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
                        <i class="fa-solid fa-plus-circle text-blue-500 mr-3"></i>Cadastro de Produtos
                    </h1>
                    <p class="text-[#94A3B8] text-sm mt-1">Registre novos produtos no seu inventário</p>
                </div>
                <div class="flex items-center gap-6">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-[#1E293B] transition text-[#94A3B8] hover:text-[#FFFFFF]" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
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

            <!-- Conteúdo da Página -->
            <section class="p-8 bg-[#020617]">
                <!-- Navegação breadcrumb -->
                <div class="mb-8 flex items-center gap-2 text-sm">
                    <a href="{{ route('products.index') }}" class="text-blue-400 hover:text-blue-300 transition">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                    </a>
                    <i class="fa-solid fa-chevron-right text-[#1E293B]"></i>
                    <span class="text-[#94A3B8]">Novo Produto</span>
                </div>

                <!-- Card do Formulário -->
                <div class="card-dark rounded-2xl border overflow-hidden shadow-lg">
                    <!-- Header do Card -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 border-b border-blue-700 flex-shrink-0">
                        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-list"></i>
                            Cadastro de Novo Produto
                        </h2>
                        <p class="text-blue-100 text-sm mt-2">Preencha todos os campos obrigatórios para registrar um novo produto no sistema</p>
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
                        <div class="mb-10 pb-10 border-b border-[#1E293B]">
                            <h3 class="text-lg font-bold text-[#FFFFFF] mb-6 flex items-center gap-3 pb-4">
                                <span class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-info-circle text-blue-400 text-lg"></i>
                                </span>
                                Informações Básicas
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome do Produto -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-box text-blue-400"></i>Nome do Produto <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name"
                                        value="{{ old('name') }}"
                                        placeholder="Ex: Notebook Dell Inspiron 15"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 @error('name') border-red-600/50 bg-red-600/10 @enderror transition-all"
                                        required
                                    >
                                    @error('name')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Código de Barras -->
                                <div>
                                    <label for="barcode" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-qrcode text-purple-400"></i>Código de Barras
                                    </label>
                                    <input 
                                        type="text" 
                                        name="barcode" 
                                        id="barcode"
                                        value="{{ old('barcode') }}"
                                        placeholder="Ex: 1234567890123"
                                        pattern="[0-9]{1,13}"
                                        maxlength="13"
                                        title="Código de barras deve conter apenas números (máx. 13 dígitos)"
                                        inputmode="numeric"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all"
                                    >
                                </div>

                                <!-- Descrição -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-align-left text-orange-400"></i>Descrição Detalhada
                                    </label>
                                    <textarea 
                                        name="description" 
                                        id="description"
                                        placeholder="Descreva as características, especificações técnicas do produto..."
                                        rows="3"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all resize-none"
                                    >{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 2: Preços e Estoque -->
                        <div class="mb-10 pb-10 border-b border-[#1E293B]">
                            <h3 class="text-lg font-bold text-[#FFFFFF] mb-6 flex items-center gap-3 pb-4">
                                <span class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-tag text-green-400 text-lg"></i>
                                </span>
                                Preços e Estoque
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Custo Unitário -->
                                <div>
                                    <label for="cost_price" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-calculator text-indigo-400"></i>Custo Unitário (R$)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all"
                                    >
                                </div>

                                <!-- Preço Unitário -->
                                <div>
                                    <label for="unit_price" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-dollar-sign text-green-400"></i>Preço de Venda (R$) <span class="text-red-500">*</span>
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 @error('unit_price') border-red-600/50 bg-red-600/10 @enderror transition-all"
                                        required
                                    >
                                    @error('unit_price')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Preço Especial -->
                                <div>
                                    <label for="selling_price" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-emerald-400"></i>Preço Especial (R$)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all"
                                    >
                                    <p class="text-xs text-[#94A3B8] mt-1"><i class="fa-solid fa-info-circle"></i> Preço opcional para promoções</p>
                                </div>

                                <!-- Quantidade Atual -->
                                <div>
                                    <label for="quantity" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-boxes text-cyan-400"></i>Quantidade em Estoque <span class="text-red-500">*</span>
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500 @error('quantity') border-red-600/50 bg-red-600/10 @enderror transition-all"
                                        required
                                    >
                                    @error('quantity')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estoque Máximo -->
                                <div>
                                    <label for="max_stock" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-arrow-up text-blue-400"></i>Estoque Máximo
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                                    >
                                </div>

                                <!-- Nível de Ressuprimento -->
                                <div>
                                    <label for="reorder_level" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-[#FFFFFF]"></i>Nível de Ressuprimento <span class="text-red-500">*</span>
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FFFFFF]/50 focus:border-[#FFFFFF] @error('reorder_level') border-red-600/50 bg-red-600/10 @enderror transition-all"
                                        required
                                    >
                                    @error('reorder_level')
                                        <p class="text-red-400 text-xs mt-2 flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i>{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Quantidade por Embalagem -->
                                <div>
                                    <label for="package_quantity" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-cube text-red-400"></i>Quantidade por Embalagem
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500 transition-all"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 3: Dimensões e Peso -->
                        <div class="mb-10 pb-10 border-b border-[#1E293B]">
                            <h3 class="text-lg font-bold text-[#FFFFFF] mb-6 flex items-center gap-3 pb-4">
                                <span class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-ruler-combined text-purple-400 text-lg"></i>
                                </span>
                                Dimensões e Peso
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Peso (kg) -->
                                <div>
                                    <label for="weight" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-weight text-orange-400"></i>Peso (kg)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all"
                                    >
                                </div>

                                <!-- Altura (cm) -->
                                <div>
                                    <label for="height" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-arrow-up text-pink-400"></i>Altura (cm)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500 transition-all"
                                    >
                                </div>

                                <!-- Largura (cm) -->
                                <div>
                                    <label for="width" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-arrow-right text-teal-400"></i>Largura (cm)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/50 focus:border-teal-500 transition-all"
                                    >
                                </div>

                                <!-- Profundidade (cm) -->
                                <div>
                                    <label for="depth" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-arrow-left text-sky-400"></i>Profundidade (cm)
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
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all"
                                    >
                                </div>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- SEÇÃO 4: Categorização e Localização -->
                        <div class="mb-10">
                            <h3 class="text-lg font-bold text-[#FFFFFF] mb-6 flex items-center gap-3 pb-4">
                                <span class="w-10 h-10 bg-orange-600/20 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-orange-400 text-lg"></i>
                                </span>
                                Categorização e Localização
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Categoria -->
                                <div>
                                    <label for="category" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-list text-indigo-400"></i>Categoria
                                    </label>
                                    <select 
                                        name="category" 
                                        id="category"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all cursor-pointer"
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
                                    <label for="unit" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-measure text-violet-400"></i>Unidade de Medida
                                    </label>
                                    <select 
                                        name="unit" 
                                        id="unit"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 transition-all cursor-pointer"
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
                                    <label for="warehouse_location" class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-lime-400"></i>Localização no Armazém
                                    </label>
                                    <input 
                                        type="text" 
                                        name="warehouse_location" 
                                        id="warehouse_location"
                                        value="{{ old('warehouse_location') }}"
                                        placeholder="Ex: Prateleira A-10"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500/50 focus:border-lime-500 transition-all"
                                    >
                                </div>

                                <!-- Fornecedor -->
                                <div class="md:col-span-2 p-6 rounded-lg border border-[#1E293B] bg-blue-600/5">
                                    <div class="flex items-center justify-between mb-4">
                                        <label for="supplier_id" class="block text-base font-bold text-[#FFFFFF] flex items-center gap-3">
                                            <span class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-handshake text-blue-400 text-lg"></i>
                                            </span>Fornecedor Principal
                                        </label>
                                        <button type="button" onclick="openSupplierModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm">
                                            <i class="fa-solid fa-plus-circle"></i> Novo
                                        </button>
                                    </div>
                                    <select 
                                        name="supplier_id" 
                                        id="supplier_id"
                                        class="w-full px-4 py-3 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all cursor-pointer font-medium"
                                    >
                                        <option value="" class="text-[#94A3B8]">-- Selecione um fornecedor --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-[#FFFFFF] mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-toggle-on text-fuchsia-400"></i>Status do Produto
                                    </label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-green-600/50 hover:bg-green-600/10 transition">
                                            <input type="radio" name="status" value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }} class="w-5 h-5 text-green-600 cursor-pointer">
                                            <span class="text-sm font-semibold text-[#FFFFFF] flex items-center gap-2">
                                                <i class="fa-solid fa-circle-check text-green-400"></i>Ativo
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-red-600/50 hover:bg-red-600/10 transition">
                                            <input type="radio" name="status" value="inativo" {{ old('status') == 'inativo' ? 'checked' : '' }} class="w-5 h-5 text-red-600 cursor-pointer">
                                            <span class="text-sm font-semibold text-[#FFFFFF] flex items-center gap-2">
                                                <i class="fa-solid fa-circle-xmark text-red-400"></i>Inativo
                                            </span>
                                        </label>
                                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border-2 border-transparent hover:border-[#FFFFFF]/50 hover:bg-[#FFFFFF]/10 transition">
                                            <input type="radio" name="status" value="descontinuado" {{ old('status') == 'descontinuado' ? 'checked' : '' }} class="w-5 h-5 text-[#FFFFFF] cursor-pointer">
                                            <span class="text-sm font-semibold text-[#FFFFFF] flex items-center gap-2">
                                                <i class="fa-solid fa-circle-pause text-[#FFFFFF]"></i>Descontinuado
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
                            <a href="{{ route('products.index') }}" class="flex-1 bg-[#1E293B] hover:bg-[#2D3A4F] text-[#FFFFFF] px-8 py-4 rounded-lg font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                                <i class="fa-solid fa-xmark text-lg"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para Novo Fornecedor -->
    <div id="supplierModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="card-dark rounded-lg shadow-xl max-w-md w-full">
            <!-- Header do Modal -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center rounded-t-lg">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>Novo Fornecedor
                </h3>
                <button type="button" onclick="closeSupplierModal()" class="text-white hover:text-blue-100 transition">
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Formulário -->
            <form id="supplierForm" class="p-6 space-y-4">
                @csrf
                
                <!-- Nome -->
                <div>
                    <label for="supplier_name" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-store text-blue-400"></i> Nome do Fornecedor *
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="supplier_name"
                        placeholder="Ex: Distribuidora XYZ"
                        required
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="supplier_email" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-envelope text-blue-400"></i> Email
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="supplier_email"
                        placeholder="contato@fornecedor.com"
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                    >
                </div>

                <!-- Telefone -->
                <div>
                    <label for="supplier_phone" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-phone text-blue-400"></i> Telefone
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="supplier_phone"
                        placeholder="(00) 00000-0000"
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                    >
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="supplier_cnpj" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-id-card text-blue-400"></i> CNPJ
                    </label>
                    <input 
                        type="text" 
                        name="cnpj" 
                        id="supplier_cnpj"
                        placeholder="00.000.000/0000-00"
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                    >
                </div>

                <!-- Endereço -->
                <div>
                    <label for="supplier_address" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-map-location-dot text-blue-400"></i> Endereço
                    </label>
                    <input 
                        type="text" 
                        name="address" 
                        id="supplier_address"
                        placeholder="Rua, número, complemento..."
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
                    >
                </div>

                <!-- Cidade -->
                <div>
                    <label for="supplier_city" class="block text-sm font-bold text-[#FFFFFF] mb-2">
                        <i class="fa-solid fa-city text-blue-400"></i> Cidade
                    </label>
                    <input 
                        type="text" 
                        name="city" 
                        id="supplier_city"
                        placeholder="São Paulo"
                        class="w-full px-4 py-2 border-2 border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all"
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
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Abrir modal
        function openSupplierModal() {
            document.getElementById('supplierModal').classList.remove('hidden');
            document.getElementById('supplierForm').reset();
            document.getElementById('supplierError').classList.add('hidden');
            document.getElementById('supplierSuccess').classList.add('hidden');
        }

        // Fechar modal
        function closeSupplierModal() {
            document.getElementById('supplierModal').classList.add('hidden');
            document.getElementById('supplierForm').reset();
        }

        // Enviar formulário via AJAX
        document.getElementById('supplierForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const errorDiv = document.getElementById('supplierError');
            const successDiv = document.getElementById('supplierSuccess');
            const submitBtn = this.querySelector('button[type="submit"]');

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

                const csrfToken = 
                    document.querySelector('input[name="_token"]')?.value ||
                    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    formData.get('_token');
                
                console.log('CSRF Token encontrado:', !!csrfToken);

                const response = await fetch('{{ route("suppliers.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || '',
                    }
                });

                let data;
                const contentType = response.headers.get('content-type');
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error('Resposta não é JSON:', text.substring(0, 500));
                    console.error('Content-Type:', contentType);
                    console.error('Status:', response.status, response.statusText);
                    
                    // Tenta parsear como JSON mesmo assim
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error(`Erro ${response.status}: ${response.statusText}. Resposta inválida.`);
                    }
                }

                if (!response.ok) {
                    throw data;
                }

                // Sucesso
                successDiv.textContent = data.message;
                successDiv.classList.remove('hidden');

                // Atualizar select
                const select = document.getElementById('supplier_id');
                const newOption = document.createElement('option');
                newOption.value = data.supplier.id;
                newOption.textContent = data.supplier.name;
                newOption.selected = true;
                select.appendChild(newOption);

                setTimeout(() => {
                    closeSupplierModal();
                }, 1500);

            } catch (error) {
                console.error('Erro ao enviar:', error);
                let messages = [];
                if (error.errors) {
                    Object.values(error.errors).forEach(msgs => {
                        messages = messages.concat(msgs);
                    });
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
