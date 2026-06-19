@extends('layouts.app')

@section('title', 'Emissão NF-e')
@section('page-title', 'Emissão de Nota Fiscal')
@section('page-subtitle', 'Preencha os dados abaixo para gerar um novo documento fiscal')

@push('styles')
<style>
    .active-tab-btn {
        background: var(--accent) !important;
        color: var(--accent-fg) !important;
        border-color: var(--accent) !important;
    }
    .tab-btn {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .tab-btn:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
    }
    .item-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: var(--r-md);
        background: var(--bg-surface);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .item-card:hover {
        border-color: var(--border-strong) !important;
        box-shadow: var(--shadow-md);
    }
    .fiscal-tab-content {
        animation: fadeIn 0.2s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tax-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="w-full">
    <form method="POST" action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}" id="invoice-form" class="anim-entrance">
        @csrf
        @if(isset($invoice)) @method('PUT') @endif
        
        <div class="flex flex-col gap-6 lg:gap-10 pb-36">

            {{-- Card Unificado: Dados Gerais e Destinatário --}}
            <div class="card shadow-md">
                <div class="card-header bg-surface" style="border-bottom: 2px solid var(--accent-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1.5rem 2rem;">
                    <div class="flex items-center gap-3">
                        <div style="width:36px; height:36px; background:var(--accent-subtle); color:var(--accent); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="m-0" style="font-family:'Outfit'; font-weight: 800; letter-spacing: -0.01em; font-size: 1.2rem; line-height: 1.2;">Dados Gerais e Destinatário</h3>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.15rem;">SÉRIE 001 | NF-e Nº {{ $invoice->number ?? $number }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2 relative items-center flex-wrap" style="max-width: 100%;">
                        <div class="relative" style="min-width: 200px;">
                            <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem; z-index: 1;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <select id="customer-select" class="form-control customer-select" style="width: 100%; max-width: 280px; font-size: 0.85rem; height: 42px; padding: 0 1rem 0 2.2rem; border-radius: var(--r-md); background: var(--bg-base);" onchange="fillCustomerData(this)">
                                <option value="">Vincular Cliente...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" 
                                            data-name="{{ $c->name }}" 
                                            data-document="{{ $c->document }}" 
                                            data-email="{{ $c->email }}" 
                                            data-phone="{{ $c->phone }}" 
                                            data-address="{{ $c->address }}"
                                            data-city="{{ $c->city }}"
                                            data-state="{{ $c->state }}"
                                            data-zip="{{ $c->zip_code }}">
                                        {{ $c->name }} ({{ $c->document }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="btn-open-customer-picker" class="btn btn-secondary" style="padding: 0 0.75rem; height: 42px;" title="Buscar cliente (lupa)">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button type="button" onclick="toggleCustomerModal()" class="btn btn-secondary" style="padding: 0 0.75rem; height: 42px;" title="Cadastrar novo cliente">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-8 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        {{-- Linha 1: Dados do Protocolo --}}
                        <div class="form-group">
                            <label class="form-label">Número da NF (Sequencial)</label>
                            <input type="text" value="{{ $invoice->number ?? $number }}" readonly class="form-control text-center" style="background: var(--bg-hover); font-family: 'Outfit'; font-weight: 800; color: var(--accent); height: 48px; border-style: dashed;">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tipo de Operação</label>
                            <select name="type" required class="form-control" style="font-weight: 600; height: 48px;">
                                <option value="saida" {{ (isset($invoice) && $invoice->type === 'saida') ? 'selected' : '' }}>↑ Saída (Venda/Remessa)</option>
                                <option value="entrada" {{ (isset($invoice) && $invoice->type === 'entrada') ? 'selected' : '' }}>↓ Entrada (Compra/Devolução)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Data de Emissão</label>
                            <input type="date" name="issued_at" value="{{ isset($invoice) ? $invoice->issued_at->format('Y-m-d') : date('Y-m-d') }}" class="form-control" style="height: 48px;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Vencimento Financeiro</label>
                            <input type="date" name="due_date" value="{{ isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}" class="form-control" style="height: 48px;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Forma de Recebimento</label>
                            <select name="payment_method" required class="form-control" style="height: 48px;">
                                @foreach(['pix' => 'PIX (Instantâneo)', 'boleto' => 'Boleto Bancário', 'dinheiro' => 'Dinheiro / Espécie', 'cartao_credito' => 'Cartão de Crédito', 'cartao_debito' => 'Cartão de Débito'] as $val => $lab)
                                    <option value="{{ $val }}" {{ (isset($invoice) && $invoice->payment_method === $val) ? 'selected' : '' }}>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Linha 2: Dados do Destinatário --}}
                        <div class="form-group lg:col-span-2">
                            <label class="form-label">Nome / Razão Social <span style="color:var(--red);">*</span></label>
                            <input type="text" name="recipient_name" id="recipient_name" value="{{ $invoice->recipient_name ?? '' }}" required class="form-control" placeholder="Ex: Lucas Ferreira Ltda" style="height: 48px; font-weight: 500;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CPF / CNPJ <span style="color:var(--red);">*</span></label>
                            <input type="text" name="recipient_document" id="recipient_document" value="{{ $invoice->recipient_document ?? '' }}" required class="form-control" placeholder="00.000.000/0001-00" style="height: 48px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail para Faturamento</label>
                            <input type="email" name="recipient_email" id="recipient_email" value="{{ $invoice->recipient_email ?? '' }}" class="form-control" placeholder="financeiro@empresa.com" style="height: 48px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefone de Contato</label>
                            <input type="text" name="recipient_phone" id="recipient_phone" value="{{ $invoice->recipient_phone ?? '' }}" class="form-control" placeholder="(00) 00000-0000" style="height: 48px;">
                        </div>

                        {{-- Linha 3: Fornecedor e Endereço --}}
                        <div class="form-group lg:col-span-2">
                             <label class="form-label">Fornecedor Associado (Opcional)</label>
                             <div style="display:flex; gap:0.4rem;">
                                 <select name="supplier_id" class="form-control" style="flex:1; height: 48px;">
                                     <option value="">Nenhum fornecedor vinculado</option>
                                     @foreach($suppliers as $s)
                                         <option value="{{ $s->id }}" {{ (isset($invoice) && $invoice->supplier_id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                     @endforeach
                                 </select>
                                 <button type="button" class="btn btn-secondary" data-open-supplier-modal style="padding:0 .75rem; height: 48px;" title="Novo Fornecedor">
                                     <i class="fa-solid fa-plus"></i>
                                 </button>
                             </div>
                         </div>
                        <div class="form-group lg:col-span-3">
                            <label class="form-label">Logradouro / Número</label>
                            <input type="text" name="recipient_address" id="recipient_address" value="{{ $invoice->recipient_address ?? '' }}" class="form-control" placeholder="Rua, Número, Bairro" style="height: 48px;">
                        </div>

                        {{-- Linha 4: Restante do Endereço --}}
                        <div class="form-group">
                            <label class="form-label">CEP</label>
                            <input type="text" name="recipient_zip" id="recipient_zip" value="{{ $invoice->recipient_zip ?? '' }}" class="form-control" placeholder="00000-000" style="height: 48px;">
                        </div>
                        <div class="form-group lg:col-span-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="recipient_city" id="recipient_city" value="{{ $invoice->recipient_city ?? '' }}" class="form-control" style="height: 48px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estado (UF)</label>
                            <input type="text" name="recipient_state" id="recipient_state" value="{{ $invoice->recipient_state ?? '' }}" maxlength="2" class="form-control text-center" placeholder="UF" style="height: 48px;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Card --}}
            <div class="card shadow-lg" style="overflow: visible; border: none;">
                <div class="card-header bg-surface flex-mobile-col" style="padding: 1.5rem 2.5rem; border-bottom: 1px solid var(--border);">
                    <div class="flex items-center gap-2">
                        <div style="width:36px; height:36px; background:var(--blue-bg); color:var(--blue); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="m-0" style="font-family:'Outfit'; font-weight: 800;">Itens e Mercadorias (Tributação 2026)</h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="button" onclick="expandAll()" class="btn btn-secondary btn-sm" style="font-size:0.8rem; padding: 0.5rem 1rem;">
                            <i class="fa-solid fa-angles-down mr-1"></i> Expandir Todos
                        </button>
                        <button type="button" onclick="collapseAll()" class="btn btn-secondary btn-sm" style="font-size:0.8rem; padding: 0.5rem 1rem;">
                            <i class="fa-solid fa-angles-up mr-1"></i> Recolher Todos
                        </button>
                        <button type="button" onclick="addItem()" class="btn btn-primary w-full md:w-auto" style="padding: 0.65rem 1.5rem; border-radius: var(--r-md);">
                            <i class="fa-solid fa-plus-circle mr-1"></i> Incluir Novo Item
                        </button>
                    </div>
                </div>

                {{-- Container for item cards --}}
                <div class="p-6 sm:p-4 flex flex-col gap-6" id="items-container">
                    {{-- Dynamically generated --}}
                </div>

                {{-- ══ CARD: TRANSPORTADOR / VOLUMES ══ --}}
                <div class="p-6 sm:p-4" style="border-top: 1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem;">
                        <div style="width:32px; height:32px; background:var(--accent-subtle); color:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <h4 style="margin:0; font-family:'Outfit'; font-size:1rem; font-weight:800; color:var(--text-primary);">
                            Transportador / Volumes Transportados
                        </h4>
                        <span style="font-size:0.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Seção 6 da NF Modelo 1</span>
                    </div>

                    {{-- Busca de transportadora cadastrada --}}
                    <div style="margin-bottom:1.5rem; padding:1rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:var(--r-md);">
                        <label class="form-label" style="margin-bottom:0.5rem;">
                            <i class="fa-solid fa-magnifying-glass mr-1" style="color:var(--text-muted);"></i>
                            Vincular Transportadora Cadastrada
                        </label>
                        <div style="display:flex; gap:0.75rem; align-items:center;">
                            <select id="carrier-select" name="carrier_id" class="form-select" style="flex:1; max-width:400px;" onchange="fillCarrierData(this)">
                                <option value="">— Selecionar transportadora do cadastro... —</option>
                                @foreach($carriers ?? [] as $carrier)
                                    <option value="{{ $carrier->id }}"
                                        data-name="{{ $carrier->name }}"
                                        data-cnpj="{{ $carrier->cnpj }}"
                                        data-state_reg="{{ $carrier->state_registration }}"
                                        data-street="{{ $carrier->street }}"
                                        data-number="{{ $carrier->number }}"
                                        data-city="{{ $carrier->city }}"
                                        data-state="{{ $carrier->state }}"
                                        data-plate="{{ $carrier->vehicle_plate }}"
                                        data-uf="{{ $carrier->vehicle_uf }}"
                                        {{ isset($invoice) && $invoice->carrier_id == $carrier->id ? 'selected' : '' }}>
                                        {{ $carrier->name }}{{ $carrier->cnpj ? ' — ' . $carrier->cnpj : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <span style="font-size:0.8rem; color:var(--text-muted);">ou preencha manualmente abaixo</span>
                        </div>
                    </div>

                    {{-- LINHA 1: Nome, CNPJ, IE, Endereço --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" style="margin-bottom:1rem;">
                        <div class="form-group" style="margin-bottom:0; grid-column: span 2;">
                            <label class="form-label text-xs">Nome / Razão Social da Transportadora</label>
                            <input type="text" name="carrier_name" id="carrier_name" class="form-control"
                                   placeholder="Ex: Transportes Rápidos Ltda"
                                   value="{{ $invoice->carrier_name ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">CNPJ da Transportadora</label>
                            <input type="text" name="carrier_cnpj" id="carrier_cnpj" class="form-control"
                                   placeholder="00.000.000/0001-00" style="font-family:monospace;"
                                   value="{{ $invoice->carrier_cnpj ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">Inscrição Estadual</label>
                            <input type="text" name="carrier_state_reg" id="carrier_state_reg" class="form-control"
                                   placeholder="IE"
                                   value="{{ $invoice->carrier_state_reg ?? '' }}">
                        </div>
                    </div>

                    {{-- LINHA 2: Endereço, Cidade, Frete por Conta, Placa, UF --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4" style="margin-bottom:1.5rem;">
                        <div class="form-group" style="margin-bottom:0; grid-column: span 2;">
                            <label class="form-label text-xs">Endereço</label>
                            <input type="text" name="carrier_address" id="carrier_address" class="form-control"
                                   placeholder="Rua, Nº"
                                   value="{{ $invoice->carrier_address ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">Cidade</label>
                            <input type="text" name="carrier_city" id="carrier_city" class="form-control"
                                   placeholder="Cidade"
                                   value="{{ $invoice->carrier_city ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">Placa do Veículo</label>
                            <input type="text" name="vehicle_plate" id="vehicle_plate" class="form-control"
                                   placeholder="ABC-1234" style="font-family:monospace; text-transform:uppercase;" maxlength="8"
                                   value="{{ $invoice->vehicle_plate ?? '' }}">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">UF Placa</label>
                            <select name="vehicle_uf" id="vehicle_uf" class="form-select">
                                <option value="">UF</option>
                                @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                    <option value="{{ $uf }}" {{ (isset($invoice) && $invoice->vehicle_uf === $uf) ? 'selected' : '' }}>{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- LINHA 3: Frete por conta + Tipo de carga --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" style="margin-bottom:1.5rem;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">Frete por Conta de</label>
                            <select name="freight_account" id="freight_account" class="form-select">
                                <option value="0" {{ (!isset($invoice) || $invoice->freight_account == '0') ? 'selected' : '' }}>0 — Emitente</option>
                                <option value="1" {{ (isset($invoice) && $invoice->freight_account == '1') ? 'selected' : '' }}>1 — Destinatário</option>
                                <option value="2" {{ (isset($invoice) && $invoice->freight_account == '2') ? 'selected' : '' }}>2 — Terceiros</option>
                                <option value="3" {{ (isset($invoice) && $invoice->freight_account == '3') ? 'selected' : '' }}>3 — Remetente</option>
                                <option value="9" {{ (isset($invoice) && $invoice->freight_account == '9') ? 'selected' : '' }}>9 — Sem Frete</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label text-xs">Tipo de Carga</label>
                            <input type="text" name="cargo_type" id="cargo_type" class="form-control"
                                   placeholder="Ex: Carga Geral, Fracionado, Perigosa..."
                                   value="{{ $invoice->cargo_type ?? '' }}">
                        </div>
                    </div>

                    {{-- LINHA 4: VOLUMES --}}
                    <div style="border-top: 1px dashed var(--border); padding-top:1.25rem; margin-top:0.5rem;">
                        <p style="font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin:0 0 1rem 0;">
                            <i class="fa-solid fa-boxes-stacked mr-1"></i> Volumes Transportados
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Quantidade</label>
                                <input type="number" name="vol_quantity" id="vol_quantity" class="form-control text-right"
                                       placeholder="0" min="0"
                                       value="{{ $invoice->vol_quantity ?? '' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Espécie</label>
                                <input type="text" name="vol_species" id="vol_species" class="form-control"
                                       placeholder="VOLUMES, CAIXAS..."
                                       value="{{ $invoice->vol_species ?? 'VOLUMES' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Marca</label>
                                <input type="text" name="vol_brand" id="vol_brand" class="form-control"
                                       placeholder="Marca"
                                       value="{{ $invoice->vol_brand ?? '' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Número</label>
                                <input type="text" name="vol_number" id="vol_number" class="form-control"
                                       placeholder="Numeração"
                                       value="{{ $invoice->vol_number ?? '' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Peso Bruto (kg)</label>
                                <input type="number" name="vol_gross_weight" id="vol_gross_weight" class="form-control text-right"
                                       step="0.001" placeholder="0,000"
                                       value="{{ $invoice->vol_gross_weight ?? '' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label text-xs">Peso Líquido (kg)</label>
                                <input type="number" name="vol_net_weight" id="vol_net_weight" class="form-control text-right"
                                       step="0.001" placeholder="0,000"
                                       value="{{ $invoice->vol_net_weight ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Summary --}}

                <div class="p-8 sm:p-6 bg-hover grid grid-cols-1 lg:grid-cols-[1fr_1.2fr_1fr] gap-6 lg:gap-10 items-start" style="border-top: 1px solid var(--border);">
                    <div class="form-group w-full">
                        <label class="form-label" style="opacity: 0.7; font-weight:700;">Observações Adicionais (Impressas na NF)</label>
                        <textarea name="notes" rows="6" class="form-control" style="background: var(--bg-surface); font-size: 0.9rem;" placeholder="Ex: Informações sobre tributação, dados bancários para depósito, etc.">{{ $invoice->notes ?? '' }}</textarea>
                    </div>

                    {{-- Consolidated Taxes Card --}}
                    <div class="card p-5" style="border: 1px solid var(--border); background: var(--bg-surface);">
                        <h4 class="text-xs font-bold mb-4" style="text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; display:flex; align-items:center; gap:0.4rem;">
                            <i class="fa-solid fa-scale-balanced" style="color:var(--blue);"></i> Resumo de Tributos Consolidados
                        </h4>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs" style="color: var(--text-secondary);">
                            <div class="flex justify-between border-b pb-1">
                                <span>ICMS Próprio:</span>
                                <span class="font-bold text-right" id="tot-icms">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>ICMS ST:</span>
                                <span class="font-bold text-right" id="tot-icms-st">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>IPI Federal:</span>
                                <span class="font-bold text-right" id="tot-ipi">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>PIS / COFINS:</span>
                                <span class="font-bold text-right" id="tot-pis-cofins">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>ISS Municipal:</span>
                                <span class="font-bold text-right" id="tot-iss">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>CSLL / IRPJ:</span>
                                <span class="font-bold text-right" id="tot-csll-irpj">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>CPP Previdenc.:</span>
                                <span class="font-bold text-right" id="tot-cpp">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1">
                                <span>IBS / CBS / IS:</span>
                                <span class="font-bold text-right" id="tot-reform">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between border-b pb-1 col-span-2 mt-2 pt-2" style="border-top: 1px dashed var(--border); font-size: 0.8rem; color: var(--text-primary);">
                                <span class="font-bold">Total aproximado de impostos:</span>
                                <span class="font-black text-right" id="tot-all-taxes" style="color: var(--blue);">R$ 0,00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Totals --}}
                    <div class="flex flex-col gap-4 w-full">
                        <div class="flex justify-between p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary); font-weight:600;">Subtotal dos Itens:</span>
                            <span id="subtotal-display" class="font-bold" style="color: var(--text-primary);">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between items-center p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary); font-weight:600;">Desconto Financeiro (R$):</span>
                            <input type="number" name="discount" id="discount-input" step="0.01" class="form-control" style="width: 100px; text-align: right; height: 32px; padding: 0 0.5rem; font-size: 0.85rem;" oninput="calcTotals()" value="{{ isset($invoice) ? $invoice->discount : '0' }}">
                        </div>
                        <div class="flex justify-between items-center p-2 border-bottom" style="border-bottom: 1px solid var(--border);">
                            <span class="text-sm" style="color: var(--text-secondary); font-weight:600;">Frete / Encargos (R$):</span>
                            <input type="number" name="shipping" id="shipping-input" step="0.01" class="form-control" style="width: 100px; text-align: right; height: 32px; padding: 0 0.5rem; font-size: 0.85rem;" oninput="calcTotals()" value="{{ isset($invoice) ? $invoice->shipping : '0' }}">
                        </div>
                        <div class="flex justify-between mt-4 p-6 bg-accent rounded-md items-center shadow-md" style="background: var(--accent); border-radius: var(--r-md); box-shadow: 0 10px 20px -5px var(--accent-glow);">
                            <span class="font-bold text-lg" style="font-family: 'Outfit'; color: var(--accent-fg);">TOTAL NF-E:</span>
                            <span id="total-display" class="font-bold text-2xl" style="font-family: 'Outfit'; color: var(--accent-fg);">R$ 0,00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Actions --}}
            <div class="p-6 flex flex-mobile-col justify-between items-center gap-4 sticky bottom-6 shadow-lg rounded-lg" style="position: sticky; bottom: 1.5rem; z-index: 50; background: var(--bg-surface); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); box-shadow: 0 -10px 30px -10px rgba(0,0,0,0.1); border-radius: var(--r-lg); border: 1px solid var(--border);">
                <div class="hidden md:flex gap-4">
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary px-8">Descartar</a>
                </div>
                <div class="flex flex-mobile-col gap-4 w-full md:w-auto">
                    <button type="submit" name="action" value="save" class="btn btn-secondary w-full md:w-auto px-8">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Rascunho
                    </button>
                    <button type="submit" name="action" value="emit" class="btn btn-primary w-full md:w-auto px-12">
                        <i class="fa-solid fa-check-double"></i> EMITIR NOTA AGORA
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Product Template --}}
<template id="product-select-template">
    <select class="product-select form-control" style="font-size: 0.85rem; height: 36px; padding: 0 0.5rem;">
        <option value="">— Selecione o Produto —</option>
        @foreach($products as $p)
            <option value="{{ $p->id }}" 
                    data-price="{{ $p->unit_price }}" 
                    data-unit="{{ $p->unit }}"
                    data-name="{{ $p->name }}"
                    data-stock="{{ $p->quantity }}"
                    data-ncm="{{ $p->ncm }}"
                    data-cfop="{{ $p->cfop_default }}"
                    data-cest="{{ $p->cest }}"
                    
                    data-iss_rate="{{ $p->iss_rate }}"
                    data-pis_rate="{{ $p->pis_rate }}"
                    data-cofins_rate="{{ $p->cofins_rate }}"
                    data-csll_rate="{{ $p->csll_rate }}"
                    data-irpj_rate="{{ $p->irpj_rate }}"
                    data-cpp_rate="{{ $p->cpp_rate }}"
                    data-ipi_rate="{{ $p->ipi_rate }}"
                    
                    data-icms_rate="{{ $p->icms_rate }}"
                    data-icms_cst="{{ $p->icms_cst }}"
                    data-icms_orig="{{ $p->icms_orig }}"
                    data-icms_red_bc="{{ $p->icms_red_bc }}"
                    data-icms_mod_bc="{{ $p->icms_mod_bc }}"
                    
                    data-icms_st_rate="{{ $p->icms_st_rate }}"
                    data-icms_st_mva="{{ $p->icms_st_mva }}"
                    data-icms_st_cst="{{ $p->icms_st_cst }}"
                    
                    data-ibs_rate="{{ $p->ibs_rate }}"
                    data-cbs_rate="{{ $p->cbs_rate }}"
                    data-is_rate="{{ $p->is_rate }}">
                {{ $p->name }} [Est: {{ $p->quantity }}]
            </option>
        @endforeach
    </select>
</template>

@push('scripts')
<script>
let itemIndex = 0;

function fmtBR(val) {
    return 'R$ ' + parseFloat(val || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function parseVal(val) {
    if (typeof val === 'object' && val !== null) val = val.value;
    if (!val) return 0;
    
    let str = String(val).trim();
    str = str.replace(/[R$\s%]/g, '');
    
    if (str.includes(',')) {
        str = str.replace(/\./g, '').replace(',', '.');
    } else {
        str = str.replace(/\._/g, ''); 
    }
    
    return parseFloat(str) || 0;
}

function switchTab(index, tabName) {
    const parentCard = document.getElementById(`item-card-${index}`);
    if (!parentCard) return;
    
    // Hide all tab contents in this card
    parentCard.querySelectorAll('.fiscal-tab-content').forEach(el => el.style.display = 'none');
    
    // Deactivate all tab buttons in this card
    parentCard.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.className = 'px-3 py-1.5 rounded-md tab-btn';
    });
    
    // Show selected content and activate button
    const targetContent = parentCard.querySelector(`#tab-content-${tabName}-${index}`);
    if (targetContent) targetContent.style.display = 'grid';
    
    const targetBtn = parentCard.querySelector(`#tab-btn-${tabName}-${index}`);
    if (targetBtn) targetBtn.className = 'px-3 py-1.5 rounded-md active-tab-btn';
}

function toggleCardBody(index) {
    const body = document.getElementById(`card-body-${index}`);
    const chevron = document.getElementById(`chevron-icon-${index}`);
    if (!body) return;
    
    if (body.style.display === 'none') {
        body.style.display = 'flex';
        chevron.className = 'fa-solid fa-chevron-up';
    } else {
        body.style.display = 'none';
        chevron.className = 'fa-solid fa-chevron-down';
    }
}

function expandAll() {
    document.querySelectorAll('.item-card').forEach(card => {
        const index = card.id.replace('item-card-', '');
        const body = document.getElementById(`card-body-${index}`);
        const chevron = document.getElementById(`chevron-icon-${index}`);
        if (body) {
            body.style.display = 'flex';
            chevron.className = 'fa-solid fa-chevron-up';
        }
    });
}

function collapseAll() {
    document.querySelectorAll('.item-card').forEach(card => {
        const index = card.id.replace('item-card-', '');
        const body = document.getElementById(`card-body-${index}`);
        const chevron = document.getElementById(`chevron-icon-${index}`);
        if (body) {
            body.style.display = 'none';
            chevron.className = 'fa-solid fa-chevron-down';
        }
    });
}

function calcTotals(manualOverride = false) {
    let subtotal = 0;
    
    // Totals of all taxes consolidated
    let sumIcms = 0;
    let sumIcmsSt = 0;
    let sumIpi = 0;
    let sumPis = 0;
    let sumCofins = 0;
    let sumIss = 0;
    let sumCsll = 0;
    let sumIrpj = 0;
    let sumCpp = 0;
    let sumIbs = 0;
    let sumCbs = 0;
    let sumIs = 0;
    let sumIi = 0;

    document.querySelectorAll('.item-card').forEach(card => {
        const index = card.id.replace('item-card-', '');
        
        const qty = parseVal(card.querySelector('.qty-input'));
        const price = parseVal(card.querySelector('.price-input'));
        const disc = parseVal(card.querySelector('.disc-input'));
        const total = qty * price * (1 - disc / 100);
        
        card.querySelector('.total-display').textContent = fmtBR(total);
        subtotal += total;
        
        // Retrieve inputs
        const icmsRateInput = card.querySelector('.icms-rate-input');
        const icmsBaseInput = card.querySelector('.icms-base-input');
        const icmsValInput = card.querySelector('.icms-value-input');
        const icmsRedBcInput = card.querySelector('.icms-red-bc-input');
        
        const stRateInput = card.querySelector('.icms-st-rate-input');
        const stMvaInput = card.querySelector('.icms-st-mva-input');
        const stBaseInput = card.querySelector('.icms-st-base-input');
        const stValInput = card.querySelector('.icms-st-value-input');
        
        const ipiRateInput = card.querySelector('.ipi-rate-input');
        const ipiBaseInput = card.querySelector('.ipi-base-input');
        const ipiValInput = card.querySelector('.ipi-value-input');
        
        const pisRateInput = card.querySelector('.pis-rate-input');
        const pisBaseInput = card.querySelector('.pis-base-input');
        const pisValInput = card.querySelector('.pis-value-input');
        
        const cofinsRateInput = card.querySelector('.cofins-rate-input');
        const cofinsBaseInput = card.querySelector('.cofins-base-input');
        const cofinsValInput = card.querySelector('.cofins-value-input');
        
        const issRateInput = card.querySelector('.iss-rate-input');
        const issBaseInput = card.querySelector('.iss-base-input');
        const issValInput = card.querySelector('.iss-value-input');
        
        const csllRateInput = card.querySelector('.csll-rate-input');
        const csllBaseInput = card.querySelector('.csll-base-input');
        const csllValInput = card.querySelector('.csll-value-input');
        
        const irpjRateInput = card.querySelector('.irpj-rate-input');
        const irpjBaseInput = card.querySelector('.irpj-base-input');
        const irpjValInput = card.querySelector('.irpj-value-input');
        
        const cppRateInput = card.querySelector('.cpp-rate-input');
        const cppBaseInput = card.querySelector('.cpp-base-input');
        const cppValInput = card.querySelector('.cpp-value-input');

        const ibsRateInput = card.querySelector('.ibs-rate-input');
        const ibsBaseInput = card.querySelector('.ibs-base-input');
        const ibsValInput = card.querySelector('.ibs-value-input');

        const cbsRateInput = card.querySelector('.cbs-rate-input');
        const cbsBaseInput = card.querySelector('.cbs-base-input');
        const cbsValInput = card.querySelector('.cbs-value-input');

        const isRateInput = card.querySelector('.is-rate-input');
        const isBaseInput = card.querySelector('.is-base-input');
        const isValInput = card.querySelector('.is-value-input');

        const iiRateInput = card.querySelector('.ii-rate-input');
        const iiBaseInput = card.querySelector('.ii-base-input');
        const iiValInput = card.querySelector('.ii-value-input');
        const iiDesp = parseVal(card.querySelector('.ii-desp-input'));
        const iiIof = parseVal(card.querySelector('.ii-iof-input'));

        if (!manualOverride) {
            // Apply standard bases equal to item total
            const redIcms = parseVal(icmsRedBcInput);
            const icmsBase = total * (1 - redIcms/100);
            icmsBaseInput.value = icmsBase.toFixed(2);

            const icmsRate = parseVal(icmsRateInput);
            const icmsVal = icmsBase * icmsRate / 100;
            icmsValInput.value = icmsVal.toFixed(2);

            const ipiRate = parseVal(ipiRateInput);
            const ipiVal = total * ipiRate / 100;
            ipiBaseInput.value = total.toFixed(2);
            ipiValInput.value = ipiVal.toFixed(2);

            const mva = parseVal(stMvaInput);
            // ST Base includes product total + IPI
            const stBase = (total + ipiVal) * (1 + mva/100);
            stBaseInput.value = stBase.toFixed(2);

            const stRate = parseVal(stRateInput);
            const stValCalculated = (stBase * stRate / 100) - icmsVal;
            stValInput.value = Math.max(0, stValCalculated).toFixed(2);
            
            pisBaseInput.value = total.toFixed(2);
            cofinsBaseInput.value = total.toFixed(2);
            issBaseInput.value = total.toFixed(2);
            csllBaseInput.value = total.toFixed(2);
            irpjBaseInput.value = total.toFixed(2);
            cppBaseInput.value = total.toFixed(2);
            ibsBaseInput.value = total.toFixed(2);
            cbsBaseInput.value = total.toFixed(2);
            isBaseInput.value = total.toFixed(2);
            iiBaseInput.value = total.toFixed(2);

            pisValInput.value = (parseVal(pisBaseInput) * parseVal(pisRateInput) / 100).toFixed(2);
            cofinsValInput.value = (parseVal(cofinsBaseInput) * parseVal(cofinsRateInput) / 100).toFixed(2);
            issValInput.value = (parseVal(issBaseInput) * parseVal(issRateInput) / 100).toFixed(2);
            csllValInput.value = (parseVal(csllBaseInput) * parseVal(csllRateInput) / 100).toFixed(2);
            irpjValInput.value = (parseVal(irpjBaseInput) * parseVal(irpjRateInput) / 100).toFixed(2);
            cppValInput.value = (parseVal(cppBaseInput) * parseVal(cppRateInput) / 100).toFixed(2);
            ibsValInput.value = (parseVal(ibsBaseInput) * parseVal(ibsRateInput) / 100).toFixed(2);
            cbsValInput.value = (parseVal(cbsBaseInput) * parseVal(cbsRateInput) / 100).toFixed(2);
            isValInput.value = (parseVal(isBaseInput) * parseVal(isRateInput) / 100).toFixed(2);
            
            // II value = base * rate / 100 + desp + iof
            const iiVal = (parseVal(iiBaseInput) * parseVal(iiRateInput) / 100) + iiDesp + iiIof;
            iiValInput.value = iiVal.toFixed(2);
        }

        // Sum up tax values for consolidated card
        sumIcms += parseVal(icmsValInput);
        sumIcmsSt += parseVal(stValInput);
        sumIpi += parseVal(ipiValInput);
        sumPis += parseVal(pisValInput);
        sumCofins += parseVal(cofinsValInput);
        sumIss += parseVal(issValInput);
        sumCsll += parseVal(csllValInput);
        sumIrpj += parseVal(irpjValInput);
        sumCpp += parseVal(cppValInput);
        sumIbs += parseVal(ibsValInput);
        sumCbs += parseVal(cbsValInput);
        sumIs += parseVal(isValInput);
        sumIi += parseVal(iiValInput);

        // Update active tax badges in card header
        document.getElementById(`badge-icms-${index}`).textContent = `ICMS: ${parseVal(icmsRateInput).toFixed(1)}%`;
        document.getElementById(`badge-st-${index}`).textContent = `ST: ${parseVal(stRateInput).toFixed(1)}% (MVA: ${parseVal(stMvaInput).toFixed(1)}%)`;
        document.getElementById(`badge-ipi-${index}`).textContent = `IPI: ${parseVal(ipiRateInput).toFixed(1)}%`;
        document.getElementById(`badge-pis-cof-${index}`).textContent = `PIS/COF: ${parseVal(pisRateInput).toFixed(1)}% / ${parseVal(cofinsRateInput).toFixed(1)}%`;
        document.getElementById(`badge-ibs-cbs-${index}`).textContent = `IBS/CBS: ${parseVal(ibsRateInput).toFixed(1)}% / ${parseVal(cbsRateInput).toFixed(1)}%`;
    });

    // Update Consolidated totals
    document.getElementById('tot-icms').textContent = fmtBR(sumIcms);
    document.getElementById('tot-icms-st').textContent = fmtBR(sumIcmsSt);
    document.getElementById('tot-ipi').textContent = fmtBR(sumIpi);
    document.getElementById('tot-pis-cofins').textContent = fmtBR(sumPis + sumCofins);
    document.getElementById('tot-iss').textContent = fmtBR(sumIss);
    document.getElementById('tot-csll-irpj').textContent = fmtBR(sumCsll + sumIrpj);
    document.getElementById('tot-cpp').textContent = fmtBR(sumCpp);
    document.getElementById('tot-reform').textContent = fmtBR(sumIbs + sumCbs + sumIs);
    
    const allTaxes = sumIcms + sumIcmsSt + sumIpi + sumPis + sumCofins + sumIss + sumCsll + sumIrpj + sumCpp + sumIbs + sumCbs + sumIs + sumIi;
    document.getElementById('tot-all-taxes').textContent = fmtBR(allTaxes);

    const discountTotal = parseVal(document.getElementById('discount-input'));
    const shippingTotal = parseVal(document.getElementById('shipping-input'));
    // Brazilian NFe total includes ICMS ST, IPI and II taxes
    const grandTotal = subtotal - discountTotal + shippingTotal + sumIcmsSt + sumIpi + sumIi;
    
    document.getElementById('subtotal-display').textContent = fmtBR(subtotal);
    document.getElementById('total-display').textContent = fmtBR(grandTotal);
}

function addItem(data = {}) {
    const i = itemIndex++;
    const container = document.getElementById('items-container');
    const card = document.createElement('div');
    
    card.className = 'item-card';
    card.id = `item-card-${i}`;

    const selectHtml = document.getElementById('product-select-template').innerHTML;
    
    card.innerHTML = `
        {{-- Card Header --}}
        <div class="p-4 bg-hover flex justify-between items-center" style="border-bottom: 1px solid var(--border);">
            <div class="flex items-center gap-4 flex-wrap" style="flex: 1;">
                <span class="badge badge-secondary" style="font-family:'Outfit'; font-weight:800; font-size: 0.9rem; padding:0.4rem 0.6rem;">Item #${i + 1}</span>
                <div style="flex: 1; min-width: 250px; max-width: 320px; display: flex; gap: 0.4rem; align-items: center;">
                    <input type="hidden" name="items[${i}][product_id]" class="product-id-input" value="${data.product_id || ''}">
                    <input type="text" id="invoice-product-display-${i}" readonly class="form-control" style="flex: 1; height: 36px; background: var(--bg-hover); cursor: pointer; font-size: 0.85rem;" placeholder="Selecionar produto..." onclick="document.querySelector('.btn-open-invoice-product-picker[data-row=\'${i}\']').click()">
                    
                    <div style="display: none;">
                        ${selectHtml.replace(/class="product-select/g, `name="items[${i}][product_id_select]" class="product-select form-control w-full`)}
                    </div>
                    
                    <button type="button" class="btn btn-secondary btn-open-invoice-product-picker" data-row="${i}" style="padding: 0 0.65rem; height: 36px;" title="Buscar produto (lupa)">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                {{-- Quick badges showing active rates --}}
                <div class="flex gap-2 flex-wrap" id="tax-badges-${i}">
                    <span class="tax-badge" style="background:var(--blue-bg); color:var(--blue);" id="badge-icms-${i}">ICMS: --%</span>
                    <span class="tax-badge" style="background:var(--orange-bg); color:var(--orange);" id="badge-st-${i}">ST: --% (MVA: --%)</span>
                    <span class="tax-badge" style="background:var(--red-bg); color:var(--red);" id="badge-ipi-${i}">IPI: --%</span>
                    <span class="tax-badge" style="background:var(--accent-subtle); color:var(--text-secondary);" id="badge-pis-cof-${i}">PIS/COF: --% / --%</span>
                    <span class="tax-badge" style="background:var(--green-bg); color:var(--green);" id="badge-ibs-cbs-${i}">IBS/CBS: --% / --%</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleCardBody(${i})" style="padding: 0.4rem 0.60rem;">
                    <i class="fa-solid fa-chevron-up" id="chevron-icon-${i}"></i>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('item-card-${i}').remove(); calcTotals();" style="color: var(--red); padding: 0.4rem 0.60rem;">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
        
        {{-- Card Body --}}
        <div class="p-6 flex flex-col gap-6" id="card-body-${i}">
            {{-- Section 1: Product info / Pricing --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="form-group col-span-1 md:col-span-2">
                    <label class="form-label text-xs">Descrição Personalizada</label>
                    <input type="text" name="items[${i}][description]" required placeholder="Nome/Descrição do produto na nota..." class="desc-input form-input form-control" value="${data.description || ''}">
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">NCM</label>
                    <input type="text" name="items[${i}][ncm]" class="form-input form-control text-center" value="${data.ncm || '0000.00.00'}" placeholder="0000.00.00">
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">CFOP</label>
                    <input type="text" name="items[${i}][cfop]" class="form-input form-control text-center" value="${data.cfop || '5.102'}" placeholder="5.102">
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">Unid.</label>
                    <input type="text" name="items[${i}][unit]" class="unit-input form-input form-control text-center" value="${data.unit || 'un'}" placeholder="un">
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">Quantidade</label>
                    <input type="number" name="items[${i}][quantity]" step="0.001" required class="qty-input form-input form-control text-right font-bold" value="${data.quantity || 1}" oninput="calcTotals()">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label text-xs">Preço Unitário (R$)</label>
                    <input type="number" name="items[${i}][unit_price]" step="0.01" required class="price-input form-input form-control text-right" value="${data.unit_price || 0}" oninput="calcTotals()">
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">Desconto (%)</label>
                    <input type="number" name="items[${i}][discount]" step="0.01" class="disc-input form-input form-control text-right" style="color: var(--red);" value="${data.discount || 0}" oninput="calcTotals()">
                </div>
                <div class="form-group col-span-1 md:col-span-2 flex flex-col justify-center items-end p-4 bg-hover rounded-md" style="background:var(--bg-hover);">
                    <span class="text-xs text-muted font-semibold">VALOR TOTAL DO ITEM</span>
                    <span class="text-2xl font-black total-display" style="font-family:'Outfit'; color: var(--text-primary);">R$ 0,00</span>
                </div>
            </div>

            {{-- Section 2: Fiscal Tabbed Section --}}
            <div style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1rem;">
                    <h4 class="text-sm font-bold" style="font-family:'Outfit'; color:var(--text-secondary); margin:0;">
                        <i class="fa-solid fa-calculator mr-1"></i> Detalhamento Fiscal e Alíquotas
                    </h4>
                    {{-- Tabs navigation --}}
                    <div class="flex gap-2 flex-wrap bg-hover p-1 rounded-md text-xs font-bold" style="background: var(--bg-hover); padding: 4px;">
                        <button type="button" class="px-3 py-1.5 rounded-md active-tab-btn" style="border:none;" id="tab-btn-icms-${i}" onclick="switchTab(${i}, 'icms')">ICMS / ICMS ST</button>
                        <button type="button" class="px-3 py-1.5 rounded-md tab-btn" style="border:none;" id="tab-btn-ipi-${i}" onclick="switchTab(${i}, 'ipi')">IPI / PIS / COFINS</button>
                        <button type="button" class="px-3 py-1.5 rounded-md tab-btn" style="border:none;" id="tab-btn-retencoes-${i}" onclick="switchTab(${i}, 'retencoes')">ISS / CSLL / IRPJ / CPP</button>
                        <button type="button" class="px-3 py-1.5 rounded-md tab-btn" style="border:none;" id="tab-btn-reforma-${i}" onclick="switchTab(${i}, 'reforma')">Reforma 2026 / Importação</button>
                    </div>
                </div>

                {{-- TAB 1: ICMS & ICMS ST --}}
                <div class="fiscal-tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="tab-content-icms-${i}">
                    <div class="form-group">
                        <label class="form-label text-xs">Origem da Mercadoria</label>
                        <select name="items[${i}][icms_orig]" class="form-select form-control text-xs orig-input" onchange="calcTotals()">
                            <option value="0" ${data.icms_orig == 0 ? 'selected' : ''}>0 - Nacional</option>
                            <option value="1" ${data.icms_orig == 1 ? 'selected' : ''}>1 - Estrangeira - Importação Direta</option>
                            <option value="2" ${data.icms_orig == 2 ? 'selected' : ''}>2 - Estrangeira - Mercado Interno</option>
                            <option value="3" ${data.icms_orig == 3 ? 'selected' : ''}>3 - Nacional - Conteúdo Imp. > 40%</option>
                            <option value="4" ${data.icms_orig == 4 ? 'selected' : ''}>4 - Nacional - Produção Básica</option>
                            <option value="5" ${data.icms_orig == 5 ? 'selected' : ''}>5 - Nacional - Conteúdo Imp. <= 40%</option>
                            <option value="6" ${data.icms_orig == 6 ? 'selected' : ''}>6 - Estrangeira - Importação Direta (Sem Similar)</option>
                            <option value="7" ${data.icms_orig == 7 ? 'selected' : ''}>7 - Estrangeira - Mercado Interno (Sem Similar)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">CST ICMS</label>
                        <select name="items[${i}][icms_cst]" class="form-select form-control text-xs icms-cst-input" onchange="calcTotals()">
                            <option value="00" ${data.icms_cst == '00' ? 'selected' : ''}>00 - Tributada integralmente</option>
                            <option value="10" ${data.icms_cst == '10' ? 'selected' : ''}>10 - Tributada e com cobrança de ST</option>
                            <option value="20" ${data.icms_cst == '20' ? 'selected' : ''}>20 - Com redução de BC</option>
                            <option value="30" ${data.icms_cst == '30' ? 'selected' : ''}>30 - Isenta/Não Trib. com cobrança de ST</option>
                            <option value="40" ${data.icms_cst == '40' ? 'selected' : ''}>40 - Isenta</option>
                            <option value="41" ${data.icms_cst == '41' ? 'selected' : ''}>41 - Não tributada</option>
                            <option value="50" ${data.icms_cst == '50' ? 'selected' : ''}>50 - Suspensão</option>
                            <option value="51" ${data.icms_cst == '51' ? 'selected' : ''}>51 - Diferimento</option>
                            <option value="60" ${data.icms_cst == '60' ? 'selected' : ''}>60 - ICMS cobrado anteriormente por ST</option>
                            <option value="70" ${data.icms_cst == '70' ? 'selected' : ''}>70 - Redução de BC e cobrança de ST</option>
                            <option value="90" ${data.icms_cst == '90' ? 'selected' : ''}>90 - Outras</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Modalidade Determinação BC</label>
                        <select name="items[${i}][icms_mod_bc]" class="form-select form-control text-xs icms-mod-bc-input">
                            <option value="3" ${data.icms_mod_bc == 3 ? 'selected' : ''}>3 - Valor da operação</option>
                            <option value="0" ${data.icms_mod_bc == 0 ? 'selected' : ''}>0 - Margem Valor Agregado (%)</option>
                            <option value="1" ${data.icms_mod_bc == 1 ? 'selected' : ''}>1 - Pauta (Valor)</option>
                            <option value="2" ${data.icms_mod_bc == 2 ? 'selected' : ''}>2 - Preço Tabelado Máx. (Valor)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Redução de BC (%)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_red_bc]" class="form-input form-control text-right icms-red-bc-input" value="${data.icms_red_bc || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs font-semibold">BC ICMS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_base]" class="form-input form-control text-right icms-base-input" value="${data.icms_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs font-semibold">Alíquota ICMS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_rate]" class="form-input form-control text-right icms-rate-input" value="${(data.icms_rate !== undefined && data.icms_rate !== '') ? data.icms_rate : 18}" oninput="calcTotals()">
                    </div>
                    <div class="form-group col-span-1 md:col-span-2">
                        <label class="form-label text-xs font-semibold">Valor do ICMS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_value]" class="form-input form-control text-right icms-value-input" value="${data.icms_value || 0}" oninput="calcTotals(true)">
                    </div>
                    
                    {{-- ST --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST ICMS ST</label>
                        <select name="items[${i}][icms_st_cst]" class="form-select form-control text-xs icms-st-cst-input">
                            <option value="10" ${data.icms_st_cst == '10' ? 'selected' : ''}>10 - Tributada com ST</option>
                            <option value="30" ${data.icms_st_cst == '30' ? 'selected' : ''}>30 - Isenta/Não Trib. com ST</option>
                            <option value="70" ${data.icms_st_cst == '70' ? 'selected' : ''}>70 - Redução de BC com ST</option>
                            <option value="90" ${data.icms_st_cst == '90' ? 'selected' : ''}>90 - Outros com ST</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Margem MVA ST (%)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_st_mva]" class="form-input form-control text-right icms-st-mva-input" value="${data.icms_st_mva || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC ICMS ST (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_st_base]" class="form-input form-control text-right icms-st-base-input" value="${data.icms_st_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota ICMS ST (%)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_st_rate]" class="form-input form-control text-right icms-st-rate-input" value="${data.icms_st_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group col-span-1 md:col-span-2">
                        <label class="form-label text-xs">Valor do ICMS ST (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][icms_st_value]" class="form-input form-control text-right icms-st-value-input" value="${data.icms_st_value || 0}" oninput="calcTotals(true)">
                    </div>
                </div>

                {{-- TAB 2: IPI, PIS & COFINS --}}
                <div class="fiscal-tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="tab-content-ipi-${i}" style="display:none;">
                    {{-- IPI --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST IPI</label>
                        <select name="items[${i}][ipi_cst]" class="form-select form-control text-xs ipi-cst-input">
                            <option value="50" ${data.ipi_cst == '50' ? 'selected' : ''}>50 - Saída Tributada</option>
                            <option value="00" ${data.ipi_cst == '00' ? 'selected' : ''}>00 - Entrada com Crédito</option>
                            <option value="49" ${data.ipi_cst == '49' ? 'selected' : ''}>49 - Outras Entradas</option>
                            <option value="99" ${data.ipi_cst == '99' ? 'selected' : ''}>99 - Outras Saídas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Cód. Enquadramento IPI</label>
                        <input type="text" name="items[${i}][ipi_enq]" class="form-input form-control text-center" value="${data.ipi_enq || '999'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC IPI (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ipi_base]" class="form-input form-control text-right ipi-base-input" value="${data.ipi_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota IPI (%)</label>
                        <input type="number" step="0.01" name="items[${i}][ipi_rate]" class="form-input form-control text-right ipi-rate-input" value="${data.ipi_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group col-span-1 md:col-span-4">
                        <label class="form-label text-xs">Valor do IPI (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ipi_value]" class="form-input form-control text-right ipi-value-input" value="${data.ipi_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- PIS --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST PIS</label>
                        <select name="items[${i}][pis_cst]" class="form-select form-control text-xs pis-cst-input">
                            <option value="01" ${data.pis_cst == '01' ? 'selected' : ''}>01 - Alíquota Básica</option>
                            <option value="02" ${data.pis_cst == '02' ? 'selected' : ''}>02 - Alíquota Diferenciada</option>
                            <option value="07" ${data.pis_cst == '07' ? 'selected' : ''}>07 - Operação Isenta</option>
                            <option value="99" ${data.pis_cst == '99' ? 'selected' : ''}>99 - Outras Operações</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC PIS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][pis_base]" class="form-input form-control text-right pis-base-input" value="${data.pis_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota PIS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][pis_rate]" class="form-input form-control text-right pis-rate-input" value="${(data.pis_rate !== undefined && data.pis_rate !== '') ? data.pis_rate : 1.65}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor PIS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][pis_value]" class="form-input form-control text-right pis-value-input" value="${data.pis_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- COFINS --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST COFINS</label>
                        <select name="items[${i}][cofins_cst]" class="form-select form-control text-xs cofins-cst-input">
                            <option value="01" ${data.cofins_cst == '01' ? 'selected' : ''}>01 - Alíquota Básica</option>
                            <option value="02" ${data.cofins_cst == '02' ? 'selected' : ''}>02 - Alíquota Diferenciada</option>
                            <option value="07" ${data.cofins_cst == '07' ? 'selected' : ''}>07 - Operação Isenta</option>
                            <option value="99" ${data.cofins_cst == '99' ? 'selected' : ''}>99 - Outras Operações</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC COFINS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cofins_base]" class="form-input form-control text-right cofins-base-input" value="${data.cofins_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota COFINS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][cofins_rate]" class="form-input form-control text-right cofins-rate-input" value="${(data.cofins_rate !== undefined && data.cofins_rate !== '') ? data.cofins_rate : 7.6}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor COFINS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cofins_value]" class="form-input form-control text-right cofins-value-input" value="${data.cofins_value || 0}" oninput="calcTotals(true)">
                    </div>
                </div>

                {{-- TAB 3: ISS / CSLL / IRPJ / CPP --}}
                <div class="fiscal-tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="tab-content-retencoes-${i}" style="display:none;">
                    {{-- ISS --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST ISS</label>
                        <input type="text" name="items[${i}][iss_cst]" class="form-input form-control text-center" value="${data.iss_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC ISS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][iss_base]" class="form-input form-control text-right iss-base-input" value="${data.iss_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota ISS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][iss_rate]" class="form-input form-control text-right iss-rate-input" value="${data.iss_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor ISS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][iss_value]" class="form-input form-control text-right iss-value-input" value="${data.iss_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- CSLL --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST CSLL</label>
                        <input type="text" name="items[${i}][csll_cst]" class="form-input form-control text-center" value="${data.csll_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC CSLL (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][csll_base]" class="form-input form-control text-right csll-base-input" value="${data.csll_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota CSLL (%)</label>
                        <input type="number" step="0.01" name="items[${i}][csll_rate]" class="form-input form-control text-right csll-rate-input" value="${data.csll_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor CSLL (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][csll_value]" class="form-input form-control text-right csll-value-input" value="${data.csll_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- IRPJ --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST IRPJ</label>
                        <input type="text" name="items[${i}][irpj_cst]" class="form-input form-control text-center" value="${data.irpj_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC IRPJ (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][irpj_base]" class="form-input form-control text-right irpj-base-input" value="${data.irpj_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota IRPJ (%)</label>
                        <input type="number" step="0.01" name="items[${i}][irpj_rate]" class="form-input form-control text-right irpj-rate-input" value="${data.irpj_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor IRPJ (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][irpj_value]" class="form-input form-control text-right irpj-value-input" value="${data.irpj_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- CPP --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST CPP</label>
                        <input type="text" name="items[${i}][cpp_cst]" class="form-input form-control text-center" value="${data.cpp_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC CPP (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cpp_base]" class="form-input form-control text-right cpp-base-input" value="${data.cpp_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota CPP (%)</label>
                        <input type="number" step="0.01" name="items[${i}][cpp_rate]" class="form-input form-control text-right cpp-rate-input" value="${data.cpp_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor CPP (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cpp_value]" class="form-input form-control text-right cpp-value-input" value="${data.cpp_value || 0}" oninput="calcTotals(true)">
                    </div>
                </div>

                {{-- TAB 4: REFORMA 2026 & IMPORTAÇÃO --}}
                <div class="fiscal-tab-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="tab-content-reforma-${i}" style="display:none;">
                    {{-- IBS --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST IBS</label>
                        <input type="text" name="items[${i}][ibs_cst]" class="form-input form-control text-center" value="${data.ibs_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC IBS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ibs_base]" class="form-input form-control text-right ibs-base-input" value="${data.ibs_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota IBS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][ibs_rate]" class="form-input form-control text-right ibs-rate-input" value="${(data.ibs_rate !== undefined && data.ibs_rate !== '') ? data.ibs_rate : 0.1}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor IBS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ibs_value]" class="form-input form-control text-right ibs-value-input" value="${data.ibs_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- CBS --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST CBS</label>
                        <input type="text" name="items[${i}][cbs_cst]" class="form-input form-control text-center" value="${data.cbs_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC CBS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cbs_base]" class="form-input form-control text-right cbs-base-input" value="${data.cbs_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota CBS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][cbs_rate]" class="form-input form-control text-right cbs-rate-input" value="${(data.cbs_rate !== undefined && data.cbs_rate !== '') ? data.cbs_rate : 0.9}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor CBS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][cbs_value]" class="form-input form-control text-right cbs-value-input" value="${data.cbs_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- Imposto Seletivo --}}
                    <div class="form-group">
                        <label class="form-label text-xs">CST IS</label>
                        <input type="text" name="items[${i}][is_cst]" class="form-input form-control text-center" value="${data.is_cst || '01'}">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">BC IS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][is_base]" class="form-input form-control text-right is-base-input" value="${data.is_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota IS (%)</label>
                        <input type="number" step="0.01" name="items[${i}][is_rate]" class="form-input form-control text-right is-rate-input" value="${data.is_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor IS (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][is_value]" class="form-input form-control text-right is-value-input" value="${data.is_value || 0}" oninput="calcTotals(true)">
                    </div>

                    {{-- II --}}
                    <div class="form-group">
                        <label class="form-label text-xs">BC Importação II (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ii_base]" class="form-input form-control text-right ii-base-input" value="${data.ii_base || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Alíquota II (%)</label>
                        <input type="number" step="0.01" name="items[${i}][ii_rate]" class="form-input form-control text-right ii-rate-input" value="${data.ii_rate || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Valor II (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ii_value]" class="form-input form-control text-right ii-value-input" value="${data.ii_value || 0}" oninput="calcTotals(true)">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Desp. Aduaneiras (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ii_desp]" class="form-input form-control text-right ii-desp-input" value="${data.ii_desp || 0}" oninput="calcTotals()">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">IOF II (R$)</label>
                        <input type="number" step="0.01" name="items[${i}][ii_iof]" class="form-input form-control text-right ii-iof-input" value="${data.ii_iof || 0}" oninput="calcTotals()">
                    </div>
                </div>
            </div>
        </div>
    `;

    container.appendChild(card);

    // Bind product select
    const sel = card.querySelector('select');
    if (sel) {
        sel.addEventListener('change', function() { fillProductData(this); });
        // Set initial select value if passed in data
        if (data.product_id) {
            sel.value = data.product_id;
            // Set display product name directly from option dataset to avoid triggering change event
            // which overrides database values with default values.
            const opt = sel.options[sel.selectedIndex];
            if (opt && opt.value) {
                const displayInp = document.getElementById(`invoice-product-display-${i}`);
                if (displayInp) {
                    displayInp.value = opt.getAttribute('data-name') || opt.text || '';
                }
            }
        }
    }

    // Initialize masks for the new card
    if (window.initMasks) {
        window.initMasks(card);
    }

    calcTotals(Object.keys(data).length > 0);
}

function fillProductData(sel) {
    const card = sel.closest('.item-card');
    const opt = sel.options[sel.selectedIndex];
    if (!card) return;
    
    const index = card.id.replace('item-card-', '');
    const displayInp = document.getElementById(`invoice-product-display-${index}`);
    
    if (!opt || !opt.value) {
        card.querySelector('.product-id-input').value = '';
        if (displayInp) displayInp.value = '';
        return;
    }
    
    card.querySelector('.product-id-input').value = opt.value;
    card.querySelector('.desc-input').value = opt.getAttribute('data-name') || '';
    card.querySelector('.price-input').value = opt.getAttribute('data-price') || 0;
    card.querySelector('.unit-input').value = opt.getAttribute('data-unit') || 'un';
    if (displayInp) displayInp.value = opt.getAttribute('data-name') || '';
    
    // Auto-fill NCM, CFOP
    card.querySelector('[name$="[ncm]"]').value = opt.getAttribute('data-ncm') || '';
    card.querySelector('[name$="[cfop]"]').value = opt.getAttribute('data-cfop') || '';
    
    // Safely get attribute value helper
    const getVal = (attr, fallback) => {
        const val = opt.getAttribute('data-' + attr);
        return (val !== null && val !== '') ? val : fallback;
    };
    
    // Auto-fill ICMS Rates & CST
    card.querySelector('.icms-rate-input').value = getVal('icms_rate', 18);
    card.querySelector('.icms-cst-input').value = getVal('icms_cst', '00');
    card.querySelector('.orig-input').value = getVal('icms_orig', 0);
    card.querySelector('.icms-red-bc-input').value = getVal('icms_red_bc', 0);
    card.querySelector('.icms-mod-bc-input').value = getVal('icms_mod_bc', 3);
    
    // Auto-fill ICMS ST Rates & MVA
    card.querySelector('.icms-st-rate-input').value = getVal('icms_st_rate', 0);
    card.querySelector('.icms-st-mva-input').value = getVal('icms_st_mva', 0);
    card.querySelector('.icms-st-cst-input').value = getVal('icms_st_cst', '10');
    
    // Auto-fill PIS, COFINS, IPI & ISS
    card.querySelector('.pis-rate-input').value = getVal('pis_rate', 1.65);
    card.querySelector('.cofins-rate-input').value = getVal('cofins_rate', 7.6);
    card.querySelector('.ipi-rate-input').value = getVal('ipi_rate', 0);
    card.querySelector('.iss-rate-input').value = getVal('iss_rate', 0);
    
    // Auto-fill Retencoes (CSLL, IRPJ, CPP)
    card.querySelector('.csll-rate-input').value = getVal('csll_rate', 0);
    card.querySelector('.irpj-rate-input').value = getVal('irpj_rate', 0);
    card.querySelector('.cpp-rate-input').value = getVal('cpp_rate', 0);
 
    // Auto-fill Reforma 2026 (IBS, CBS, IS)
    card.querySelector('.ibs-rate-input').value = getVal('ibs_rate', 0.1);
    card.querySelector('.cbs-rate-input').value = getVal('cbs_rate', 0.9);
    card.querySelector('.is-rate-input').value = getVal('is_rate', 0);

    calcTotals();
}

function fillCustomerData(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    
    document.getElementById('recipient_name').value = opt.dataset.name || '';
    document.getElementById('recipient_document').value = opt.dataset.document || '';
    document.getElementById('recipient_email').value = opt.dataset.email || '';
    document.getElementById('recipient_phone').value = opt.dataset.phone || '';
    document.getElementById('recipient_address').value = opt.dataset.address || '';
    document.getElementById('recipient_city').value = opt.dataset.city || '';
    document.getElementById('recipient_state').value = opt.dataset.state || '';
    document.getElementById('recipient_zip').value = opt.dataset.zip || '';
}

// Initialize
@if(isset($invoice))
    @foreach($invoice->items as $item)
        addItem({
            product_id: "{{ $item->product_id }}",
            description: "{{ $item->description }}",
            ncm: "{{ $item->ncm }}",
            cfop: "{{ $item->cfop }}",
            unit: "{{ $item->unit }}",
            quantity: "{{ $item->quantity }}",
            unit_price: "{{ $item->unit_price }}",
            discount: "{{ $item->discount }}",
            
            icms_cst: "{{ $item->icms_cst }}",
            icms_orig: "{{ $item->icms_orig }}",
            icms_mod_bc: "{{ $item->icms_mod_bc }}",
            icms_red_bc: "{{ $item->icms_red_bc }}",
            icms_base: "{{ $item->icms_base }}",
            icms_rate: "{{ $item->icms_rate }}",
            icms_value: "{{ $item->icms_value }}",

            icms_st_cst: "{{ $item->icms_st_cst }}",
            icms_st_mva: "{{ $item->icms_st_mva }}",
            icms_st_base: "{{ $item->icms_st_base }}",
            icms_st_rate: "{{ $item->icms_st_rate }}",
            icms_st_value: "{{ $item->icms_st_value }}",

            ipi_cst: "{{ $item->ipi_cst }}",
            ipi_enq: "{{ $item->ipi_enq }}",
            ipi_base: "{{ $item->ipi_base }}",
            ipi_rate: "{{ $item->ipi_rate }}",
            ipi_value: "{{ $item->ipi_value }}",

            pis_cst: "{{ $item->pis_cst }}",
            pis_base: "{{ $item->pis_base }}",
            pis_rate: "{{ $item->pis_rate }}",
            pis_value: "{{ $item->pis_value }}",

            cofins_cst: "{{ $item->cofins_cst }}",
            cofins_base: "{{ $item->cofins_base }}",
            cofins_rate: "{{ $item->cofins_rate }}",
            cofins_value: "{{ $item->cofins_value }}",

            iss_cst: "{{ $item->iss_cst }}",
            iss_base: "{{ $item->iss_base }}",
            iss_rate: "{{ $item->iss_rate }}",
            iss_value: "{{ $item->iss_value }}",

            csll_cst: "{{ $item->csll_cst }}",
            csll_base: "{{ $item->csll_base }}",
            csll_rate: "{{ $item->csll_rate }}",
            csll_value: "{{ $item->csll_value }}",

            irpj_cst: "{{ $item->irpj_cst }}",
            irpj_base: "{{ $item->irpj_base }}",
            irpj_rate: "{{ $item->irpj_rate }}",
            irpj_value: "{{ $item->irpj_value }}",

            cpp_cst: "{{ $item->cpp_cst }}",
            cpp_base: "{{ $item->cpp_base }}",
            cpp_rate: "{{ $item->cpp_rate }}",
            cpp_value: "{{ $item->cpp_value }}",

            ibs_cst: "{{ $item->ibs_cst }}",
            ibs_base: "{{ $item->ibs_base }}",
            ibs_rate: "{{ $item->ibs_rate }}",
            ibs_value: "{{ $item->ibs_value }}",

            cbs_cst: "{{ $item->cbs_cst }}",
            cbs_base: "{{ $item->cbs_base }}",
            cbs_rate: "{{ $item->cbs_rate }}",
            cbs_value: "{{ $item->cbs_value }}",

            is_cst: "{{ $item->is_cst }}",
            is_base: "{{ $item->is_base }}",
            is_rate: "{{ $item->is_rate }}",
            is_value: "{{ $item->is_value }}",

            ii_base: "{{ $item->ii_base }}",
            ii_rate: "{{ $item->ii_rate }}",
            ii_value: "{{ $item->ii_value }}",
            ii_desp: "{{ $item->ii_desp }}",
            ii_iof: "{{ $item->ii_iof }}"
        });
    @endforeach
@else
    addItem();
@endif

@if(request()->has('simulate'))
setTimeout(() => {
    const custSelect = document.getElementById('customer-select');
    if (custSelect.options.length > 1) {
        custSelect.selectedIndex = 1;
        fillCustomerData(custSelect);
    }
    document.querySelector('[name="notes"]').value = "Simulação de saída de mercadoria via LogiSync WMS.";
}, 500);
@endif
</script>
@endpush
@include('partials.customer_quick_create')
@include('partials.supplier_quick_create')
@include('partials.product_picker')
@include('partials.customer_picker')
@endsection
