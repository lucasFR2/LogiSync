{{-- Category Quick Create Modal ÔÇö reusable partial --}}
<div id="categoryModal" class="modal-backdrop" style="display:none;">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fa-solid fa-tag"></i> Nova Categoria
            </h3>
            <button type="button" class="icon-btn" id="closeCategoryModal" style="width:32px;height:32px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="categoryModalErrors" style="display:none;" class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div id="categoryModalErrorList"></div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Nome da Categoria <span style="color:var(--red);">*</span>
                </label>
                <input type="text" id="cq_name" class="form-input"
                       placeholder="Ex: Eletr├┤nicos, Ferramentas..." required>
                <small style="color:var(--text-muted);">Deve ser ├║nico. M├íx. 100 caracteres.</small>
            </div>

            <div class="form-group" style="margin-top:.75rem;">
                <label class="form-label">Descri├º├úo</label>
                <textarea id="cq_description" class="form-textarea" rows="2"
                          placeholder="Descri├º├úo opcional..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelCategoryModal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="saveCategoryModal">
                <i class="fa-solid fa-check"></i> Salvar Categoria
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal   = document.getElementById('categoryModal');
    const errBox  = document.getElementById('categoryModalErrors');
    const errList = document.getElementById('categoryModalErrorList');
    if (!modal) return;

    function openModal() {
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('open'));
        errBox.style.display = 'none';
        document.getElementById('cq_name')?.focus();
    }

    function closeModal() {
        modal.classList.remove('open');
        setTimeout(() => {
            modal.style.display = 'none';
            ['cq_name', 'cq_description'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            errBox.style.display = 'none';
        }, 200);
    }

    // Open triggers ÔÇö any element with [data-open-category-modal]
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-open-category-modal]')) {
            e.preventDefault();
            openModal();
        }
    });

    document.getElementById('closeCategoryModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelCategoryModal')?.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.getElementById('saveCategoryModal')?.addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
        errBox.style.display = 'none';

        const data = {
            name:        document.getElementById('cq_name').value.trim(),
            description: document.getElementById('cq_description').value.trim(),
        };

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;

            const res = await fetch('{{ route("categories.quick-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data),
            });

            const json = await res.json();

            if (!res.ok) {
                const errors = json.errors || { general: [json.message || 'Erro desconhecido'] };
                errList.innerHTML = Object.values(errors).flat()
                    .map(e => `<div>${e}</div>`).join('');
                errBox.style.display = 'flex';
                return;
            }

            // Inject new option into every category <select> on the page
            document.querySelectorAll('select[name="category"], .category-select').forEach(sel => {
                const opt = document.createElement('option');
                opt.value    = json.name;
                opt.text     = json.name;
                opt.selected = true;
                sel.appendChild(opt);
            });

            closeModal();
        } catch(err) {
            errList.innerHTML = '<div>Erro de conex├úo. Tente novamente.</div>';
            errBox.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar Categoria';
        }
    });
})();
</script>
@endpush
