<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Fornecedor - LogiSync WMS</title>
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
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
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
                    <i class="fa-solid fa-handshake text-blue-600 mr-2"></i>Novo Fornecedor
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
                <div class="max-w-2xl mx-auto">
                    <!-- Mensagens de Erro -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-600 font-semibold mb-2">
                                <i class="fa-solid fa-exclamation-circle mr-2"></i>Erros ao validar:
                            </p>
                            <ul class="text-red-600 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white rounded-lg shadow-md p-8">
                        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Informações Básicas -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                    <i class="fa-solid fa-info-circle mr-2"></i>Informações Básicas
                                </h3>

                                <!-- Nome -->
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nome <span class="text-red-600">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name"
                                        value="{{ old('name') }}"
                                        placeholder="Nome da empresa"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>

                                <!-- CNPJ -->
                                <div class="mb-4">
                                    <label for="cnpj" class="block text-sm font-semibold text-gray-700 mb-2">CNPJ</label>
                                    <input 
                                        type="text" 
                                        name="cnpj" 
                                        id="cnpj"
                                        value="{{ old('cnpj') }}"
                                        placeholder="00.000.000/0000-00"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                            </div>

                            <!-- Contato -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                    <i class="fa-solid fa-phone mr-2"></i>Contato
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            id="email"
                                            value="{{ old('email') }}"
                                            placeholder="email@empresa.com"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>

                                    <!-- Telefone -->
                                    <div>
                                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                                        <input 
                                            type="tel" 
                                            name="phone" 
                                            id="phone"
                                            value="{{ old('phone') }}"
                                            placeholder="(11) 99999-9999"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                                    <i class="fa-solid fa-map-location-dot mr-2"></i>Endereço
                                </h3>

                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Rua/Avenida</label>
                                    <input 
                                        type="text" 
                                        name="address" 
                                        id="address"
                                        value="{{ old('address') }}"
                                        placeholder="Rua/Avenida"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Cidade -->
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">Cidade</label>
                                        <input 
                                            type="text" 
                                            name="city" 
                                            id="city"
                                            value="{{ old('city') }}"
                                            placeholder="São Paulo"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>

                                    <!-- Estado -->
                                    <div>
                                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">Estado (UF)</label>
                                        <input 
                                            type="text" 
                                            name="state" 
                                            id="state"
                                            value="{{ old('state') }}"
                                            placeholder="SP"
                                            maxlength="2"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex gap-4 pt-6 border-t border-gray-200">
                                <a href="{{ route('suppliers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold text-center transition">
                                    Cancelar
                                </a>
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                                    Salvar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
