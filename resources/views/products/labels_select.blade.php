@extends('layouts.app')

@section('title', 'Selecionar Etiquetas')
@section('page-title', 'Impressão de Etiquetas')
@section('page-subtitle', 'Selecione os produtos e informe a quantidade de etiquetas para impressão em lote A4')

@section('content')
<div class="w-full anim-entrance">

    @if(session('error'))
        <div class="alert alert-error mb-6">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-6 lg:gap-8 items-start">
        
        {{-- Catalog Card --}}
        <div class="card shadow-md">
            <div class="card-header bg-surface p-4 sm:p-6" style="border-bottom: 1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:12px; height:24px; background:var(--accent); border-radius:4px; box-shadow: 0 0 15px var(--accent-glow);"></div>
                    <h3 style="margin:0; font-size: 1.15rem; font-family:'Outfit'; font-weight:800;">Produtos Disponíveis</h3>
                </div>
            </div>
            <div class="card-body p-4 sm:p-6">
                {{-- Search --}}
                <div style="position:relative; margin-bottom:1.5rem;">
                    <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem; z-index: 1;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" id="catalog-search" class="form-input w-full" style="padding-left:2.5rem; height:45px; border-radius:12px;" placeholder="Buscar produto por nome, SKU ou código de barras...">
                </div>

                {{-- Product List Header --}}
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--bg-hover); border: 1px solid var(--border); border-bottom: none; border-top-left-radius: var(--r-md); border-top-right-radius: var(--r-md); font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    <div style="flex: 1; min-width: 0;">Produto / Código</div>
                    <div style="display: flex; align-items: center; gap: 0.85rem; flex-shrink: 0;">
                        <div style="width: 80px; text-align: right; margin-right: 0.85rem;">Preço</div>
                        <div style="width: 100px; text-align: center;">Ação</div>
                    </div>
                </div>

                {{-- Product List Body (now flexbox list) --}}
                <div style="display: flex; flex-direction: column; border: 1px solid var(--border); border-bottom-left-radius: var(--r-md); border-bottom-right-radius: var(--r-md); overflow: hidden;">
                    @forelse($products as $product)
                    <div class="product-row" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ strtolower($product->name) }}" 
                         data-barcode="{{ strtolower($product->barcode ?? '') }}" 
                         data-sku="{{ strtolower($product->sku) }}">
                        
                        {{-- Product Info --}}
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem; line-height: 1.2; word-break: break-word;">{{ $product->name }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.25rem; word-break: break-word;">
                                {{ $product->category ?? 'Sem Categoria' }} | SKU: {{ $product->barcode ?? $product->sku }}
                            </div>
                        </div>
                        
                        {{-- Price & Action --}}
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-shrink: 0;">
                            <span style="font-weight: 700; color: var(--accent); font-size: 0.85rem; white-space: nowrap; width: 80px; text-align: right;">
                                R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                            </span>
                            <div style="width: 100px; display: flex; justify-content: center;">
                                <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.6rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.8rem; height: 32px; width: 100%; justify-content: center;" 
                                        onclick="addToQueue({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->barcode ?? $product->sku }}', {{ $product->unit_price }})">
                                    <i class="fa-solid fa-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-8 text-muted" style="font-style: italic;">
                        Nenhum produto ativo encontrado.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Queue Card --}}
        <div class="card shadow-lg sticky-queue" style="border: 1px solid var(--accent-subtle);">
            <form action="{{ route('products.labels') }}" method="GET" target="_blank">
                <input type="hidden" name="from_selection" value="1">
                
                <div class="card-header bg-surface p-4 sm:p-6" style="border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:36px; height:36px; background:var(--accent-subtle); color:var(--accent); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">
                            <i class="fa-solid fa-print"></i>
                        </div>
                        <div class="flex flex-col">
                            <h3 style="margin:0; font-size: 1.1rem; font-family:'Outfit'; font-weight: 800;">Fila de Impressão</h3>
                            <span style="font-size: 0.725rem; color: var(--text-muted); font-weight: 600;" id="queue-item-count">0 produtos selecionados</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearQueue()" style="color:var(--red); padding:0.4rem 0.75rem; border-radius:8px;">
                        <i class="fa-solid fa-trash-can"></i> Limpar Fila
                    </button>
                </div>

                <div class="card-body p-4 sm:p-6">
                    {{-- Selected items container --}}
                    <div id="queue-container" style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom: 1.5rem;">
                        {{-- Filled dynamically via Javascript --}}
                        <div class="text-center p-8 text-muted" id="empty-queue-msg" style="font-style:italic; border: 1px dashed var(--border); border-radius:var(--r-md); background: var(--bg-hover);">
                            Nenhum produto selecionado. Adicione produtos do catálogo ao lado para começar.
                        </div>
                    </div>

                    {{-- Summary Panel --}}
                    <div style="background:var(--bg-hover); border:1px solid var(--border); border-radius:var(--r-md); padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                            <span style="color:var(--text-secondary); font-weight:600;">Total de Etiquetas:</span>
                            <span class="font-bold" id="total-labels-display" style="color:var(--text-primary); font-size: 1.1rem; font-family:'Outfit';">0</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem;">
                            <span style="color:var(--text-secondary); font-weight:600;">Folhas A4 Estimadas:</span>
                            <span class="font-bold text-primary" id="total-sheets-display" style="color:var(--accent); font-size: 1.1rem; font-family:'Outfit';">0 folhas</span>
                        </div>
                        <small style="color:var(--text-muted); font-size:0.725rem; line-height:1.4; border-top:1px dashed var(--border); padding-top:0.5rem; display:block;">
                            <i class="fa-solid fa-lightbulb" style="color:var(--yellow); margin-right:0.25rem;"></i>
                            Cálculo baseado no layout padrão de 18 etiquetas por folha A4 (3 colunas x 6 linhas).
                        </small>
                    </div>
                </div>

                <div class="card-footer p-4 sm:p-6" style="background: var(--bg-surface); border-top: 1px solid var(--border); display:flex; justify-content:flex-end;">
                    <button type="submit" id="submit-btn" class="btn btn-primary w-full" style="height:48px; font-weight:700; font-size:0.95rem; border-radius:12px; box-shadow: 0 4px 12px var(--accent-alpha); display:inline-flex; align-items:center; justify-content:center; gap:0.5rem;" disabled>
                        <i class="fa-solid fa-file-pdf"></i> Gerar Etiquetas PDF
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
    /* Styling qty buttons and elements */
    .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: var(--bg-surface);
        color: var(--text-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.15s ease;
    }
    .qty-btn:hover {
        background: var(--bg-hover);
        border-color: var(--border-strong);
    }
    .qty-btn:active {
        transform: scale(0.92);
    }
    .product-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }
    .product-row:hover {
        background: var(--bg-hover);
    }
    .product-row:last-child {
        border-bottom: none;
    }

    .queue-row {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        padding: 0.65rem 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.65rem;
        animation: slideIn 0.2s ease-out;
        transition: all 0.2s ease;
    }
    .queue-row:hover {
        border-color: var(--accent-subtle);
        box-shadow: var(--shadow-sm);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Hide spin buttons for input type number */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }

    @media (min-height: 750px) and (min-width: 1024px) {
        .sticky-queue {
            position: sticky;
            top: 1.5rem;
        }
    }
