<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - LogiSync WMS</title>
    <script>
        tailwindConfig = {
            darkMode: 'class',
        };
    </script>
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
        
        .card-dark:hover {
            background-color: #111927;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-[#020617] dark:bg-[#020617] font-sans transition-colors text-[#FFFFFF]">

    <div class="min-h-screen flex bg-[#020617]">
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
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1E293B] hover:text-[#FFFFFF] rounded-xl transition-colors">
                    <i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 bg-blue-600 hover:bg-blue-700 rounded-xl text-white transition-all shadow-md">
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
                        <i class="fa-solid fa-handshake text-blue-500 mr-3"></i>Fornecedores
                    </h1>
                    <p class="text-[#94A3B8] text-sm mt-1">Gerencie seus fornecedores e parceiros comerciais</p>
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

            <!-- Conteúdo -->
            <section class="p-8 bg-[#020617]">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-600/20 border border-green-600/50 rounded-lg flex items-center gap-3 text-green-400">
                        <i class="fa-solid fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Controles -->
                <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="flex-1 w-full">
                        <div class="relative max-w-md">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-[#94A3B8]"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Pesquisar por nome..." 
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 bg-[#0F172A] border border-[#1E293B] rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 text-[#FFFFFF] placeholder-[#94A3B8] transition-all"
                            >
                        </div>
                    </form>
                    <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class="fa-solid fa-plus"></i> Novo Fornecedor
                    </a>
                </div>

                <!-- Tabela de Fornecedores -->
                <div class="card-dark rounded-2xl border overflow-hidden shadow-lg">
                    @if ($suppliers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-[#1E293B]/50 text-[#94A3B8] text-sm font-semibold uppercase tracking-wider border-b border-[#1E293B]">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Nome</th>
                                        <th class="px-6 py-4 text-left">CNPJ</th>
                                        <th class="px-6 py-4 text-left">E-mail</th>
                                        <th class="px-6 py-4 text-left">Telefone</th>
                                        <th class="px-6 py-4 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $supplier)
                                        <tr class="border-b border-[#1E293B]/50 hover:bg-[#111927] transition-colors">
                                            <td class="px-6 py-4 font-semibold text-[#FFFFFF]">{{ $supplier->name }}</td>
                                            <td class="px-6 py-4 text-[#94A3B8]">{{ $supplier->cnpj ?? '-' }}</td>
                                            <td class="px-6 py-4 text-[#94A3B8]">{{ $supplier->email ?? '-' }}</td>
                                            <td class="px-6 py-4 text-[#94A3B8]">{{ $supplier->phone ?? '-' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex justify-center gap-3">
                                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-400 hover:text-blue-300 p-2 hover:bg-[#1E293B] rounded-lg transition" title="Editar">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline;" onsubmit="return confirm('Tem certeza?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-300 p-2 hover:bg-red-900/20 rounded-lg transition" title="Excluir">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-16 text-center">
                            <div class="mb-6">
                                <i class="fa-solid fa-building text-5xl text-[#334155] opacity-50 mb-4"></i>
                            </div>
                            <h4 class="text-[#FFFFFF] font-semibold text-lg mb-2">Nenhum fornecedor encontrado</h4>
                            <p class="text-[#94A3B8] mb-6">Comece adicionando seus fornecedores e parceiros comerciais ao sistema.</p>
                            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                <i class="fa-solid fa-plus"></i>Adicionar Fornecedor
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Paginação -->
                @if ($suppliers->count() > 0)
                    <div class="mt-8">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>

</body>
</html>
