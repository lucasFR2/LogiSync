<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Nota Fiscal - LogiSync WMS</title>
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
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white"><i class="fa-solid fa-file-invoice-dollar text-blue-600 mr-2"></i>Nova Nota Fiscal</h1>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-400"><i class="fa-solid fa-moon"></i></button>
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
            </div>
        </header>

        <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
        @csrf
        <div class="p-8 space-y-6">

            {{-- Errors --}}
            @if($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="list-disc list-inside text-red-700 dark:text-red-300 text-sm space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Cabeçalho da NF --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-info-circle text-blue-500"></i> Informações Gerais</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número</label>
                        <input type="text" value="{{ $number }}" readonly class="w-full px-3 py-2 border border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg bg-gray-50 text-gray-500 font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="saida">↑ Saída</option>
                            <option value="entrada">↓ Entrada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Emissão</label>
                        <input type="date" name="issued_at" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vencimento</label>
                        <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fornecedor (opcional)</label>
                        <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">— Nenhum —</option>
                            @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Forma de Pagamento <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="pix">PIX</option>
                            <option value="boleto">Boleto Bancário</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="transferencia">Transferência Bancária</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Destinatário --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-green-500"></i> Destinatário / Remetente</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome / Razão Social <span class="text-red-500">*</span></label>
                        <input type="text" name="recipient_name" required value="{{ old('recipient_name') }}" placeholder="Ex: Empresa XYZ Ltda" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF / CNPJ</label>
                        <input type="text" name="recipient_document" value="{{ old('recipient_document') }}" placeholder="00.000.000/0001-00" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                        <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                        <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="(11) 99999-9999" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                        <input type="text" name="recipient_zip" value="{{ old('recipient_zip') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereço</label>
                        <input type="text" name="recipient_address" value="{{ old('recipient_address') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                        <input type="text" name="recipient_city" value="{{ old('recipient_city') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF</label>
                        <input type="text" name="recipient_state" value="{{ old('recipient_state') }}" maxlength="2" placeholder="SP" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm uppercase">
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2"><i class="fa-solid fa-list text-purple-500"></i> Itens da Nota</h2>
                    <button type="button" onclick="addItem()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Adicionar Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                <table class="w-full text-sm" id="items-table">
                    <thead class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold w-1/3">Produto / Descrição</th>
                            <th class="px-3 py-3 text-left font-semibold w-20">Unid.</th>
                            <th class="px-3 py-3 text-right font-semibold w-24">Qtde</th>
                            <th class="px-3 py-3 text-right font-semibold w-28">Preço Unit.</th>
                            <th class="px-3 py-3 text-right font-semibold w-20">Desc. %</th>
                            <th class="px-3 py-3 text-right font-semibold w-28">Total</th>
                            <th class="px-3 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- linhas adicionadas por JS --}}
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 dark:border-slate-700">
                        <tr>
                            <td colspan="4"></td>
                            <td class="px-3 py-2 text-right text-sm text-gray-600 dark:text-gray-400 font-medium">Subtotal</td>
                            <td class="px-3 py-2 text-right font-bold text-gray-800 dark:text-white" id="subtotal-display">R$ 0,00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td class="px-3 py-2 text-right text-sm text-gray-600 dark:text-gray-400">
                                <span>Desconto (R$)</span>
                                <input type="number" name="discount" id="discount-input" value="0" min="0" step="0.01"
                                    class="ml-2 w-24 text-right px-2 py-1 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm" oninput="calcTotals()">
                            </td>
                            <td class="px-3 py-2 text-right text-red-600 dark:text-red-400 font-semibold" id="discount-display">- R$ 0,00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td class="px-3 py-2 text-right text-sm text-gray-600 dark:text-gray-400">
                                <span>Frete (R$)</span>
                                <input type="number" name="shipping" id="shipping-input" value="0" min="0" step="0.01"
                                    class="ml-2 w-24 text-right px-2 py-1 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm" oninput="calcTotals()">
                            </td>
                            <td class="px-3 py-2 text-right text-blue-600 dark:text-blue-400 font-semibold" id="shipping-display">+ R$ 0,00</td>
                            <td></td>
                        </tr>
                        <tr class="bg-blue-50 dark:bg-blue-900/20">
                            <td colspan="4"></td>
                            <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white">TOTAL</td>
                            <td class="px-3 py-3 text-right text-xl font-bold text-blue-600 dark:text-blue-400" id="total-display">R$ 0,00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>

            {{-- Observações --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-note-sticky text-yellow-500"></i> Observações</h2>
                <textarea name="notes" rows="3" placeholder="Informações adicionais, condições de entrega, etc..." class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Botões de ação --}}
            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('invoices.index') }}" class="px-6 py-3 border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition font-semibold">
                    Cancelar
                </a>
                <button type="submit" name="action" value="draft" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar Rascunho
                </button>
                <button type="submit" name="action" value="emit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Emitir Nota Fiscal
                </button>
            </div>

        </div>
        </form>
    </main>
</div>

{{-- Select de produto (template) --}}
<template id="product-select-template">
    <select class="product-select w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm focus:ring-2 focus:ring-blue-500" onchange="fillProductData(this)">
        <option value="">— Digitar descrição —</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}" data-price="{{ $p->unit_price }}" data-unit="{{ $p->unit }}" data-name="{{ $p->name }}">{{ $p->name }}</option>
        @endforeach
    </select>
