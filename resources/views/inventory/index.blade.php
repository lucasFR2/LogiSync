<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Entradas - LogiSync WMS</title>
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
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 bg-blue-600 hover:bg-blue-700 rounded-xl text-white transition-all shadow-md">
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
                        <i class="fa-solid fa-truck-ramp-box text-blue-500 mr-3"></i>Registro de Entradas
                    </h1>
                    <p class="text-[#94A3B8] text-sm mt-1">Gerenciar movimentações de entrada no estoque</p>
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
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total de Entradas -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-semibold">Total de Entradas</p>
                                <p class="text-4xl font-bold mt-3">{{ $inventories->total() }}</p>
                            </div>
                            <div class="text-4xl opacity-30">
                                <i class="fa-solid fa-arrow-up-to-line"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Entradas este mês -->
                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-100 text-sm font-semibold">Este Mês</p>
                                <p class="text-4xl font-bold mt-3">
                                    {{ $inventories->where('created_at', '>=', now()->startOfMonth())->count() }}
                                </p>
                            </div>
                            <div class="text-4xl opacity-30">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Entradas hoje -->
                    <div class="bg-gradient-to-br from-[#2563EB] to-[#1E40AF] rounded-2xl shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[#FFFFFF] text-sm font-semibold">Hoje</p>
                                <p class="text-4xl font-bold mt-3">
                                    {{ $inventories->where('created_at', '>=', now()->startOfDay())->count() }}
                                </p>
                            </div>
                            <div class="text-4xl opacity-30">
                                <i class="fa-solid fa-sun"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Produtos com entrada -->
                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold">Produtos Movimentados</p>
                                <p class="text-4xl font-bold mt-3">
                                    {{ $inventories->groupBy('product_id')->count() }}
                                </p>
                            </div>
                            <div class="text-4xl opacity-30">
                                <i class="fa-solid fa-cubes"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Entradas -->
                <div class="card-dark rounded-2xl border overflow-hidden shadow-lg">
                    <!-- Header -->
                    <div class="p-6 border-b border-[#1E293B] flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-[#FFFFFF] flex items-center gap-2">
                                <i class="fa-solid fa-history text-blue-400"></i>
                                Histórico de Entradas
                            </h3>
                            <p class="text-[#94A3B8] text-sm mt-2">Últimas movimentações de entrada no estoque</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button id="openRegisterEntryBtn" type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                <i class="fa-solid fa-plus"></i>
                                Registrar Entrada
                            </button>
                        </div>
                    </div>

                    @if ($inventories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-[#1E293B]/50 text-[#94A3B8] text-sm font-semibold uppercase tracking-wider border-b border-[#1E293B]">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Data/Hora</th>
                                        <th class="px-6 py-4 text-left">Produto</th>
                                        <th class="px-6 py-4 text-center">Quantidade</th>
                                        <th class="px-6 py-4 text-left">Observações</th>
                                        <th class="px-6 py-4 text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr class="border-b border-[#1E293B]/50 hover:bg-[#111927] transition-colors">
                                            <td class="px-6 py-4">
                                                    @if($inventory->entry_date)
                                                        <div class="font-semibold text-[#FFFFFF]">{{ $inventory->entry_date->format('d/m/Y') }}</div>
                                                        <div class="text-xs text-[#94A3B8]">{{ $inventory->entry_date->format('H:i') }}</div>
                                                    @else
                                                        <div class="font-semibold text-[#FFFFFF]">{{ $inventory->created_at->format('d/m/Y') }}</div>
                                                        <div class="text-xs text-[#94A3B8]">{{ $inventory->created_at->format('H:i') }}</div>
                                                    @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-[#FFFFFF]">{{ $inventory->product->name }}</div>
                                                <div class="text-xs text-[#94A3B8]">{{ $inventory->product->barcode ?? 'Sem código' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-600/20 text-green-400 border border-green-600/50 rounded-lg text-sm font-semibold">
                                                    +{{ $inventory->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-[#94A3B8] text-sm">
                                                {{ $inventory->notes ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('products.show', $inventory->product) }}" class="text-blue-400 hover:text-blue-300 p-2 hover:bg-[#1E293B] rounded-lg transition inline-block" title="Ver Detalhes do Produto">
                                                    <i class="fa-solid fa-arrow-up-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-16 text-center">
                            <div class="mb-6">
                                <i class="fa-solid fa-inbox text-5xl text-[#334155] opacity-50 mb-4"></i>
                            </div>
                            <h4 class="text-[#FFFFFF] font-semibold text-lg mb-2">Nenhuma entrada registrada</h4>
                            <p class="text-[#94A3B8] mb-6">Comece adicionando produtos ao seu estoque através da página de produtos.</p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg">
                                <i class="fa-solid fa-arrow-left"></i>Ir para Produtos
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Modal: Registrar Entrada -->
                @php
                    $products = \App\Models\Product::orderBy('name')->get();
                @endphp
                <div id="registerEntryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="w-full max-w-xl bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-bold text-white">Registrar Nova Entrada</h4>
                            <button id="closeRegisterEntryBtn" class="text-[#94A3B8] hover:text-white">✕</button>
                        </div>

                        <form id="registerEntryForm" method="POST" action="" class="space-y-4">
                            @csrf
                            <div>
                                <label class="text-sm text-[#94A3B8]">Produto</label>
                                <select id="entryProductSelect" name="product_id" required class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md">
                                    <option value="">-- Selecionar produto --</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode ?? '—' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-sm text-[#94A3B8]">Data/Hora</label>
                                <input id="entryDateInput" type="datetime-local" name="entry_date" required class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md">
                            </div>

                            <div>
                                <label class="text-sm text-[#94A3B8]">Quantidade</label>
                                <input type="number" name="quantity" min="1" required class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md" placeholder="Ex: 10">
                            </div>

                            <div>
                                <label class="text-sm text-[#94A3B8]">Observações</label>
                                <textarea name="notes" rows="3" class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md" placeholder="Descrição da entrada (opcional)"></textarea>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" id="cancelRegisterEntry" class="px-4 py-2 rounded bg-[#334155] text-white">Cancelar</button>
                                <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold">Registrar Entrada</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Paginação -->
                @if ($inventories->count() > 0)
                    <div class="mt-8">
                        {{ $inventories->links() }}
                    </div>
                @endif
            
                <script>
                    (function () {
                        const openBtn = document.getElementById('openRegisterEntryBtn');
                        const modal = document.getElementById('registerEntryModal');
                        const closeBtn = document.getElementById('closeRegisterEntryBtn');
                        const cancelBtn = document.getElementById('cancelRegisterEntry');
                        const form = document.getElementById('registerEntryForm');
                        const productSelect = document.getElementById('entryProductSelect');
                        const entryDateInput = document.getElementById('entryDateInput');

                        function openModal() {
                            // set default date/time to now (local) in datetime-local format
                            if (entryDateInput) {
                                const now = new Date();
                                const tzOffset = now.getTimezoneOffset() * 60000;
                                const localISOTime = new Date(now - tzOffset).toISOString().slice(0,16);
                                entryDateInput.value = localISOTime;
                            }
                            modal.classList.remove('hidden');
                        }

                        function closeModal() {
                            modal.classList.add('hidden');
                            form.reset();
                            form.action = '';
                        }

                        openBtn && openBtn.addEventListener('click', () => openModal());
                        closeBtn && closeBtn.addEventListener('click', () => closeModal());
                        cancelBtn && cancelBtn.addEventListener('click', () => closeModal());

                        // Atualiza a action do form antes do submit para usar a rota: /products/{id}/add-inventory
                        form && form.addEventListener('submit', function (e) {
                            const pid = productSelect.value;
                            if (!pid) {
                                e.preventDefault();
                                alert('Selecione um produto.');
                                return;
                            }
                            if (entryDateInput && !entryDateInput.value) {
                                e.preventDefault();
                                alert('Selecione a data/hora da entrada.');
                                return;
                            }
                            this.action = '/products/' + pid + '/add-inventory';
                        });
                    })();
                </script>
            </section>
        </main>
    </div>

</body>
</html>
