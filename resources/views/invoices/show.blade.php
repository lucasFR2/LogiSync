<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->number }} - LogiSync WMS</title>
    <script>tailwindConfig = { darkMode: 'class' };</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/theme-toggle.js') }}"></script>
</head>
<body class="bg-gray-50 dark:bg-slate-950 font-sans transition-colors duration-300">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-slate-900 dark:bg-slate-950 text-white hidden md:flex flex-col">
        <div class="p-6 border-b border-slate-800 flex justify-center">
            <a href="/"><img src="{{ asset('images/logisync-logo.png') }}" alt="LogiSync" class="w-40 brightness-0 invert"></a>
        </div>
        <nav class="flex-1 px-4 mt-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition"><i class="fa-solid fa-chart-line mr-3"></i> Dashboard</a>
            <a href="{{ route('products.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition"><i class="fa-solid fa-boxes-stacked mr-3"></i> Produtos</a>
            <a href="{{ route('inventory.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition"><i class="fa-solid fa-truck-ramp-box mr-3"></i> Entradas</a>
            <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-gray-400 hover:bg-slate-800 hover:text-white rounded-lg transition"><i class="fa-solid fa-handshake mr-3"></i> Fornecedores</a>
            <a href="{{ route('invoices.index') }}" class="flex items-center p-3 bg-blue-600 rounded-lg text-white"><i class="fa-solid fa-file-invoice mr-3"></i> Notas Fiscais</a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="flex items-center w-full p-3 text-red-400 hover:bg-red-900/20 rounded-lg transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Sair</button>
            </form>
        </div>
    </aside>

    <main class="flex-1">
        <header class="bg-white dark:bg-slate-900 shadow-sm px-8 py-4 flex justify-between items-center border-b dark:border-slate-800">
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-blue-600 transition"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $invoice->number }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Série {{ $invoice->series }} &bull; {{ $invoice->type === 'saida' ? 'Nota de Saída' : 'Nota de Entrada' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400"><i class="fa-solid fa-moon"></i></button>

                @if($invoice->status === 'emitida')
                <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition text-sm">
                    <i class="fa-solid fa-file-pdf"></i> Baixar PDF
                </a>
                @endif

                @if($invoice->status === 'rascunho')
                <a href="{{ route('invoices.edit', $invoice) }}"
                    class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold transition text-sm">
                    <i class="fa-solid fa-pencil"></i> Editar
                </a>
                @endif

                @if($invoice->status !== 'cancelada')
                <form method="POST" action="{{ route('invoices.cancel', $invoice) }}" onsubmit="return confirm('Cancelar esta nota fiscal?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition text-sm">
                        <i class="fa-solid fa-ban"></i> Cancelar NF
                    </button>
                </form>
                @endif

                <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</div>
            </div>
        </header>

        <div class="p-8 space-y-6">

            {{-- Alertas --}}
            @foreach(['success' => 'green', 'error' => 'red'] as $key => $color)
            @if($msg = session($key))
            <div class="p-4 bg-{{ $color }}-50 dark:bg-{{ $color }}-950 border border-{{ $color }}-200 dark:border-{{ $color }}-800 rounded-lg flex items-center gap-3">
                <i class="fa-solid fa-{{ $color === 'green' ? 'check-circle' : 'circle-exclamation' }} text-{{ $color }}-600"></i>
                <span class="text-{{ $color }}-700 dark:text-{{ $color }}-300">{{ $msg }}</span>
            </div>
            @endif
            @endforeach

            {{-- Status badge --}}
            @php
            $statusMap = ['rascunho' => ['color'=>'yellow','icon'=>'pen'], 'emitida' => ['color'=>'green','icon'=>'check-circle'], 'cancelada' => ['color'=>'red','icon'=>'ban']];
            $s = $statusMap[$invoice->status] ?? ['color'=>'gray','icon'=>'circle'];
            @endphp
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-{{ $s['color'] }}-100 text-{{ $s['color'] }}-800 dark:bg-{{ $s['color'] }}-900/30 dark:text-{{ $s['color'] }}-400">
                    <i class="fa-solid fa-{{ $s['icon'] }}"></i> {{ $invoice->statusLabel() }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Emitido em {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : '-' }} &bull; por {{ $invoice->user->name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Emitente --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                    <h2 class="font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wider mb-4 flex items-center gap-2"><i class="fa-solid fa-building text-blue-500"></i> Emitente</h2>
                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $invoice->issuer_name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">CNPJ: {{ $invoice->issuer_cnpj }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $invoice->issuer_address }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->issuer_city }} - {{ $invoice->issuer_state }} &bull; CEP: {{ $invoice->issuer_zip }}</p>
                </div>

                {{-- Destinatário --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                    <h2 class="font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wider mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-green-500"></i> Destinatário</h2>
                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $invoice->recipient_name }}</p>
                    @if($invoice->recipient_document)<p class="text-sm text-gray-600 dark:text-gray-400">CPF/CNPJ: {{ $invoice->recipient_document }}</p>@endif
                    @if($invoice->recipient_email)<p class="text-sm text-gray-600 dark:text-gray-400"><i class="fa-solid fa-envelope mr-1"></i>{{ $invoice->recipient_email }}</p>@endif
                    @if($invoice->recipient_phone)<p class="text-sm text-gray-600 dark:text-gray-400"><i class="fa-solid fa-phone mr-1"></i>{{ $invoice->recipient_phone }}</p>@endif
                    @if($invoice->recipient_address)<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $invoice->recipient_address }}, {{ $invoice->recipient_city }} - {{ $invoice->recipient_state }}</p>@endif
                </div>
            </div>

            {{-- Detalhes financeiros --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pagamento</p>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->paymentLabel() }}</p>
                </div>
                @if($invoice->due_date)
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Vencimento</p>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>
                @endif
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-5">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Subtotal</p>
                    <p class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-900 p-5">
                    <p class="text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Total</p>
                    <p class="font-bold text-blue-700 dark:text-blue-300 text-2xl">R$ {{ number_format($invoice->total, 2, ',', '.') }}</p>
                </div>
            </div>

            {{-- Itens --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800">
                    <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><i class="fa-solid fa-list text-purple-500"></i> Itens ({{ $invoice->items->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">#</th>
                            <th class="px-6 py-3 text-left">Descrição</th>
                            <th class="px-6 py-3 text-center">Unid.</th>
                            <th class="px-6 py-3 text-right">Qtde</th>
                            <th class="px-6 py-3 text-right">Preço Unit.</th>
                            <th class="px-6 py-3 text-right">Desc. %</th>
                            <th class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $idx => $item)
                        <tr class="border-b border-gray-100 dark:border-slate-800">
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->description }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">{{ $item->discount > 0 ? number_format($item->discount, 2, ',', '.').'%' : '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-slate-800">
                        <tr><td colspan="6" class="px-6 py-2 text-right text-gray-600 dark:text-gray-400 text-xs">Subtotal</td><td class="px-6 py-2 text-right font-semibold text-gray-800 dark:text-white">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
                        @if($invoice->discount > 0)<tr><td colspan="6" class="px-6 py-1 text-right text-red-600 text-xs">Desconto</td><td class="px-6 py-1 text-right text-red-600">- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</td></tr>@endif
                        @if($invoice->shipping > 0)<tr><td colspan="6" class="px-6 py-1 text-right text-blue-600 text-xs">Frete</td><td class="px-6 py-1 text-right text-blue-600">+ R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</td></tr>@endif
                        <tr class="border-t-2 border-gray-200 dark:border-slate-700"><td colspan="6" class="px-6 py-3 text-right font-bold text-gray-800 dark:text-white">TOTAL GERAL</td><td class="px-6 py-3 text-right text-xl font-bold text-blue-600 dark:text-blue-400">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
                </div>
            </div>

            @if($invoice->notes)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                <h2 class="font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wider mb-3 flex items-center gap-2"><i class="fa-solid fa-note-sticky text-yellow-500"></i> Observações</h2>
                <p class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ $invoice->notes }}</p>
            </div>
            @endif

        </div>
    </main>
</div>
</body>
</html>