</template>

<script>
let itemIndex = 0;

function fmtBR(val) {
    return 'R$ ' + parseFloat(val || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function calcTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const disc  = parseFloat(row.querySelector('.disc-input').value) || 0;
        const total = qty * price * (1 - disc / 100);
        row.querySelector('.total-display').textContent = fmtBR(total);
        subtotal += total;
    });
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const shipping = parseFloat(document.getElementById('shipping-input').value) || 0;
    const grand    = subtotal - discount + shipping;
    document.getElementById('subtotal-display').textContent  = fmtBR(subtotal);
    document.getElementById('discount-display').textContent  = '- ' + fmtBR(discount);
    document.getElementById('shipping-display').textContent  = '+ ' + fmtBR(shipping);
    document.getElementById('total-display').textContent     = fmtBR(grand);
}

function addItem(data = {}) {
    const i   = itemIndex++;
    const row = document.createElement('tr');
    row.className = 'item-row border-b border-gray-100 dark:border-slate-800';
    const selectHtml = document.getElementById('product-select-template').innerHTML;
    row.innerHTML = `
        <td class="px-3 py-2">
            <input type="hidden" name="items[${i}][product_id]" class="product-id-input">
            ${selectHtml.replace(/name="/g, `name_dummy="`)}
            <input type="text" name="items[${i}][description]" required placeholder="Descrição do item"
                class="desc-input mt-1 w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm"
                value="${data.description || ''}">
        </td>
        <td class="px-3 py-2">
            <input type="text" name="items[${i}][unit]" placeholder="un" value="${data.unit || 'un'}"
                class="unit-input w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm text-center">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][quantity]" required min="0.001" step="0.001" value="${data.quantity || 1}"
                class="qty-input w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm text-right" oninput="calcTotals()">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][unit_price]" required min="0" step="0.01" value="${data.unit_price || '0.00'}"
                class="price-input w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm text-right" oninput="calcTotals()">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][discount]" min="0" max="100" step="0.01" value="${data.discount || 0}"
                class="disc-input w-full px-2 py-1.5 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded text-sm text-right" oninput="calcTotals()">
        </td>
        <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-white total-display">R$ 0,00</td>
        <td class="px-3 py-2 text-center">
            <button type="button" onclick="this.closest('tr').remove(); calcTotals();" class="text-red-400 hover:text-red-600 transition"><i class="fa-solid fa-trash-can"></i></button>
        </td>
    `;
    document.getElementById('items-body').appendChild(row);

    // Bind product select
    const sel = row.querySelector('select');
    if (sel) sel.addEventListener('change', function() { fillProductData(this); });

    calcTotals();
}

function fillProductData(sel) {
    const row = sel.closest('tr');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    row.querySelector('.product-id-input').value = opt.value;
    row.querySelector('.desc-input').value   = opt.dataset.name || '';
    row.querySelector('.price-input').value  = opt.dataset.price || 0;
    row.querySelector('.unit-input').value   = opt.dataset.unit || 'un';
    calcTotals();
}

// Adiciona 1 item inicial
addItem();
</script>
</body>
</html>
