<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fornecedor - LogiSync WMS</title>
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
<body class="bg-[#020617] font-sans transition-colors">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#0F172A] text-white hidden md:flex flex-col border-r border-[#1E293B] shadow-xl">
            <div class="p-6 border-[#1E293B] flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 bg-[#2563EB] rounded-lg text-white">
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
            <!-- Header -->
            <header class="bg-[#0F172A] dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center dark:border-slate-800">
                <h1 class="text-2xl font-bold text-[#FFFFFF]">
                    <i class="fa-solid fa-pencil text-[#2563EB] mr-2"></i>Editar Fornecedor
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

            <!-- Conteúdo -->
            <section class="p-8">
                <div class="max-w-2xl mx-auto">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-900/20 border border-red-600 rounded-lg">
                            <p class="text-red-400 font-semibold mb-2">Erros ao validar:</p>
                            <ul class="text-red-400 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white rounded-lg shadow-md p-8">
                        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Informações Básicas -->
                            <div>
                                 <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 pb-2 border-b border-[#1E293B]">
                                    <i class="fa-solid fa-info-circle mr-2"></i>Informações Básicas
                                </h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">Nome <span class="text-red-600">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">CNPJ</label>
                                    <input type="text" name="cnpj" value="{{ old('cnpj', $supplier->cnpj) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Contato -->
                            <div>
                                 <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 pb-2 border-b border-[#1E293B]">
                                    <i class="fa-solid fa-phone mr-2"></i>Contato
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">E-mail</label>
                                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">Telefone</label>
                                        <input type="tel" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div>
                                 <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 pb-2 border-b border-[#1E293B]">
                                    <i class="fa-solid fa-map-location-dot mr-2"></i>Endereço
                                </h3>
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">Rua/Avenida</label>
                                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">Cidade</label>
                                        <input type="text" name="city" value="{{ old('city', $supplier->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-[#FFFFFF] mb-2 flex items-center gap-2">Estado</label>
                                        <input type="text" name="state" value="{{ old('state', $supplier->state) }}" maxlength="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex gap-4 pt-6">
                                <a href="{{ route('suppliers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold text-center">
                                    Cancelar
                                </a>
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                                    Atualizar
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
