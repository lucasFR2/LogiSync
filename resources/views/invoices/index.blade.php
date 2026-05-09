<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas Fiscais - LogiSync WMS</title>
    <script>
        tailwindConfig = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-50 dark:bg-slate-950 font-sans transition-colors duration-300">

    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-900 dark:bg-slate-950 text-white hidden md:flex flex-col">
            <div class="p-6 border-b border-slate-800 dark:border-slate-700 flex justify-center">
                <a href="/"><img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync Logo" class="w-40 h-auto brightness-0 invert"></a>
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
                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-handshake mr-3"></i> Fornecedores
                </a>
                <a href="{{ route('invoices.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white">
                    <i class="fa-solid fa-file-invoice mr-3"></i> Notas Fiscais
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

        {{-- Conteúdo Principal --}}
        <main class="flex-1">
            {{-- Header --}}
            <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    <i class="fa-solid fa-file-invoice text-blue-600 mr-2"></i>Notas Fiscais
                </h1>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <section class="p-8 bg-gray-50 dark:bg-slate-950">

                {{-- Alertas --}}
                @if ($message = session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-900 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400"></i>
                        <span class="text-green-700 dark:text-green-300">{{ $message }}</span>
                    </div>
                @endif
                @if ($message = session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-900 rounded-lg flex items-center gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-600 dark:text-red-400"></i>
                        <span class="text-red-700 dark:text-red-300">{{ $message }}</span>
                    </div>
                @endif

                {{-- Filtros e botão Novo --}}
                <div class="mb-6 flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                    <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap gap-3 flex-1">
                        <div class="relative flex-1 min-w-48">
                            <input type="text" name="search" placeholder="Buscar por número ou destinatário..."
                                value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 pl-10 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <select name="status" class="px-4 py-2.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos os status</option>
                            <option value="rascunho"  {{ request('status') === 'rascunho'  ? 'selected' : '' }}>Rascunho</option>
                            <option value="emitida"   {{ request('status') === 'emitida'   ? 'selected' : '' }}>Emitida</option>
                            <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        <select name="type" class="px-4 py-2.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos os tipos</option>
                            <option value="saida"   {{ request('type') === 'saida'   ? 'selected' : '' }}>Saída</option>
                            <option value="entrada" {{ request('type') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                        </select>
                        <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg text-sm transition">
                            <i class="fa-solid fa-filter mr-1"></i> Filtrar
                        </button>
                        @if(request()->hasAny(['search','status','type']))
                            <a href="{{ route('invoices.index') }}" class="bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 px-4 py-2.5 rounded-lg text-sm transition">
                                <i class="fa-solid fa-xmark mr-1"></i> Limpar
                            </a>
                        @endif
                    </form>
                    <a href="{{ route('invoices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold flex items-center gap-2 transition whitespace-nowrap">
                        <i class="fa-solid fa-plus"></i> Nova Nota Fiscal
                    </a>
                </div>

                {{-- Tabela --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden">
                    @if($invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Número</th>
                                    <th class="px-6 py-4 font-semibold">Tipo</th>
                                    <th class="px-6 py-4 font-semibold">Destinatário / Remetente</th>
                                    <th class="px-6 py-4 font-semibold">Emissão</th>
                                    <th class="px-6 py-4 font-semibold text-right">Total</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                @php
                                    $statusColors = [
                                        'rascunho'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'emitida'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $typeColors = [
                                        'saida'   => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                        'entrada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    ];
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-mono font-semibold text-blue-600 dark:text-blue-400 text-sm">{{ $invoice->number }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-500">Série {{ $invoice->series }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $typeColors[$invoice->type] ?? '' }}">
                                            {{ $invoice->type === 'saida' ? '↑ Saída' : '↓ Entrada' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $invoice->recipient_name }}</div>
                                        @if($invoice->recipient_document)
                                            <div class="text-xs text-gray-500 dark:text-gray-500">{{ $invoice->recipient_document }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">
                                        {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        R$ {{ number_format($invoice->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$invoice->status] ?? '' }}">
                                            {{ $invoice->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-1">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Visualizar">
                                                <i class="fa-solid fa-eye text-sm"></i>
                                            </a>
                                            @if($invoice->status === 'emitida')
                                                <a href="{{ route('invoices.pdf', $invoice) }}" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition" title="Baixar PDF" target="_blank">
                                                    <i class="fa-solid fa-file-pdf text-sm"></i>
                                                </a>
                                            @endif
                                            @if($invoice->status === 'rascunho')
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition" title="Editar">
                                                    <i class="fa-solid fa-pencil text-sm"></i>
                                                </a>
                                                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Excluir esta nota fiscal?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Excluir">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="py-20 text-center">
                        <i class="fa-solid fa-file-invoice text-6xl text-gray-200 dark:text-slate-700 mb-5 block"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Nenhuma nota fiscal encontrada</p>
                        <a href="{{ route('invoices.create') }}" class="mt-4 inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                            <i class="fa-solid fa-plus"></i> Emitir primeira nota
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Paginação --}}
                <div class="mt-6">{{ $invoices->links() }}</div>

            </section>
        </main>
    </div>

</body>
</html>
