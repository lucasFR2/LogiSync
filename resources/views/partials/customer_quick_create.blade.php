{{-- ══════════════════════════════════════════════
     Modal Fullscreen: Cadastro Rápido de Cliente
     Campos completos: nome, tipo, doc, email, fone,
     IE, endereço completo, bairro, nº, compl.,
     data de nasc., gênero + auto-preench. ViaCEP
     ══════════════════════════════════════════════ --}}

<style>
/* Ocupa tudo (sobre a sidebar também) */
#customer-quick-create-modal {
    z-index: 1000 !important;
    position: fixed !important;
    inset: 0 !important;
    left: 0 !important;
}

/* Garante que inputs/selects usem o design system */
#customer-quick-create-modal .form-input,
#customer-quick-create-modal .form-select,
#customer-quick-create-modal .form-textarea {
    height: 44px;
}

/* Layout em duas colunas side-by-side */
#customer-quick-create-modal .qc-grid {
    display: grid;
    gap: 1.25rem;
}
#customer-quick-create-modal .qc-grid-2  { grid-template-columns: 1fr 1fr; }
#customer-quick-create-modal .qc-grid-3  { grid-template-columns: 1fr 1fr 1fr; }
#customer-quick-create-modal .qc-grid-4  { grid-template-columns: 1fr 1fr 1fr 1fr; }
#customer-quick-create-modal .qc-grid-32 { grid-template-columns: 3fr 2fr; }
#customer-quick-create-modal .qc-grid-23 { grid-template-columns: 2fr 3fr; }
#customer-quick-create-modal .qc-span-2  { grid-column: span 2; }
#customer-quick-create-modal .qc-span-3  { grid-column: span 3; }
#customer-quick-create-modal .qc-span-4  { grid-column: span 4; }

/* Section divider */
#customer-quick-create-modal .qc-section {
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
    margin-top: 0.5rem;
}
#customer-quick-create-modal .qc-section-label {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
#customer-quick-create-modal .qc-section-label i {
    font-size: 0.85rem;
    color: var(--blue);
}
</style>

