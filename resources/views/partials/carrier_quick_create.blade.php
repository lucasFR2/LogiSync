{{-- ══════════════════════════════════════════════
     Modal Fullscreen: Cadastro Rápido de Transportadora
     Campos completos: nome, cnpj, ie, antt, email, fone, contato,
     placa do veículo, uf do veículo, tipo do veículo,
     endereço completo via ViaCEP
     ══════════════════════════════════════════════ --}}

<style>
/* Ocupa tudo (sobre a sidebar também) */
#carrier-quick-create-modal {
    z-index: 1000 !important;
    position: fixed !important;
    inset: 0 !important;
    left: 0 !important;
}

/* Garante que inputs/selects usem o design system */
#carrier-quick-create-modal .form-input,
#carrier-quick-create-modal .form-select,
#carrier-quick-create-modal .form-textarea {
    height: 44px;
}

/* Layout em duas colunas side-by-side */
#carrier-quick-create-modal .qc-grid {
    display: grid;
    gap: 1.25rem;
}
#carrier-quick-create-modal .qc-grid-2  { grid-template-columns: 1fr 1fr; }
#carrier-quick-create-modal .qc-grid-3  { grid-template-columns: 1fr 1fr 1fr; }
#carrier-quick-create-modal .qc-grid-4  { grid-template-columns: 1fr 1fr 1fr 1fr; }
#carrier-quick-create-modal .qc-grid-32 { grid-template-columns: 3fr 2fr; }
#carrier-quick-create-modal .qc-grid-23 { grid-template-columns: 2fr 3fr; }
#carrier-quick-create-modal .qc-span-2  { grid-column: span 2; }
#carrier-quick-create-modal .qc-span-3  { grid-column: span 3; }
#carrier-quick-create-modal .qc-span-4  { grid-column: span 4; }

/* Section divider */
#carrier-quick-create-modal .qc-section {
    border-top: 1px solid var(--border);
    padding-top: 1.5rem;
    margin-top: 0.5rem;
}
#carrier-quick-create-modal .qc-section-label {
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
#carrier-quick-create-modal .qc-section-label i {
    font-size: 0.85rem;
    color: var(--blue);
}
</style>

