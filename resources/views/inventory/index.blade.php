<<<<<<< Updated upstream
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
                <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos
                </a>
                <a href="{{ route('inventory.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
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
=======
@extends('layouts.app')

@section('title', 'Controle de Entradas')
@section('page-title', 'Registro de Entradas')
@section('page-subtitle', 'Movimentações de entrada no estoque em tempo real')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    @if(session('success'))
        <div class="alert badge-success" style="padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Quick Stats Grid --}}
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--blue-bg); color: var(--blue);">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
            <div class="stat-label">Total de Registros</div>
            <div class="stat-value">{{ $inventories->total() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--green-bg); color: var(--green);">
                <i class="fa-solid fa-calendar-check"></i>
>>>>>>> Stashed changes
            </div>
            <div class="stat-label">Neste Mês</div>
            <div class="stat-value">
                {{ $inventories->where('created_at', '>=', now()->startOfMonth())->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--orange-bg); color: var(--orange);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="stat-label">Registros Hoje</div>
            <div class="stat-value">
                {{ $inventories->where('created_at', '>=', now()->startOfDay())->count() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--accent-subtle); color: var(--accent);">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div class="stat-label">SKUs Ativos</div>
            <div class="stat-value">{{ $inventories->groupBy('product_id')->count() }}</div>
        </div>
    </div>

<<<<<<< Updated upstream
        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <!-- Header Superior -->
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-truck-ramp-box text-amber-600 mr-2"></i>Registro de Entradas
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
            <section class="p-8 bg-gray-50 dark:bg-slate-950">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total de Entradas -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-semibold">Total de Entradas</p>
                                <p class="text-3xl font-bold mt-2">{{ $inventories->total() }}</p>
                            </div>
                            <i class="fa-solid fa-arrow-up-to-line text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Entradas este mês -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-100 text-sm font-semibold">Este Mês</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->where('created_at', '>=', now()->startOfMonth())->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-calendar text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Entradas hoje -->
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-yellow-100 text-sm font-semibold">Hoje</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->where('created_at', '>=', now()->startOfDay())->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-sun text-4xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Produtos com entrada -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold">Produtos Movimentados</p>
                                <p class="text-3xl font-bold mt-2">
                                    {{ $inventories->groupBy('product_id')->count() }}
                                </p>
                            </div>
                            <i class="fa-solid fa-cubes text-4xl opacity-20"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Entradas -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fa-solid fa-history mr-2 text-blue-600"></i>Histórico de Entradas
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">Últimas movimentações de entrada no estoque</p>
                    </div>

                    @if ($inventories->count() > 0)
                        <table class="w-full">
                            <thead class="border-b border-gray-200">
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Produto</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Observações</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $inventory)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-600 text-sm">
                                            <div class="font-semibold">{{ $inventory->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs">{{ $inventory->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">{{ $inventory->product->name }}</div>
                                            <div class="text-xs text-gray-600">{{ $inventory->product->barcode ?? 'Sem código' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-semibold">
                                                +{{ $inventory->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 text-sm">
                                            {{ $inventory->notes ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('products.show', $inventory->product) }}" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded transition inline-block" title="Ver Detalhes do Produto">
                                                <i class="fa-solid fa-arrow-up-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="px-6 py-12 text-center">
                            <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Nenhuma entrada registrada</p>
                            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-semibold">
                                <i class="fa-solid fa-arrow-left mr-2"></i>Ir para Produtos
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Paginação -->
                @if ($inventories->count() > 0)
                    <div class="mt-6">
                        {{ $inventories->links() }}
                    </div>
                @endif
            </section>
        </main>
=======
    {{-- Main History Card --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Histórico Operacional</h3>
            </div>
            <button id="openRegisterEntryBtn" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Registrar Entrada
            </button>
        </div>

        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($inventories->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th>Produto / SKU</th>
                            <th style="text-align:center;">Quantidade</th>
                            <th>Observações</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                            <tr>
                                <td>
                                    @php $d = $inventory->entry_date ?? $inventory->created_at; @endphp
                                    <div style="font-family:'Outfit'; font-weight:700; color:var(--text-primary);">{{ $d->format('d/m/Y') }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $d->format('H:i') }} hs</div>
                                </td>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $inventory->product->name }}</div>
                                    <div style="font-size:0.75rem; font-family:monospace; color:var(--text-muted);">{{ $inventory->product->barcode ?? 'Sem Código' }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge badge-success" style="font-weight:800; padding:0.4rem 0.8rem;">
                                        +{{ $inventory->quantity }}
                                    </span>
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $inventory->notes ?? '—' }}
                                </td>
                                <td style="text-align:center;">
                                    <a href="{{ route('products.show', $inventory->product) }}" class="icon-btn" title="Ver Produto">
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($inventories->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $inventories->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state" style="padding:6rem 2rem;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-inbox" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhuma entrada registrada</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Comece a movimentar seu estoque registrando a primeira entrada de mercadorias.</p>
                    <button class="btn btn-primary" id="openRegisterEntryBtn2" style="padding:1rem 2rem;">
                        <i class="fa-solid fa-plus"></i> Registrar Primeira Entrada
                    </button>
                </div>
            @endif
        </div>
>>>>>>> Stashed changes
    </div>
</div>

{{-- Modal: Registrar Entrada --}}
@php $products = \App\Models\Product::orderBy('name')->get(); @endphp

<div id="registerEntryModal" class="modal-backdrop" style="display:none;">
    <div class="modal" style="max-width:550px;">
        <div class="modal-header">
            <h3 class="modal-title" style="font-family:'Outfit';">Registrar Entrada</h3>
            <button id="closeRegisterEntryBtn" class="icon-btn" style="border:none; background:transparent;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="registerEntryForm" method="POST" action="">
            @csrf
            <div class="modal-body" style="gap:1.5rem;">
                <div class="form-group">
                    <label class="form-label">Selecione o Produto</label>
                    <select id="entryProductSelect" name="product_id" required class="form-select">
                        <option value="">— Selecionar produto —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-2" style="gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Data e Hora</label>
                        <input id="entryDateInput" type="datetime-local" name="entry_date" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantity" min="1" required class="form-input" placeholder="Ex: 100">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Observações da Movimentação</label>
                    <textarea name="notes" rows="3" class="form-textarea" placeholder="Descreva o motivo ou detalhes da entrada..."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="background:var(--bg-hover);">
                <button type="button" id="cancelRegisterEntry" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                    Confirmar Entrada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const modal        = document.getElementById('registerEntryModal');
    const form         = document.getElementById('registerEntryForm');
    const productSel   = document.getElementById('entryProductSelect');
    const entryDate    = document.getElementById('entryDateInput');

    function openModal() {
        if (entryDate) {
            const now = new Date();
            entryDate.value = new Date(now - now.getTimezoneOffset()*60000).toISOString().slice(0,16);
        }
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
    }

    function closeModal() {
        modal.classList.remove('open');
        setTimeout(() => { modal.style.display = 'none'; form.reset(); form.action = ''; }, 200);
    }

    ['openRegisterEntryBtn','openRegisterEntryBtn2'].forEach(id => {
        const btn = document.getElementById(id);
        btn && btn.addEventListener('click', openModal);
    });
    document.getElementById('closeRegisterEntryBtn')?.addEventListener('click', closeModal);
    document.getElementById('cancelRegisterEntry')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    form?.addEventListener('submit', function(e) {
        const pid = productSel.value;
        if (!pid) { e.preventDefault(); alert('Selecione um produto.'); return; }
        this.action = '/products/' + pid + '/add-inventory';
    });
})();
</script>
@endpush
