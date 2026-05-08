<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto - LogiSync WMS</title>
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
<body class="bg-gray-50 dark:bg-slate-950 font-sans transition-colors">

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 dark:bg-slate-950 text-white hidden md:flex flex-col border-r border-slate-700">
            <div class="p-4 sm:p-6 border-b border-slate-800 dark:border-slate-700 flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert">
                </a>
            </div>
            
            <nav class="flex-1 px-4 mt-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition text-sm">
                    <i class="fa-solid fa-chart-line mr-3 flex-shrink-0"></i> <span class="hidden lg:inline">Dashboard</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center p-3 bg-[#2563EB] rounded-lg text-white text-sm">
                    <i class="fa-solid fa-boxes-stacked mr-3 flex-shrink-0"></i> <span class="hidden lg:inline">Produtos</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition text-sm">
                    <i class="fa-solid fa-truck-ramp-box mr-3 flex-shrink-0"></i> <span class="hidden lg:inline">Entradas</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-[#94A3B8] hover:bg-[#1A2438] hover:text-white rounded-lg transition text-sm">
                    <i class="fa-solid fa-handshake mr-3 flex-shrink-0"></i> <span class="hidden lg:inline">Fornecedores</span>
                </a>
            </nav>

            <div class="p-4 border-t border-[#1E293B]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition text-sm">
                        <i class="fa-solid fa-right-from-bracket mr-3 flex-shrink-0"></i> <span class="hidden lg:inline">Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <!-- Header Superior -->
            <header class="bg-[#0F172A] shadow-lg px-4 sm:px-8 py-3 sm:py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 border-b border-[#1E293B]">
                <h1 class="text-xl sm:text-2xl font-bold text-[#FFFFFF] flex items-center gap-2">
                    <i class="fa-solid fa-box text-[#2563EB]"></i><span class="hidden sm:inline">Detalhes do Produto</span><span class="sm:hidden">Produto</span>
                </h1>
                <div class="flex items-center gap-3 sm:gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-[#1A2438] transition text-[#94A3B8]" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="text-[#94A3B8] text-sm hidden sm:inline">{{ auth()->user()->name }}</span>
                        <div class="w-10 h-10 bg-[#2563EB] rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <section class="p-4 sm:p-8 bg-[#020617]">
                <!-- Barra de Navegação -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm overflow-x-auto">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 whitespace-nowrap">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                    </a>
                    <i class="fa-solid fa-chevron-right text-[#1E293B] flex-shrink-0"></i>
                    <span class="text-[#94A3B8] whitespace-nowrap">{{ $product->name }}</span>
                </div>

                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-900/20 border border-green-600 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-400"></i>
                        <span class="text-green-300">{{ $message }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Informações do Produto -->
                    <div class="lg:col-span-2">
                        <div class="bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
                                <h2 class="text-base sm:text-lg font-bold text-white break-words">{{ $product->name }}</h2>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <a href="{{ route('products.edit', $product) }}" class="flex-1 sm:flex-none bg-white text-blue-600 px-3 sm:px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                        <i class="fa-solid fa-pencil"></i> <span class="hidden sm:inline">Editar</span><span class="sm:hidden">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');" class="w-full sm:w-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                            <i class="fa-solid fa-trash"></i> <span class="hidden sm:inline">Deletar</span><span class="sm:hidden">Del</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Detalhes -->
                            <div class="p-4 sm:p-6">
                                <!-- SEÇÃO 1: Informações Básicas -->
                                <div class="mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-[#1E293B]">
                                    <h3 class="text-base sm:text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-info-circle text-blue-600"></i><span class="hidden sm:inline">Informações Básicas</span><span class="sm:hidden">Básicas</span>
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Código de Barras</label>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $product->barcode ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Categoria</label>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ ucfirst($product->category ?? 'Não informada') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Unidade de Medida</label>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ strtoupper($product->unit ?? 'un') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Fornecedor</label>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $product->supplier?->name ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-bold">Status</label>
                                            <p class="text-xs sm:text-sm mt-1">
                                                @if ($product->status == 'ativo')
                                                    <span class="px-2 py-1 text-green-500 text-xs font-bold"><i class="fa-solid fa-check mr-1"></i>Ativo</span>
                                                @elseif ($product->status == 'inativo')
                                                    <span class="px-2 py-1 text-red-500 text-xs font-bold"><i class="fa-solid fa-ban mr-1"></i>Inativo</span>
                                                @else
                                                    <span class="px-2 py-1 text-[#FFFFFF] text-xs font-bold"><i class="fa-solid fa-pause mr-1"></i>Descontinuado</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4">
                                        <label class="text-xs text-gray-200 font-bold">Descrição</label>
                                        <p class="text-gray-500 text-xs sm:text-sm mt-1">{{ $product->description ?? '—' }}</p>
                                    </div>
                                </div>

                                <!-- SEÇÃO 2: Preços e Estoque -->
                                <div class="mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-[#1E293B]">
                                    <h3 class="text-base sm:text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-green-600"></i><span class="hidden sm:inline">Preços e Estoque</span><span class="sm:hidden">Preços</span>
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Preço de Venda</label>
                                            <p class="text-lg sm:text-2xl text-gray-500 font-bold mt-1">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Custo Unitário</label>
                                            <p class="text-sm sm:text-xl text-gray-500 font-bold mt-1">R$ {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Quantidade</label>
                                            <p class="text-lg sm:text-2xl text-gray-500 font-bold mt-1">{{ $product->quantity }}</p>
                                        </div>
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Valor Total</label>
                                            <p class="text-base sm:text-lg text-gray-500 font-bold mt-1">R$ {{ number_format($product->quantity * $product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4 mt-3 sm:mt-4">
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Nível de Ressuprimento</label>
                                            <p class="text-sm sm:text-xl text-gray-500 font-bold mt-1">{{ $product->reorder_level }}</p>
                                        </div>
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Estoque Máximo</label>
                                            <p class="text-sm sm:text-xl text-gray-500 font-bold mt-1">{{ $product->max_stock ?? '—' }}</p>
                                        </div>
                                        <div class="p-2 sm:p-3">
                                            <label class="text-xs text-gray-200 font-semibold">Qtd. por Embalagem</label>
                                            <p class="text-sm sm:text-xl text-gray-500 font-bold mt-1">{{ $product->package_quantity ?? 1 }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-200 font-semibold">Status de Estoque</label>
                                            <p class="text-xs sm:text-sm mt-1">
                                                @if ($product->quantity <= $product->reorder_level)
                                                    <span class="px-2 py-1 text-red-500 rounded text-xs font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i><span class="hidden sm:inline">Abaixo do limite</span><span class="sm:hidden">Crítico</span></span>
                                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    <span class="px-2 py-1 text-yellow-500 rounded text-xs font-semibold"><i class="fa-solid fa-exclamation mr-1"></i>Atenção</span>
                                                @else
                                                    <span class="px-2 py-1 text-green-500 rounded text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i><span class="hidden sm:inline">Em estoque</span><span class="sm:hidden">OK</span></span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEÇÃO 3: Dimensões e Peso -->
                                @if ($product->weight || $product->height || $product->width || $product->depth)
                                    <div class="mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-[#1E293B]">
                                        <h3 class="text-base sm:text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-combined text-purple-600"></i><span class="hidden sm:inline">Dimensões e Peso</span><span class="sm:hidden">Dimensões</span>
                                        </h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Peso</label>
                                                <p class="text-sm sm:text-lg text-gray-500 font-semibold mt-1">{{ $product->weight ?? '—' }} kg</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Altura</label>
                                                <p class="text-sm sm:text-lg text-gray-500 font-semibold mt-1">{{ $product->height ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Largura</label>
                                                <p class="text-sm sm:text-lg text-gray-500 font-semibold mt-1">{{ $product->width ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-200 font-semibold">Profundidade</label>
                                                <p class="text-sm sm:text-lg text-gray-500 font-semibold mt-1">{{ $product->depth ?? '—' }} cm</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- SEÇÃO 4: Localização no Armazém -->
                                @if ($product->warehouse_location)
                                    <div>
                                        <h3 class="text-base sm:text-lg font-semibold text-[#FFFFFF] mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-warehouse text-orange-600"></i><span class="hidden sm:inline">Localização no Armazém</span><span class="sm:hidden">Localização</span>
                                        </h3>
                                        <div class="bg-gray-800 p-3 sm:p-4 rounded-lg">
                                            <p class="text-sm sm:text-lg text-gray-500 font-bold">{{ $product->warehouse_location }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Right: Histórico de Alterações -->
                    <div>
                        <div class="bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-3 sm:py-4">
                                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-history text-purple-300"></i><span class="hidden sm:inline">Histórico de Alterações</span><span class="sm:hidden">Alterações</span>
                                </h3>
                            </div>
                            <!-- Conteúdo -->
                            <div class="p-4 sm:p-6 space-y-3">
                                @php
                                    $auditLogs = $product->auditLogs()->orderBy('changed_at', 'desc')->limit(20)->get();
                                @endphp
                                
                                @if ($auditLogs->count() > 0)
                                    @foreach ($auditLogs as $log)
                                        @php
                                            $isCreation = $log->action === 'create';
                                            $isUpdate = $log->action === 'update';
                                            $isDelete = $log->action === 'delete';
                                        @endphp
                                        
                                        <div class="p-3 bg-[#1A2438] rounded-lg border {{ $isCreation ? 'border-green-600/50' : ($isDelete ? 'border-red-600/50' : 'border-purple-600/50') }}">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 pt-0.5">
                                                    <div class="flex items-center justify-center h-6 w-6 rounded-full {{ $isCreation ? 'bg-green-600/20' : ($isDelete ? 'bg-red-600/20' : 'bg-purple-600/20') }} border {{ $isCreation ? 'border-green-600' : ($isDelete ? 'border-red-600' : 'border-purple-600') }}">
                                                        <i class="fa-solid {{ $isCreation ? 'fa-plus' : ($isDelete ? 'fa-trash' : 'fa-pencil') }} {{ $isCreation ? 'text-green-400' : ($isDelete ? 'text-red-400' : 'text-purple-400') }} text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <!-- Título do Campo -->
                                                    <p class="font-semibold text-sm text-[#FFFFFF] break-words">
                                                        @if ($isCreation)
                                                            <i class="fa-solid fa-plus-circle text-green-400 mr-2"></i>{{ $log->field_name }}
                                                        @elseif ($isDelete)
                                                            <i class="fa-solid fa-trash text-red-400 mr-2"></i>{{ $log->field_name }}
                                                        @else
                                                            <i class="fa-solid fa-pencil text-purple-400 mr-2"></i>{{ $log->field_name }}
                                                        @endif
                                                    </p>
                                                    
                                                    <!-- Data e Hora -->
                                                    <p class="text-gray-500 text-xs mt-1">{{ $log->changed_at->format('d/m/Y H:i:s') }}</p>
                                                    
                                                    <!-- Valores Antigo e Novo -->
                                                    @if ($isUpdate && $log->old_value !== null && $log->new_value !== null)
                                                        <div class="mt-2 space-y-1 text-xs">
                                                            <p class="text-gray-400">
                                                                <span class="text-red-400 break-words">{{ $log->old_value }}</span> 
                                                                <span class="text-gray-500 mx-1">→</span> 
                                                                <span class="text-green-400 break-words">{{ $log->new_value }}</span>
                                                            </p>
                                                        </div>
                                                    @elseif ($log->old_value !== null)
                                                        <p class="mt-2 text-red-400 text-xs break-words">{{ $log->old_value }}</p>
                                                    @elseif ($log->new_value !== null)
                                                        <p class="mt-2 text-green-400 text-xs break-words">{{ $log->new_value }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <i class="fa-solid fa-inbox text-2xl text-gray-600 mb-2 block"></i>
                                        <p class="text-gray-500 text-xs">Sem alterações</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Entradas - Full Width -->
                <div class="bg-[#0F172A] rounded-lg shadow-lg border border-[#1E293B] overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-3 sm:py-4">
                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-truck-ramp-box text-white"></i><span class="hidden sm:inline">Histórico de Entradas de Estoque</span><span class="sm:hidden">Entradas</span>
                        </h3>
                    </div>

                    <!-- Tabela -->
                    @if ($inventories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b border-[#1E293B] bg-[#1A2438]">
                                    <tr>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-[#94A3B8]">
                                            <i class="fa-solid fa-calendar mr-2"></i>Data e Hora
                                        </th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold text-[#94A3B8]">
                                            <i class="fa-solid fa-boxes-stacked mr-2"></i>Quantidade
                                        </th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-[#94A3B8] hidden sm:table-cell">
                                            <i class="fa-solid fa-note-sticky mr-2"></i>Observações
                                        </th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-[#94A3B8] hidden lg:table-cell">
                                            <i class="fa-solid fa-user mr-2"></i>Usuário
                                        </th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold text-[#94A3B8] hidden md:table-cell">
                                            <i class="fa-solid fa-tag mr-2"></i>Lote
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr class="border-b border-[#1E293B] hover:bg-[#1A2438] transition">
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-300 text-xs sm:text-sm whitespace-nowrap">
                                                <i class="fa-solid fa-clock text-purple-400 mr-2"></i>
                                                {{ $inventory->created_at->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                                <span class="px-3 py-1 bg-green-600/20 text-green-300 rounded-lg text-xs sm:text-sm font-semibold inline-block border border-green-600/50">
                                                    <i class="fa-solid fa-arrow-up mr-1"></i>+{{ $inventory->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-400 text-xs sm:text-sm hidden sm:table-cell max-w-xs truncate" title="{{ $inventory->notes ?? '—' }}">
                                                @if ($inventory->notes)
                                                    <i class="fa-solid fa-comment mr-2 text-blue-400"></i>{{ $inventory->notes }}
                                                @else
                                                    <span class="text-gray-600">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-400 text-xs sm:text-sm hidden lg:table-cell">
                                                @if ($inventory->user)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                            {{ substr($inventory->user->name, 0, 1) }}
                                                        </div>
                                                        <span>{{ $inventory->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-600">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-400 text-xs sm:text-sm hidden md:table-cell">
                                                @if ($inventory->lot_number)
                                                    <span class="px-2 py-1 bg-gray-700/50 rounded text-xs">
                                                        {{ $inventory->lot_number }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-600">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="px-4 sm:px-6 py-4 border-t border-[#1E293B] bg-[#1A2438]">
                            {{ $inventories->links() }}
                        </div>
                    @else
                        <div class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                            <i class="fa-solid fa-inbox text-4xl sm:text-5xl text-gray-600 mb-4 block"></i>
                            <p class="text-gray-500 text-sm sm:text-base">Nenhuma entrada registrada para este produto</p>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </div>

</body>
</html>