</style>

@push('scripts')
<script>
    let queue = {};

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('catalog-search');
        
        // Instant search filtering
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.product-row');
                
                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const barcode = row.getAttribute('data-barcode');
                    const sku = row.getAttribute('data-sku');
                    
                    if (name.includes(query) || barcode.includes(query) || sku.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });

    function addToQueue(id, name, refCode, price) {
        if (queue[id]) {
            queue[id].qty++;
        } else {
            queue[id] = {
                id: id,
                name: name,
                refCode: refCode,
                price: price,
                qty: 1
            };
        }
        renderQueue();
    }

    function removeFromQueue(id) {
        delete queue[id];
        renderQueue();
    }

    function changeQty(id, delta) {
        if (queue[id]) {
            queue[id].qty += delta;
            if (queue[id].qty < 1) {
                removeFromQueue(id);
                return;
            }
            renderQueue();
        }
    }

    function setQty(id, value) {
        const val = parseInt(value);
        if (queue[id]) {
            if (isNaN(val) || val < 1) {
                queue[id].qty = 1;
            } else {
                queue[id].qty = val;
            }
            // Retain input cursor/state by only updating counters without full render
            updateSummary();
        }
    }

    function clearQueue() {
        queue = {};
        renderQueue();
    }

    function updateSummary() {
        let totalLabels = 0;
        let differentProducts = 0;

        for (const id in queue) {
            totalLabels += queue[id].qty;
            differentProducts++;
        }

        const sheets = Math.ceil(totalLabels / 18);

        document.getElementById('queue-item-count').textContent = `${differentProducts} produto(s) selecionado(s)`;
        document.getElementById('total-labels-display').textContent = totalLabels;
        document.getElementById('total-sheets-display').textContent = `${sheets} ${sheets === 1 ? 'folha' : 'folhas'}`;
        
        const submitBtn = document.getElementById('submit-btn');
        if (totalLabels > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    function renderQueue() {
        const container = document.getElementById('queue-container');
        const emptyMsg = document.getElementById('empty-queue-msg');
        
        // Remove existing queue-rows
        container.querySelectorAll('.queue-row').forEach(el => el.remove());
        
        const keys = Object.keys(queue);
        
        if (keys.length === 0) {
            emptyMsg.style.display = 'block';
        } else {
            emptyMsg.style.display = 'none';
            
            keys.forEach(id => {
                const item = queue[id];
                const row = document.createElement('div');
                row.className = 'queue-row';
                row.innerHTML = `
                    <div style="flex: 1; min-width: 0;">
                        <input type="hidden" name="product_ids[]" value="${item.id}">
                        <div style="font-weight: 700; font-size: 0.825rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.name}">${item.name}</div>
                        <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.1rem;">Ref: ${item.refCode}</div>
                    </div>
                    
                    {{-- Quantity Selector --}}
                    <div style="display:flex; align-items:center; gap:0.25rem; flex-shrink:0;">
                        <button type="button" class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                        <input type="number" name="quantities[${item.id}]" class="text-center" 
                               value="${item.qty}" min="1" style="width: 38px; height: 24px; padding: 0; font-size: 0.8rem; font-weight:700; border: 1px solid var(--border); background: var(--bg-hover); color: var(--text-primary); border-radius: 6px; outline:none; -moz-appearance: textfield; appearance: textfield;" 
                               oninput="setQty(${item.id}, this.value)">
                        <button type="button" class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                    
                    {{-- Action btn --}}
                    <button type="button" class="btn btn-secondary btn-sm" onclick="removeFromQueue(${item.id})" 
                            style="color: var(--red); padding: 0; width: 24px; height: 24px; display:inline-flex; align-items:center; justify-content:center; border-radius: 6px; flex-shrink:0;" title="Remover item">
                        <i class="fa-solid fa-xmark" style="font-size: 0.75rem;"></i>
                    </button>
                `;
                container.appendChild(row);
            });
        }
        
        updateSummary();
    }
</script>
@endpush
@endsection
