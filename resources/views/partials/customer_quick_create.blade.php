<div id="customer-quick-create-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden anim-entrance">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-500"></i> Cadastrar Novo Cliente
            </h3>
            <button type="button" onclick="toggleCustomerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form id="quick-customer-form" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nome / Raz├úo Social <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="company">Pessoa Jur├¡dica (CNPJ)</option>
                        <option value="individual">Pessoa F├¡sica (CPF)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">CPF / CNPJ <span class="text-red-500">*</span></label>
                    <input type="text" name="document" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                    <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div class="md:col-span-2 border-t border-gray-100 dark:border-slate-800 pt-4 mt-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Endere├ºo</h4>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Logradouro</label>
                    <input type="text" name="address" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                    <input type="text" name="city" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Estado (UF)</label>
                    <input type="text" name="state" maxlength="2" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm uppercase">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                    <input type="text" name="zip_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="toggleCustomerModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition">Cancelar</button>
                <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-500/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCustomerModal() {
    const modal = document.getElementById('customer-quick-create-modal');
    modal.classList.toggle('hidden');
}

document.getElementById('quick-customer-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Salvando...';

    fetch('{{ route("customers.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            // Adicionar ao select de clientes
            const selects = document.querySelectorAll('.customer-select');
            selects.forEach(select => {
                const opt = new Option(data.name, data.id);
                // Armazenar dados extras no dataset
                opt.dataset.document = data.document || '';
                opt.dataset.email    = data.email || '';
                opt.dataset.phone    = data.phone || '';
                opt.dataset.address  = data.address || '';
                opt.dataset.city     = data.city || '';
                opt.dataset.state    = data.state || '';
                opt.dataset.zip      = data.zip_code || '';
                
                select.add(opt);
                select.value = data.id;
                // Trigger change to fill the form
                select.dispatchEvent(new Event('change'));
            });
            
            toggleCustomerModal();
            this.reset();
        } else {
            alert('Erro ao cadastrar cliente. Verifique os dados.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro de conex├úo ao cadastrar cliente.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar Cliente';
    });
});
</script>
