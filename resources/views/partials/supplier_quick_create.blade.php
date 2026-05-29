{{-- Supplier Quick Create Modal ÔÇö reusable partial --}}
<div id="supplierModal" class="modal-backdrop" style="display:none;">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-building"></i> Novo Fornecedor</h3>
            <button type="button" class="icon-btn" id="closeSupplierModal" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="supplierModalErrors" style="display:none;" class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div id="supplierModalErrorList"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Nome / Raz├úo Social <span style="color:var(--red);">*</span></label>
                <input type="text" id="sq_name" class="form-input" placeholder="Ex: Distribuidora ABC Ltda" required>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">CNPJ</label>
                    <input type="text" id="sq_cnpj" class="form-input" placeholder="00.000.000/0000-00" maxlength="18">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="tel" id="sq_phone" class="form-input" placeholder="(11) 99999-9999">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" id="sq_email" class="form-input" placeholder="contato@empresa.com">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelSupplierModal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="saveSupplierModal">
                <i class="fa-solid fa-check"></i> Salvar Fornecedor
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal     = document.getElementById('supplierModal');
    const errBox    = document.getElementById('supplierModalErrors');
    const errList   = document.getElementById('supplierModalErrorList');
    if (!modal) return;

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        errBox.style.display = 'none';
    }

    function closeModal() {
        modal.classList.remove('open');
        setTimeout(() => {
            modal.style.display = 'none';
            ['sq_name','sq_cnpj','sq_phone','sq_email'].forEach(id => {
                const el = document.getElementById(id);
                if(el) el.value = '';
            });
            errBox.style.display = 'none';
        }, 200);
    }

    // Open triggers ÔÇö any button with data-open-supplier-modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-open-supplier-modal]')) {
            e.preventDefault();
            openModal();
        }
    });

    document.getElementById('closeSupplierModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelSupplierModal')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.getElementById('saveSupplierModal')?.addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
        errBox.style.display = 'none';

        const data = {
            name:  document.getElementById('sq_name').value,
            cnpj:  document.getElementById('sq_cnpj').value,
            phone: document.getElementById('sq_phone').value,
            email: document.getElementById('sq_email').value,
        };

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;

            const res = await fetch('/suppliers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data),
            });

            const json = await res.json();

            if (!res.ok) {
                const errors = json.errors || { general: [json.message || 'Erro desconhecido'] };
                errList.innerHTML = Object.values(errors).flat().map(e => `<div>${e}</div>`).join('');
                errBox.style.display = 'flex';
                return;
            }

            // Success: inject option into all supplier selects on the page
            const supplier = json.supplier;
            document.querySelectorAll('select[name="supplier_id"]').forEach(sel => {
                const opt = document.createElement('option');
                opt.value = supplier.id;
                opt.text = supplier.name;
                opt.selected = true;
                sel.appendChild(opt);
            });

            closeModal();
        } catch(err) {
            errList.innerHTML = '<div>Erro de conex├úo. Tente novamente.</div>';
            errBox.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar Fornecedor';
        }
    });
})();
</script>
@endpush
