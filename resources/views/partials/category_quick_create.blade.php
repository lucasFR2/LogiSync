{{-- Category Quick Create Modal — reusable partial --}}
<style>
#categoryModal.modal-backdrop {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    z-index: 95 !important;
    padding: 0 !important;
    background: var(--bg-base) !important;
    display: none;
    align-items: stretch !important;
    justify-content: stretch !important;
}
@media (min-width: 769px) {
    #categoryModal.modal-backdrop {
        left: var(--sidebar-w) !important;
    }
}
#categoryModal .modal {
    max-width: 100% !important;
    width: 100% !important;
    height: 100vh !important;
    max-height: 100vh !important;
    border-radius: 0 !important;
    margin: 0 !important;
    border: none !important;
    box-shadow: none !important;
    display: flex !important;
    flex-direction: column !important;
    background: var(--bg-surface) !important;
}
#categoryModal .modal-header-container,
#categoryModal .modal-body-container,
#categoryModal .modal-footer-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
}
#categoryModal .modal-header-container {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}
#categoryModal .modal-footer-container {
    flex-direction: row;
    justify-content: flex-end;
    gap: 1rem;
}
</style>
<div id="categoryModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-header-container">
                <h3 class="modal-title">
                    <i class="fa-solid fa-tag"></i> Nova Categoria
                </h3>
                <button type="button" class="icon-btn" id="closeCategoryModal" style="width:32px;height:32px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-body-container">
                <div id="categoryModalErrors" style="display:none;" class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div id="categoryModalErrorList"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Nome da Categoria <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" id="cq_name" class="form-input"
                           placeholder="Ex: Eletrônicos, Ferramentas..." required>
                    <small style="color:var(--text-muted);">Deve ser único. Máx. 100 caracteres.</small>
                </div>

                <div class="form-group" style="margin-top:1.5rem;">
                    <label class="form-label">Grupo da Categoria (Opcional)</label>
                    <select id="cq_parent_id" class="form-select">
                        <option value="">-- Sem Grupo (Criar como Grupo Principal) --</option>
                        @php
                            $qcParentCategories = \App\Models\Category::whereNull('parent_id')->orderBy('name')->get();
                        @endphp
                        @foreach($qcParentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);">Selecione um grupo se for um subgrupo.</small>
                </div>

                <div class="form-group" style="margin-top:1.5rem;">
                    <label class="form-label">Descrição</label>
                    <textarea id="cq_description" class="form-textarea" rows="4"
                              placeholder="Descrição opcional..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="modal-footer-container">
                <button type="button" class="btn btn-secondary" id="cancelCategoryModal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveCategoryModal">
                    <i class="fa-solid fa-check"></i> Salvar Categoria
                </button>
            </div>
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

    // Move modal to body to avoid parent stacking context issues
    document.body.appendChild(modal);

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

    // Open triggers — any element with [data-open-category-modal]
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
            parent_id:   document.getElementById('cq_parent_id').value || null,
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
                if (json.parent_name) {
                    // Try to find or create an optgroup for the parent category
                    let optgroup = sel.querySelector(`optgroup[label="${json.parent_name}"]`);
                    if (!optgroup) {
                        optgroup = document.createElement('optgroup');
                        optgroup.label = json.parent_name;
                        sel.appendChild(optgroup);
                    }
                    const opt = document.createElement('option');
                    opt.value    = json.name;
                    opt.text     = json.name;
                    opt.selected = true;
                    optgroup.appendChild(opt);
                } else {
                    const opt = document.createElement('option');
                    opt.value    = json.name;
                    opt.text     = json.name;
                    opt.selected = true;
                    sel.appendChild(opt);
                }
            });

            closeModal();
        } catch(err) {
            errList.innerHTML = '<div>Erro de conexão. Tente novamente.</div>';
            errBox.style.display = 'flex';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar Categoria';
        }
    });
})();
</script>
@endpush
