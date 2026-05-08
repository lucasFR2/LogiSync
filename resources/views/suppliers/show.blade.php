<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $supplier->name }} - LogiSync WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-dark.css') }}">
</head>
<body class="bg-[#020617] font-sans">

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
            <header class="bg-[#0F172A] shadow-sm px-8 py-4 flex justify-between items-center border-b border-[#1E293B]">
                <h1 class="text-2xl font-bold text-[#FFFFFF]">
                    <i class="fa-solid fa-handshake text-[#2563EB] mr-2"></i>{{ $supplier->name }}
                </h1>
                <div class="flex items-center gap-4">
                    <span class="text-[#94A3B8]">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-[#2563EB] rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Conteúdo -->
            <section class="p-8">
                <div class="max-w-2xl mx-auto bg-[#0F172A] rounded-lg shadow-lg p-8 border border-[#1E293B]">
                    <!-- Informações Básicas -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-[#FFFFFF] mb-4 pb-2 border-b border-[#1E293B]">Informações Básicas</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-[#94A3B8] mb-1">Nome</p>
                                <p class="text-[#FFFFFF]">{{ $supplier->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#94A3B8] mb-1">CNPJ</p>
                                <p class="text-[#FFFFFF]">{{ $supplier->cnpj ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Contato</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">E-mail</p>
                                <p class="text-gray-900">{{ $supplier->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Telefone</p>
                                <p class="text-gray-900">{{ $supplier->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b">Endereço</h3>
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-1">Logradouro</p>
                            <p class="text-gray-900 mb-4">{{ $supplier->address ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Cidade</p>
                                <p class="text-gray-900">{{ $supplier->city ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Estado</p>
                                <p class="text-gray-900">{{ $supplier->state ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-4 pt-6">
                        <a href="{{ route('suppliers.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold text-center">
                            Voltar
                        </a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-center">
                            Editar
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>
