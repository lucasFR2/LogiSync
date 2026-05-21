@extends('layouts.app')

@section('title', 'Consulta de Produtos')
@section('page-title', 'Produtos')
@section('page-subtitle', 'Gerencie seu catálogo de produtos com eficiência')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:1.5rem;">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Barra de busca e ações --}}
    <div class="card" style="padding:1.5rem;">
        <form method="GET" action="{{ route('products.index') }}" id="productsSearchForm">
            <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; flex:1; min-width:min(100%, 520px); align-items:center;">
                    <div style="position:relative; flex:1; min-width:200px;">
                        <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               placeholder="Pesquisar produtos..."
                               class="form-input" style="padding-left:2.75rem; width:100%;">
                    </div>
                    <select name="filter" class="form-select" style="width:auto; min-width:160px;">
                        <option value="all" {{ ($filterBy ?? 'all') == 'all' ? 'selected' : '' }}>Todos os campos</option>
                        <option value="name" {{ ($filterBy ?? 'all') == 'name' ? 'selected' : '' }}>Nome</option>
                        <option value="barcode" {{ ($filterBy ?? 'all') == 'barcode' ? 'selected' : '' }}>Código</option>
                        <option value="category" {{ ($filterBy ?? 'all') == 'category' ? 'selected' : '' }}>Categoria</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-search"></i> Buscar
                    </button>
                    @if(($search ?? '') !== '' || ($filterBy ?? 'all') !== 'all' || request()->hasAny(['status_filter', 'price_min', 'price_max', 'stock_filter']))
                        <a href="{{ route('products.index') }}" class="btn btn-secondary" title="Limpar filtros">
                            <i class="fa-solid fa-times"></i>
                        </a>
                    @endif
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
                    <button type="button" onclick="toggleAdvancedFilter()" class="btn btn-secondary" id="btnToggleFilters">
                        <i class="fa-solid fa-sliders"></i> Filtros
                    </button>
                    @can('produtos.cadastrar')
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Novo Produto
                    </a>
                    @endcan
                </div>
            </div>

            <div id="advancedFilterPanel" class="{{ request()->hasAny(['status_filter', 'price_min', 'price_max', 'stock_filter']) ? '' : 'hidden' }}"
                 style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border);">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Status</label>
                        <select name="status_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="ativo" {{ request('status_filter') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ request('status_filter') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Preço mínimo</label>
                        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0,00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Preço máximo</label>
                        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="0,00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Estoque</label>
                        <select name="stock_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Baixo</option>
                            <option value="medium" {{ request('stock_filter') == 'medium' ? 'selected' : '' }}>Médio</option>
                            <option value="high" {{ request('stock_filter') == 'high' ? 'selected' : '' }}>Alto</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Limpar</a>
                    <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0; font-family:'Outfit',sans-serif;">Catálogo de Produtos</h3>
            </div>
            <span class="text-sm text-muted" style="font-weight:600;">
                {{ $products->total() }} {{ $products->total() === 1 ? 'item' : 'itens' }}
            </span>
        </div>

        @if($products->count() > 0)
            <div class="table-wrap" style="border:none; border-radius:0; box-shadow:none;">
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Código</th>
                                <th style="text-align:center;">Estoque</th>
                                <th style="text-align:right;">Preço</th>
                                <th style="text-align:center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div style="font-weight:700; color:var(--text-primary);">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.2rem;">{{ Str::limit($product->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <code style="background:var(--bg-hover); padding:0.25rem 0.5rem; border-radius:4px; font-size:0.8rem; color:var(--text-secondary);">{{ $product->barcode ?? '—' }}</code>
                                    </td>
                                    <td style="text-align:center;">
                                        @php
                                            $qty = $product->quantity;
                                            $lvl = max(1, (int) $product->reorder_level);
                                            $badgeStyle = $qty <= $lvl
                                                ? 'background:var(--red-bg); color:var(--red);'
                                                : ($qty <= $lvl * 1.5
                                                    ? 'background:var(--orange-bg); color:var(--orange);'
                                                    : 'background:var(--green-bg); color:var(--green);');
                                        @endphp
                                        <span class="badge" style="{{ $badgeStyle }} font-weight:700;">{{ $qty }} un</span>
                                    </td>
                                    <td style="text-align:right; font-weight:700; font-family:'Outfit',sans-serif; white-space:nowrap;">
                                        R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; justify-content:center; gap:0.5rem;">
                                            @can('produtos.visualizar')
                                            <a href="{{ route('products.show', $product) }}" class="icon-btn" title="Visualizar" style="width:32px;height:32px; color:var(--blue);">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('produtos.editar')
                                            <a href="{{ route('products.edit', $product) }}" class="icon-btn" title="Editar" style="width:32px;height:32px;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            @endcan
                                            @can('produtos.excluir')
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;" onsubmit="return confirm('Excluir este produto?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="icon-btn" title="Excluir" style="width:32px;height:32px; color:var(--red);">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="padding:1.25rem 1.5rem; border-top:1px solid var(--border); display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:1rem;">
                <span style="font-size:0.875rem; font-weight:600; color:var(--text-muted);">
                    Mostrando {{ $products->firstItem() }}–{{ $products->lastItem() }} de {{ $products->total() }}
                </span>
                @if($products->hasPages())
                    <div>{{ $products->links() }}</div>
                @endif
            </div>
        @else
            <div class="empty-state" style="padding:4rem 2rem; text-align:center;">
                <i class="fa-solid fa-boxes-stacked" style="font-size:3rem; color:var(--text-muted); margin-bottom:1.5rem;"></i>
                <h3 style="margin:0 0 0.5rem;">Nenhum produto encontrado</h3>
                <p style="color:var(--text-muted); margin:0 0 1.5rem;">Ajuste a busca ou os filtros, ou cadastre um novo produto.</p>
                @can('produtos.cadastrar')
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Cadastrar Produto
                </a>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAdvancedFilter() {
        const panel = document.getElementById('advancedFilterPanel');
        const btn = document.getElementById('btnToggleFilters');
        panel.classList.toggle('hidden');
        if (btn) {
            btn.classList.toggle('btn-secondary', panel.classList.contains('hidden'));
            btn.classList.toggle('btn-primary', !panel.classList.contains('hidden'));
        }
    }
</script>
@endpush
