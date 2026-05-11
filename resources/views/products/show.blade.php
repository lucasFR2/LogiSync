<<<<<<< Updated upstream
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
                    <i class="fa-solid fa-box text-blue-600 mr-2"></i>Detalhes do Produto
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400" title="Alternar tema">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
=======
@extends('layouts.app')

@section('title', 'Detalhes: ' . $product->name)
@section('page-title', $product->name)
@section('page-subtitle', 'Detalhes do produto')

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    <!-- Breadcrumb -->
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--text-muted);">
        <a href="{{ route('products.index') }}" style="color:var(--accent);text-decoration:none;"><i class="fa-solid fa-boxes-stacked"></i> Produtos</a>
        <i class="fa-solid fa-chevron-right" style="font-size:.65rem;"></i>
        <span>{{ $product->name }}</span>
    </div>

    @if ($message = session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            {{ $message }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">
        <!-- Produto Info -->
        <div class="card anim-fade-up">
            <div class="card-header">
                <span class="card-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <i class="fa-solid fa-box"></i> {{ $product->name }}
                </span>
                <div style="display:flex;gap:.5rem;">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-pencil"></i> Editar
                    </a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary btn-sm" style="background-color:var(--red);border-color:var(--red);">
                            <i class="fa-solid fa-trash"></i> Deletar
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body" style="display:flex;flex-direction:column;gap:1.5rem;">
                
                {{-- Informações Básicas --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-info-circle"></i> Informações Básicas
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Código de Barras</div>
                            <div style="font-family:monospace;font-size:.875rem;">{{ $product->barcode ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Categoria</div>
                            <div style="font-size:.875rem;">{{ ucfirst($product->category ?? 'Não informada') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Unidade de Medida</div>
                            <div style="font-size:.875rem;">{{ strtoupper($product->unit ?? 'un') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Fornecedor</div>
                            <div style="font-size:.875rem;">{{ $product->supplier?->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Status</div>
                            <div style="font-size:.875rem;">
                                @if ($product->status == 'ativo')
                                    <span class="badge badge-green">Ativo</span>
                                @elseif ($product->status == 'inativo')
                                    <span class="badge badge-red">Inativo</span>
                                @else
                                    <span class="badge" style="background:var(--bg-hover);color:var(--text-primary);">Descontinuado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Descrição</div>
                        <div style="font-size:.875rem;color:var(--text-secondary);">{{ $product->description ?? '—' }}</div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border);"></div>

                {{-- Preços e Estoque --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-tag"></i> Preços e Estoque
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Preço de Venda</div>
                            <div style="font-size:1.1rem;font-weight:700;">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Custo Unitário</div>
                            <div style="font-size:1rem;font-weight:600;color:var(--text-secondary);">R$ {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Quantidade</div>
                            <div style="font-size:1.1rem;font-weight:700;">{{ $product->quantity }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Valor Total</div>
                            <div style="font-size:1rem;font-weight:600;color:var(--text-secondary);">R$ {{ number_format($product->quantity * $product->unit_price, 2, ',', '.') }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Nível de Ressuprimento</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->reorder_level }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Estoque Máximo</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->max_stock ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Qtd. por Embalagem</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->package_quantity ?? 1 }}</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Status de Estoque</div>
                            <div style="font-size:.875rem;">
                                @if ($product->quantity <= $product->reorder_level)
                                    <span class="badge badge-red">Abaixo do limite</span>
                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                    <span class="badge badge-orange">Atenção</span>
                                @else
                                    <span class="badge badge-green">Em estoque</span>
                                @endif
                            </div>
                        </div>
>>>>>>> Stashed changes
                    </div>
                </div>

<<<<<<< Updated upstream
            <!-- Conteúdo da Página -->
            <section class="p-8">
                <!-- Barra de Navegação -->
                <div class="mb-6 flex items-center gap-2 text-sm">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-boxes-stacked mr-2"></i>Produtos
                    </a>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                    <span class="text-gray-600">{{ $product->name }}</span>
                </div>

                <!-- Mensagens de Sucesso/Erro -->
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-600"></i>
                        <span class="text-green-700">{{ $message }}</span>
=======
                @if ($product->weight || $product->height || $product->width || $product->depth)
                <div style="border-top:1px solid var(--border);"></div>

                {{-- Dimensões e Peso --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-ruler-combined"></i> Dimensões e Peso
>>>>>>> Stashed changes
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(120px, 1fr));gap:1rem;">
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Peso</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->weight ?? '—' }} kg</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Altura</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->height ?? '—' }} cm</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Largura</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->width ?? '—' }} cm</div>
                        </div>
                        <div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.2rem;font-weight:600;">Profundidade</div>
                            <div style="font-size:.875rem;font-weight:600;color:var(--text-secondary);">{{ $product->depth ?? '—' }} cm</div>
                        </div>
                    </div>
                </div>
                @endif

<<<<<<< Updated upstream
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informações do Produto -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                                <h2 class="text-lg font-bold text-white">{{ $product->name }}</h2>
                                <div class="flex gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center gap-2">
                                        <i class="fa-solid fa-pencil"></i> Editar
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar este produto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                                            <i class="fa-solid fa-trash"></i> Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Detalhes -->
                            <div class="p-6">
                                <!-- SEÇÃO 1: Informações Básicas -->
                                <div class="mb-8 pb-8 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-info-circle text-blue-600"></i>Informações Básicas
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Código de Barras</label>
                                            <p class="text-sm text-gray-700">{{ $product->barcode ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Categoria</label>
                                            <p class="text-sm text-gray-700">{{ ucfirst($product->category ?? 'Não informada') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Unidade de Medida</label>
                                            <p class="text-sm text-gray-700">{{ strtoupper($product->unit ?? 'un') }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Fornecedor</label>
                                            <p class="text-sm text-gray-700">{{ $product->supplier?->name ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Status</label>
                                            <p class="text-sm">
                                                @if ($product->status == 'ativo')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i>Ativo</span>
                                                @elseif ($product->status == 'inativo')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold"><i class="fa-solid fa-ban mr-1"></i>Inativo</span>
                                                @else
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold"><i class="fa-solid fa-pause mr-1"></i>Descontinuado</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4">
                                        <label class="text-xs text-gray-500 font-semibold">Descrição</label>
                                        <p class="text-gray-700 mt-1">{{ $product->description ?? '—' }}</p>
                                    </div>
                                </div>

                                <!-- SEÇÃO 2: Preços e Estoque -->
                                <div class="mb-8 pb-8 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <i class="fa-solid fa-tag text-green-600"></i>Preços e Estoque
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="bg-blue-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Preço de Venda</label>
                                            <p class="text-2xl text-blue-600 font-bold">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="bg-purple-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Custo Unitário</label>
                                            <p class="text-xl text-purple-600 font-bold">R$ {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}</p>
                                        </div>
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Quantidade em Estoque</label>
                                            <p class="text-2xl text-green-600 font-bold">{{ $product->quantity }}</p>
                                        </div>
                                        <div class="bg-orange-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Valor Total</label>
                                            <p class="text-lg text-orange-600 font-bold">R$ {{ number_format($product->quantity * $product->unit_price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div class="bg-yellow-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Nível de Ressuprimento</label>
                                            <p class="text-xl text-yellow-700 font-bold">{{ $product->reorder_level }}</p>
                                        </div>
                                        <div class="bg-indigo-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Estoque Máximo</label>
                                            <p class="text-xl text-indigo-700 font-bold">{{ $product->max_stock ?? '—' }}</p>
                                        </div>
                                        <div class="bg-cyan-50 p-3 rounded-lg">
                                            <label class="text-xs text-gray-500 font-semibold">Qtd. por Embalagem</label>
                                            <p class="text-xl text-cyan-700 font-bold">{{ $product->package_quantity ?? 1 }}</p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-semibold">Status de Estoque</label>
                                            <p class="text-sm mt-1">
                                                @if ($product->quantity <= $product->reorder_level)
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Abaixo do limite</span>
                                                @elseif ($product->quantity <= ($product->reorder_level * 1.5))
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold"><i class="fa-solid fa-exclamation mr-1"></i>Atenção</span>
                                                @else
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i>Em estoque</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEÇÃO 3: Dimensões e Peso -->
                                @if ($product->weight || $product->height || $product->width || $product->depth)
                                    <div class="mb-8 pb-8 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-combined text-purple-600"></i>Dimensões e Peso
                                        </h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-xs text-gray-500 font-semibold">Peso</label>
                                                <p class="text-lg text-gray-900 font-semibold">{{ $product->weight ?? '—' }} kg</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 font-semibold">Altura</label>
                                                <p class="text-lg text-gray-900 font-semibold">{{ $product->height ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 font-semibold">Largura</label>
                                                <p class="text-lg text-gray-900 font-semibold">{{ $product->width ?? '—' }} cm</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 font-semibold">Profundidade</label>
                                                <p class="text-lg text-gray-900 font-semibold">{{ $product->depth ?? '—' }} cm</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- SEÇÃO 4: Localização no Armazém -->
                                @if ($product->warehouse_location)
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-warehouse text-orange-600"></i>Localização no Armazém
                                        </h3>
                                        <div class="bg-orange-50 p-4 rounded-lg">
                                            <p class="text-lg text-orange-900 font-bold">{{ $product->warehouse_location }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Entrada (Controle de Entradas) -->
                    <div>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-8">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                                <h2 class="text-lg font-bold text-white">
                                    <i class="fa-solid fa-arrow-up-to-line mr-2"></i>Registrar Entrada
                                </h2>
                            </div>

                            <!-- Formulário -->
                            <form method="POST" action="{{ route('products.add-inventory', $product) }}" class="p-6 space-y-4">
                                @csrf

                                <!-- Quantidade -->
                                <div>
                                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Quantidade <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        id="quantity"
                                        placeholder="Digite a quantidade"
                                        min="1"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required
                                    >
                                </div>

                                <!-- Notas -->
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Observações (Opcional)
                                    </label>
                                    <textarea 
                                        name="notes" 
                                        id="notes"
                                        placeholder="Ex: Lote 123, Fornecedor ABC..."
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    ></textarea>
                                </div>

                                <!-- Botão -->
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-arrow-up-to-line"></i> Confirmar Entrada
                                </button>
                            </form>

                            <!-- Info Box -->
                            <div class="px-6 py-4 bg-blue-50 border-t border-gray-200">
                                <p class="text-sm text-blue-700">
                                    <i class="fa-solid fa-info-circle mr-2"></i>
                                    <strong>Quantidade atual:</strong> {{ $product->quantity }} unidades
                                </p>
                            </div>
                        </div>
=======
                @if ($product->warehouse_location)
                <div style="border-top:1px solid var(--border);"></div>

                {{-- Localização --}}
                <div>
                    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.875rem;">
                        <i class="fa-solid fa-warehouse"></i> Localização no Armazém
                    </div>
                    <div style="background:var(--bg-hover);padding:.75rem 1rem;border-radius:var(--r-md);font-weight:600;color:var(--text-primary);display:inline-block;">
                        {{ $product->warehouse_location }}
>>>>>>> Stashed changes
                    </div>
                </div>
                @endif
            </div>
        </div>

<<<<<<< Updated upstream
                <!-- Histórico de Entradas -->
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fa-solid fa-history mr-2 text-blue-600"></i>Histórico de Entradas
                            </h3>
                        </div>

                        <!-- Tabela -->
                        @if ($inventories->count() > 0)
                            <table class="w-full">
                                <thead class="border-b border-gray-200">
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data</th>
                                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Quantidade</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventories as $inventory)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $inventory->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-semibold">
                                                    +{{ $inventory->quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $inventory->notes ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Paginação -->
                            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                                {{ $inventories->links() }}
                            </div>
                        @else
                            <div class="px-6 py-12 text-center">
                                <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">Nenhuma entrada registrada para este produto</p>
                            </div>
                        @endif
                    </div>
=======
        <!-- Sidebar Right: Histórico -->
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <div class="card anim-fade-up" style="animation-delay:0.1s;">
                <div class="card-header">
                    <span class="card-title" style="font-size:.9rem;"><i class="fa-solid fa-history"></i> Histórico</span>
                </div>
                <div class="card-body" style="padding:1rem;">
                    @php
                        $auditLogs = method_exists($product, 'auditLogs') ? $product->auditLogs()->orderBy('changed_at', 'desc')->limit(10)->get() : collect();
                    @endphp

                    @if ($auditLogs->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:1rem;">
                            @foreach ($auditLogs as $log)
                                <div style="display:flex;gap:.75rem;">
                                    <div style="margin-top:.15rem;color:var(--accent);font-size:.8rem;">
                                        <i class="fa-solid fa-circle-dot"></i>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:.8125rem;font-weight:600;">{{ $log->field_name }}</div>
                                        <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">{{ $log->changed_at->format('d/m/Y H:i') }}</div>
                                        @if ($log->old_value !== null && $log->new_value !== null)
                                            <div style="font-size:.75rem;background:var(--bg-body);padding:.25rem .5rem;border-radius:var(--r-sm);border:1px solid var(--border);">
                                                <span style="color:var(--red);text-decoration:line-through;">{{ Str::limit($log->old_value, 20) }}</span>
                                                <i class="fa-solid fa-arrow-right" style="margin:0 .25rem;color:var(--text-muted);font-size:.6rem;"></i>
                                                <span style="color:var(--green);">{{ Str::limit($log->new_value, 20) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align:center;padding:1rem 0;">
                            <div style="color:var(--text-muted);font-size:1.5rem;margin-bottom:.5rem;"><i class="fa-solid fa-clock"></i></div>
                            <div style="font-size:.8rem;color:var(--text-secondary);">Sem alterações registradas</div>
                        </div>
                    @endif
>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Entradas -->
    <div class="card anim-fade-up" style="animation-delay:0.2s;">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-truck-ramp-box"></i> Histórico de Entradas
            </span>
        </div>
        <div class="table-wrap">
            @if ($inventories && $inventories->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th style="text-align:center;">Quantidade</th>
                            <th>Observações</th>
                            <th>Usuário</th>
                            <th>Lote</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.85rem;">{{ $inventory->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge badge-green">+{{ $inventory->quantity }}</span>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);">{{ Str::limit($inventory->notes, 40) ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);">{{ $inventory->user->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div style="font-size:.85rem;color:var(--text-secondary);font-family:monospace;">{{ $inventory->lot_number ?? '—' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(method_exists($inventories, 'links'))
                <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);">
                    <div class="pagination-wrap" style="margin-top:0;">
                        {{ $inventories->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa-solid fa-inbox"></i></div>
                    <h4>Nenhuma entrada registrada</h4>
                    <p>As movimentações de entrada para este produto aparecerão aqui.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