<div id="customer-quick-create-modal" style="display:none; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
    <div style="background: var(--bg-base); width:100%; height:100%; display:flex; flex-direction:column; overflow:hidden;">

        {{-- ── Header ── --}}
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid var(--border); background: var(--bg-surface); display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:36px; height:36px; background:var(--blue-bg); color:var(--blue); border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; font-size:1rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h2 style="margin:0; font-family:'Outfit'; font-size:1.2rem; font-weight:800; color:var(--text-primary);">Cadastrar Novo Cliente</h2>
                    <p style="margin:0; font-size:0.75rem; color:var(--text-muted);">Preencha os dados do cliente para cadastrá-lo no sistema</p>
                </div>
            </div>
            <button type="button" onclick="toggleCustomerModal()" class="icon-btn" title="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- ── Form Body (scrollável) ── --}}
        <div style="flex:1; overflow-y:auto; padding: 2rem; background: var(--bg-base);">
            <form id="quick-customer-form" style="max-width: 1000px; margin: 0 auto;">
                @csrf

                {{-- ── DADOS PESSOAIS ── --}}
                <div class="qc-section-label"><i class="fa-solid fa-id-card"></i> Dados Pessoais</div>

                {{-- Nome --}}
                <div class="qc-grid qc-grid-2" style="margin-bottom: 1.25rem;">
                    <div class="form-group qc-span-2" style="margin-bottom:0;">
                        <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" id="qc-name" required placeholder="Nome completo ou razão social..." class="form-input">
                    </div>
                </div>

                <div class="qc-grid qc-grid-3" style="margin-bottom: 1.25rem;">
                    {{-- Tipo --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Tipo <span style="color:var(--red);">*</span></label>
                        <select name="type" id="qc-type" required class="form-select" onchange="qcUpdateDocMask()">
                            <option value="company">🏢 Pessoa Jurídica (CNPJ)</option>
                            <option value="individual">👤 Pessoa Física (CPF)</option>
                        </select>
                    </div>

                    {{-- CPF / CNPJ --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">CPF / CNPJ <span style="color:var(--red);">*</span></label>
                        <input type="text" name="document" id="qc-document" required placeholder="00.000.000/0001-00" class="form-input" style="font-family: monospace;">
                    </div>

                    {{-- Inscrição Estadual --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Inscrição Estadual</label>
                        <input type="text" name="state_registration" id="qc-state-reg" placeholder="IE (opcional)" class="form-input">
                    </div>
                </div>

                <div class="qc-grid qc-grid-3" style="margin-bottom: 1.25rem;">
                    {{-- E-mail --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" id="qc-email" placeholder="email@exemplo.com" class="form-input">
                    </div>

                    {{-- Telefone --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="phone" id="qc-phone" placeholder="(00) 90000-0000" class="form-input" style="font-family:monospace;" oninput="qcMaskPhone(this)">
                    </div>

                    {{-- Gênero --}}
                    <div class="form-group" id="qc-gender-row" style="margin-bottom:0;">
                        <label class="form-label">Gênero</label>
                        <select name="gender" id="qc-gender" class="form-select">
                            <option value="">— Selecione —</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outro">Outro</option>
                            <option value="Preferiu não informar">Prefiro não informar</option>
                        </select>
                    </div>
                </div>

                {{-- Data de Nascimento (só pessoa física) --}}
                <div class="form-group" id="qc-birth-row" style="margin-bottom:1.25rem;">
                    <label class="form-label">Data de Nascimento</label>
                    <input type="date" name="birth_date" id="qc-birth" class="form-input" style="max-width:240px;">
                </div>

                {{-- ── ENDEREÇO ── --}}
                <div class="qc-section">
                    <div class="qc-section-label"><i class="fa-solid fa-map-location-dot"></i> Endereço</div>

                    {{-- CEP + busca ViaCEP --}}
                    <div class="qc-grid qc-grid-32" style="margin-bottom: 1.25rem;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">CEP</label>
                            <div style="display:flex; gap:0.5rem; align-items:flex-end;">
                                <input type="text" name="zip_code" id="qc-zip" placeholder="00000-000" class="form-input" style="font-family:monospace; flex:1;" oninput="qcMaskCep(this)" onblur="qcFetchCep(this.value)">
                                <button type="button" id="qc-cep-btn" onclick="qcFetchCep(document.getElementById('qc-zip').value)" class="btn btn-secondary" style="height:44px; white-space:nowrap; flex-shrink:0;">
                                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                </button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" id="qc-bairro-group">
                            <label class="form-label">Bairro / Distrito</label>
                            <input type="text" name="neighborhood" id="qc-neighborhood" placeholder="Bairro..." class="form-input">
                        </div>
                    </div>

                    <div class="qc-grid" style="grid-template-columns: 2fr 0.6fr 1.4fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        {{-- Logradouro --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="address" id="qc-address" placeholder="Rua, Avenida, Travessa..." class="form-input">
                        </div>

                        {{-- Número --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nº</label>
                            <input type="text" name="number" id="qc-number" placeholder="000" class="form-input">
                        </div>

                        {{-- Complemento --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complement" id="qc-complement" placeholder="Apto, Sala, Bloco..." class="form-input">
                        </div>
                    </div>

                    <div class="qc-grid qc-grid-23" style="margin-bottom: 1.25rem;">
                        {{-- Cidade --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="city" id="qc-city" placeholder="Cidade..." class="form-input">
                        </div>

                        {{-- Estado --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Estado (UF)</label>
                            <select name="state" id="qc-state" class="form-select">
                                <option value="">— UF —</option>
                                <option value="AC">AC – Acre</option>
                                <option value="AL">AL – Alagoas</option>
                                <option value="AP">AP – Amapá</option>
                                <option value="AM">AM – Amazonas</option>
                                <option value="BA">BA – Bahia</option>
                                <option value="CE">CE – Ceará</option>
                                <option value="DF">DF – Distrito Federal</option>
                                <option value="ES">ES – Espírito Santo</option>
                                <option value="GO">GO – Goiás</option>
                                <option value="MA">MA – Maranhão</option>
                                <option value="MT">MT – Mato Grosso</option>
                                <option value="MS">MS – Mato Grosso do Sul</option>
                                <option value="MG">MG – Minas Gerais</option>
                                <option value="PA">PA – Pará</option>
                                <option value="PB">PB – Paraíba</option>
                                <option value="PR">PR – Paraná</option>
                                <option value="PE">PE – Pernambuco</option>
                                <option value="PI">PI – Piauí</option>
                                <option value="RJ">RJ – Rio de Janeiro</option>
                                <option value="RN">RN – Rio Grande do Norte</option>
                                <option value="RS">RS – Rio Grande do Sul</option>
                                <option value="RO">RO – Rondônia</option>
                                <option value="RR">RR – Roraima</option>
                                <option value="SC">SC – Santa Catarina</option>
                                <option value="SP">SP – São Paulo</option>
                                <option value="SE">SE – Sergipe</option>
                                <option value="TO">TO – Tocantins</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Erro inline --}}
                <div id="qc-error" style="display:none; background:var(--red-bg); color:var(--red); border:1px solid var(--red-bg); border-radius:var(--r-md); padding:0.75rem 1rem; font-size:0.875rem; margin-top:1rem;">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <span id="qc-error-msg"></span>
                </div>

            </form>
        </div>

        {{-- ── Footer: Ações ── --}}
        <div style="padding: 1.25rem 2rem; border-top: 1px solid var(--border); background: var(--bg-surface); display:flex; justify-content:flex-end; gap:1rem; flex-shrink:0;">
            <button type="button" onclick="toggleCustomerModal()" class="btn btn-secondary">
                <i class="fa-solid fa-times mr-2"></i> Cancelar
            </button>
            <button type="button" id="qc-submit-btn" onclick="submitQuickCustomer()" class="btn btn-primary" style="background:var(--blue); border-color:var(--blue); box-shadow: 0 8px 16px -4px var(--blue-bg);">
                <i class="fa-solid fa-check mr-2"></i> Salvar Cliente
            </button>
        </div>

    </div>
</div>

<script>
(function () {
    // Move modal para o body raiz, evitando ser cortado por z-index de pai
    const modal = document.getElementById('customer-quick-create-modal');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
})();

/* ── Toggle ────────────────────────── */
function toggleCustomerModal() {
    const m = document.getElementById('customer-quick-create-modal');
    const isOpen = m.style.display !== 'none';
    m.style.display = isOpen ? 'none' : 'flex';
    if (!isOpen) {
        document.getElementById('qc-error').style.display = 'none';
    }
}

/* ── Máscara CPF/CNPJ ──────────────── */
function qcUpdateDocMask() {
    const type = document.getElementById('qc-type').value;
    const doc  = document.getElementById('qc-document');
    const birth = document.getElementById('qc-birth-row');
    const gender = document.getElementById('qc-gender-row');

    if (type === 'individual') {
        doc.placeholder = '000.000.000-00';
        birth.style.display = '';
        gender.style.display = '';
    } else {
        doc.placeholder = '00.000.000/0001-00';
        birth.style.display = 'none';
        gender.style.display = 'none';
    }
    doc.value = '';
    doc.oninput = () => qcMaskDoc(doc);
}

function qcMaskDoc(input) {
    const type = document.getElementById('qc-type').value;
    let v = input.value.replace(/\D/g, '');
    if (type === 'individual') {
        // CPF: 000.000.000-00
        v = v.substring(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        // CNPJ: 00.000.000/0001-00
        v = v.substring(0, 14);
        v = v.replace(/(\d{2})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1/$2');
        v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    input.value = v;
}

function qcMaskPhone(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 11);
    if (v.length > 10) {
        v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else {
        v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    }
    input.value = v;
}

function qcMaskCep(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 8);
    if (v.length > 5) v = v.replace(/(\d{5})(\d{1,3})/, '$1-$2');
    input.value = v;
}

/* ── ViaCEP ────────────────────────── */
async function qcFetchCep(cep) {
    const raw = (cep || '').replace(/\D/g, '');
    if (raw.length !== 8) return;

    const btn = document.getElementById('qc-cep-btn');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
    btn.disabled = true;

    try {
        const res  = await fetch(`https://viacep.com.br/ws/${raw}/json/`);
        const data = await res.json();
        if (!data.erro) {
            document.getElementById('qc-address').value      = data.logradouro || '';
            document.getElementById('qc-neighborhood').value = data.bairro || '';
            document.getElementById('qc-city').value         = data.localidade || '';
            const stateEl = document.getElementById('qc-state');
            if (stateEl) stateEl.value = data.uf || '';
            document.getElementById('qc-number').focus();
        }
    } catch (_) {}

    btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Buscar';
    btn.disabled = false;
}

/* ── Submit AJAX ───────────────────── */
async function submitQuickCustomer() {
    const form = document.getElementById('quick-customer-form');
    const btn  = document.getElementById('qc-submit-btn');
    const errDiv = document.getElementById('qc-error');
    const errMsg = document.getElementById('qc-error-msg');

    errDiv.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Salvando...';

    try {
        const res  = await fetch('{{ route("customers.store") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await res.json();

        if (data.id) {
            // Adiciona ao(s) select(s) de cliente e aciona change
            document.querySelectorAll('.customer-select').forEach(sel => {
                const opt = new Option(data.name, data.id);
                opt.setAttribute('data-name',               data.name || '');
                opt.setAttribute('data-document',           data.document || '');
                opt.setAttribute('data-email',              data.email || '');
                opt.setAttribute('data-phone',              data.phone || '');
                opt.setAttribute('data-address',            data.address || '');
                opt.setAttribute('data-number',             data.number || '');
                opt.setAttribute('data-complement',         data.complement || '');
                opt.setAttribute('data-neighborhood',       data.neighborhood || '');
                opt.setAttribute('data-city',               data.city || '');
                opt.setAttribute('data-state',              data.state || '');
                opt.setAttribute('data-zip',                data.zip_code || '');
                opt.setAttribute('data-state-registration', data.state_registration || '');
                sel.add(opt);
                sel.value = data.id;
                sel.dispatchEvent(new Event('change'));
            });

            // Adiciona ao customer-picker se existir
            const pickerTableBody = document.querySelector('#customer-picker-table tbody');
            if (pickerTableBody) {
                const newRow = document.createElement('tr');
                newRow.className = 'customer-row';
                newRow.setAttribute('data-id', data.id);
                newRow.setAttribute('data-name', (data.name || '').toLowerCase());
                newRow.setAttribute('data-document', (data.document || '').toLowerCase());
                newRow.setAttribute('data-email', (data.email || '').toLowerCase());
                newRow.setAttribute('data-city', (data.city || '').toLowerCase());
                
                newRow.innerHTML = `
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">${data.name}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace; margin-top:2px;">
                            Doc: ${data.document || 'Sem documento'}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:500; color:var(--text-secondary);">${data.email || 'Sem e-mail'}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                            Tel: ${data.phone || 'Sem telefone'}
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:var(--bg-hover); color:var(--text-secondary); font-size:0.75rem; font-weight:600;">
                            ${data.city || 'Sem cidade'} ${data.state ? '/ ' + data.state : ''}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-primary btn-sm btn-select-customer" data-id="${data.id}" style="padding:0.4rem 0.8rem; font-size:0.8rem; justify-content:center; width:100%;">
                            Selecionar
                        </button>
                    </td>
                `;
                pickerTableBody.appendChild(newRow);
            }

            toggleCustomerModal();
            form.reset();
            qcUpdateDocMask(); // Reseta placeholder

        } else if (data.errors) {
            const msgs = Object.values(data.errors).flat().join(' | ');
            errMsg.textContent = msgs;
            errDiv.style.display = 'block';
        } else {
            errMsg.textContent = 'Erro ao cadastrar cliente. Verifique os dados e tente novamente.';
            errDiv.style.display = 'block';
        }

    } catch (e) {
        errMsg.textContent = 'Erro de conexão. Verifique sua internet e tente novamente.';
        errDiv.style.display = 'block';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Salvar Cliente';
}

// Inicializa máscara ao carregar
document.addEventListener('DOMContentLoaded', () => {
    qcUpdateDocMask();
    const docEl = document.getElementById('qc-document');
    if (docEl) docEl.addEventListener('input', () => qcMaskDoc(docEl));
});
</script>