<div id="carrier-quick-create-modal" style="display:none; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
    <div style="background: var(--bg-base); width:100%; height:100%; display:flex; flex-direction:column; overflow:hidden;">

        {{-- ── Header ── --}}
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid var(--border); background: var(--bg-surface); display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:36px; height:36px; background:var(--blue-bg); color:var(--blue); border-radius:var(--r-md); display:flex; align-items:center; justify-content:center; font-size:1rem;">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h2 style="margin:0; font-family:'Outfit'; font-size:1.2rem; font-weight:800; color:var(--text-primary);">Cadastrar Nova Transportadora</h2>
                    <p style="margin:0; font-size:0.75rem; color:var(--text-muted);">Preencha os dados da transportadora para cadastrá-la no sistema</p>
                </div>
            </div>
            <button type="button" onclick="toggleCarrierModal()" class="icon-btn" title="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- ── Form Body (scrollável) ── --}}
        <div style="flex:1; overflow-y:auto; padding: 2rem; background: var(--bg-base);">
            <form id="quick-carrier-form" style="max-width: 1000px; margin: 0 auto;">
                @csrf

                {{-- ── DADOS DA TRANSPORTADORA ── --}}
                <div class="qc-section-label"><i class="fa-solid fa-building"></i> Dados Gerais</div>

                {{-- Nome --}}
                <div class="qc-grid qc-grid-2" style="margin-bottom: 1.25rem;">
                    <div class="form-group qc-span-2" style="margin-bottom:0;">
                        <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                        <input type="text" name="name" id="qc-carrier-name" required placeholder="Razão social da transportadora..." class="form-input">
                    </div>
                </div>

                <div class="qc-grid qc-grid-3" style="margin-bottom: 1.25rem;">
                    {{-- CNPJ --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" id="qc-carrier-cnpj" data-mask="cnpj" placeholder="00.000.000/0001-00" class="form-input" style="font-family: monospace;">
                    </div>

                    {{-- Inscrição Estadual --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Inscrição Estadual</label>
                        <input type="text" name="state_registration" id="qc-carrier-state-reg" placeholder="000.000.000.000" class="form-input" style="font-family:monospace;" oninput="qcCarrierMaskIE(this)">
                    </div>

                    {{-- RNTRC / ANTT --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">RNTRC (ANTT)</label>
                        <input type="text" name="antt" id="qc-carrier-antt" placeholder="Registro ANTT" class="form-input">
                    </div>
                </div>

                <div class="qc-grid qc-grid-3" style="margin-bottom: 1.25rem;">
                    {{-- E-mail --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" id="qc-carrier-email" placeholder="contato@transportadora.com" class="form-input">
                    </div>

                    {{-- Telefone --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="phone" id="qc-carrier-phone" data-mask="phone" placeholder="(00) 90000-0000" class="form-input" style="font-family:monospace;">
                    </div>

                    {{-- Contato --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Pessoa de Contato</label>
                        <input type="text" name="contact" id="qc-carrier-contact" placeholder="Nome do contato..." class="form-input">
                    </div>
                </div>

                {{-- ── DADOS DO VEÍCULO ── --}}
                <div class="qc-section">
                    <div class="qc-section-label"><i class="fa-solid fa-truck"></i> Dados do Veículo</div>
                    <div class="qc-grid qc-grid-3" style="margin-bottom: 1.25rem;">
                        {{-- Placa --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Placa do Veículo</label>
                            <input type="text" name="vehicle_plate" id="qc-carrier-plate" placeholder="ABC-1234" class="form-input" style="font-family:monospace; text-transform:uppercase;" maxlength="8" oninput="qcCarrierMaskPlate(this)">
                        </div>

                        {{-- UF Placa --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">UF da Placa</label>
                            <select name="vehicle_uf" id="qc-carrier-vehicle-uf" class="form-select">
                                <option value="">— UF —</option>
                                @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                    <option value="{{ $uf }}">{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tipo do Veículo --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tipo do Veículo</label>
                            <input type="text" name="vehicle_type" id="qc-carrier-vehicle-type" placeholder="Ex: Toco, Truck, Carreta..." class="form-input">
                        </div>
                    </div>
                </div>

                {{-- ── ENDEREÇO ── --}}
                <div class="qc-section">
                    <div class="qc-section-label"><i class="fa-solid fa-map-location-dot"></i> Endereço</div>

                    {{-- CEP + busca ViaCEP --}}
                    <div class="qc-grid qc-grid-32" style="margin-bottom: 1.25rem;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">CEP</label>
                            <div style="display:flex; gap:0.5rem; align-items:flex-end;">
                                <input type="text" name="zip_code" id="qc-carrier-zip" data-mask="cep" placeholder="00000-000" class="form-input" style="font-family:monospace; flex:1;" onblur="qcCarrierFetchCep(document.getElementById('qc-carrier-zip').mask ? document.getElementById('qc-carrier-zip').mask.unmaskedValue : this.value)">
                                <button type="button" id="qc-carrier-cep-btn" onclick="qcCarrierFetchCep(document.getElementById('qc-carrier-zip').mask ? document.getElementById('qc-carrier-zip').mask.unmaskedValue : document.getElementById('qc-carrier-zip').value)" class="btn btn-secondary" style="height:44px; white-space:nowrap; flex-shrink:0;">
                                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                </button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;" id="qc-carrier-bairro-group">
                            <label class="form-label">Bairro / Distrito</label>
                            <input type="text" name="neighborhood" id="qc-carrier-neighborhood" placeholder="Bairro..." class="form-input">
                        </div>
                    </div>

                    <div class="qc-grid" style="grid-template-columns: 2fr 0.6fr 1.4fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        {{-- Logradouro --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="street" id="qc-carrier-street" placeholder="Rua, Avenida, Travessa..." class="form-input">
                        </div>

                        {{-- Número --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nº</label>
                            <input type="text" name="number" id="qc-carrier-number" placeholder="000" class="form-input">
                        </div>

                        {{-- Complemento --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complement" id="qc-carrier-complement" placeholder="Apto, Sala, Bloco..." class="form-input">
                        </div>
                    </div>

                    <div class="qc-grid qc-grid-23" style="margin-bottom: 1.25rem;">
                        {{-- Cidade --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="city" id="qc-carrier-city" placeholder="Cidade..." class="form-input">
                        </div>

                        {{-- Estado --}}
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Estado (UF)</label>
                            <select name="state" id="qc-carrier-state" class="form-select">
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
                <div id="qc-carrier-error" style="display:none; background:var(--red-bg); color:var(--red); border:1px solid var(--red-bg); border-radius:var(--r-md); padding:0.75rem 1rem; font-size:0.875rem; margin-top:1rem;">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <span id="qc-carrier-error-msg"></span>
                </div>

            </form>
        </div>

        {{-- ── Footer: Ações ── --}}
        <div style="padding: 1.25rem 2rem; border-top: 1px solid var(--border); background: var(--bg-surface); display:flex; justify-content:flex-end; gap:1rem; flex-shrink:0;">
            <button type="button" onclick="toggleCarrierModal()" class="btn btn-secondary">
                <i class="fa-solid fa-times mr-2"></i> Cancelar
            </button>
            <button type="button" id="qc-carrier-submit-btn" onclick="submitQuickCarrier()" class="btn btn-primary" style="background:var(--blue); border-color:var(--blue); box-shadow: 0 8px 16px -4px var(--blue-bg);">
                <i class="fa-solid fa-check mr-2"></i> Salvar Transportadora
            </button>
        </div>

    </div>
</div>

<script>
(function () {
    // Move modal para o body raiz, evitando ser cortado por z-index de pai
    const modal = document.getElementById('carrier-quick-create-modal');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
})();

/* ── Toggle ────────────────────────── */
function toggleCarrierModal() {
    const m = document.getElementById('carrier-quick-create-modal');
    const isOpen = m.style.display !== 'none';
    m.style.display = isOpen ? 'none' : 'flex';
    if (!isOpen) {
        document.getElementById('qc-carrier-error').style.display = 'none';
        // Inicializa as máscaras IMask dentro do modal
        if (typeof window.initMasks === 'function') {
            window.initMasks(m);
        }
    }
}

/* ── Máscara Placa ─────────────────── */
function qcCarrierMaskPlate(input) {
    let v = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (v.length > 3) {
        v = v.substring(0, 3) + '-' + v.substring(3, 7);
    }
    input.value = v;
}

/* ── Máscara IE (Inscrição Estadual) ── */
function qcCarrierMaskIE(input) {
    // Remove tudo que não seja dígito ou ponto
    let v = input.value.replace(/[^\d.]/g, '');
    input.value = v;
}

/* ── ViaCEP ────────────────────────── */
async function qcCarrierFetchCep(cep) {
    const raw = (cep || '').replace(/\D/g, '');
    if (raw.length !== 8) return;

    const btn = document.getElementById('qc-carrier-cep-btn');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
    btn.disabled = true;

    try {
        const res  = await fetch(`https://viacep.com.br/ws/${raw}/json/`);
        const data = await res.json();
        if (!data.erro) {
            document.getElementById('qc-carrier-street').value      = data.logradouro || '';
            document.getElementById('qc-carrier-neighborhood').value = data.bairro || '';
            document.getElementById('qc-carrier-city').value         = data.localidade || '';
            const stateEl = document.getElementById('qc-carrier-state');
            if (stateEl) stateEl.value = data.uf || '';
            document.getElementById('qc-carrier-number').focus();
        }
    } catch (_) {}

    btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Buscar';
    btn.disabled = false;
}

/* ── Submit AJAX ───────────────────── */
async function submitQuickCarrier() {
    const form = document.getElementById('quick-carrier-form');
    const btn  = document.getElementById('qc-carrier-submit-btn');
    const errDiv = document.getElementById('qc-carrier-error');
    const errMsg = document.getElementById('qc-carrier-error-msg');

    errDiv.style.display = 'none';

    // Validação de campos obrigatórios no frontend
    const nameVal = document.getElementById('qc-carrier-name').value.trim();
    if (!nameVal) {
        errMsg.textContent = 'O campo Nome / Razão Social é obrigatório.';
        errDiv.style.display = 'block';
        document.getElementById('qc-carrier-name').focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Salvando...';

    try {
        const res  = await fetch('{{ route("carriers.store") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await res.json();

        if (data.id) {
            // Adiciona ao(s) select(s) de transportadora e aciona change
            document.querySelectorAll('.carrier-select').forEach(sel => {
                const label = data.cnpj ? `${data.name} — ${data.cnpj}` : data.name;
                const opt = new Option(label, data.id);
                opt.setAttribute('data-name',      data.name || '');
                opt.setAttribute('data-cnpj',      data.cnpj || '');
                opt.setAttribute('data-state_reg', data.state_registration || '');
                opt.setAttribute('data-street',    data.street || '');
                opt.setAttribute('data-number',    data.number || '');
                opt.setAttribute('data-city',      data.city || '');
                opt.setAttribute('data-state',     data.state || '');
                opt.setAttribute('data-plate',     data.vehicle_plate || '');
                opt.setAttribute('data-uf',        data.vehicle_uf || '');
                sel.add(opt);
                sel.value = data.id;
                sel.dispatchEvent(new Event('change'));
            });

            // Adiciona a nova transportadora na tabela do carrier picker (para aparecer imediatamente)
            const tbody = document.querySelector('#carrier-picker-table tbody');
            if (tbody) {
                const tr = document.createElement('tr');
                tr.className = 'carrier-row';
                tr.dataset.id    = data.id;
                tr.dataset.name  = (data.name || '').toLowerCase();
                tr.dataset.cnpj  = (data.cnpj || '').toLowerCase();
                tr.dataset.city  = (data.city || '').toLowerCase();
                tr.dataset.state = (data.state || '').toLowerCase();
                tr.innerHTML = `
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">${data.name}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">IE: ${data.state_registration || 'Não informada'}</div>
                    </td>
                    <td><div style="font-weight:500; color:var(--text-secondary); font-family:monospace;">${data.cnpj || 'Sem CNPJ'}</div></td>
                    <td><span class="badge" style="background:var(--bg-hover); color:var(--text-secondary); font-size:0.75rem; font-weight:600;">${(data.city || 'Sem cidade')}${data.state ? ' / ' + data.state : ''}</span></td>
                    <td><span style="font-family:monospace; font-size:0.85rem; font-weight:600; color:var(--text-primary);">${data.vehicle_plate || '—'}</span></td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-primary btn-sm btn-select-carrier"
                            data-id="${data.id}"
                            data-name="${data.name || ''}"
                            data-cnpj="${data.cnpj || ''}"
                            data-state_reg="${data.state_registration || ''}"
                            data-street="${data.street || ''}"
                            data-number="${data.number || ''}"
                            data-city="${data.city || ''}"
                            data-state="${data.state || ''}"
                            data-plate="${data.vehicle_plate || ''}"
                            data-uf="${data.vehicle_uf || ''}"
                            style="padding:0.4rem 0.8rem; font-size:0.8rem; justify-content:center; width:100%;">
                            Selecionar
                        </button>
                    </td>`;
                tbody.appendChild(tr);
            }

            toggleCarrierModal();
            form.reset();

        } else if (data.errors) {
            const msgs = Object.values(data.errors).flat().join(' | ');
            errMsg.textContent = msgs;
            errDiv.style.display = 'block';
        } else {
            errMsg.textContent = 'Erro ao cadastrar transportadora. Verifique os dados e tente novamente.';
            errDiv.style.display = 'block';
        }

    } catch (e) {
        errMsg.textContent = 'Erro de conexão. Verifique sua internet e tente novamente.';
        errDiv.style.display = 'block';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Salvar Transportadora';
}
</script>
