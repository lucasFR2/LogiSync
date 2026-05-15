@extends('layouts.app')

@section('title', 'Consulta de Produtos')
@section('page-title', 'Produtos')
@section('page-subtitle', 'Gerencie seu catálogo de produtos com eficiência')

@section('content')
<div class="anim-entrance flex flex-col gap-6">

    {{-- Success / Error alerts --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Main Controls Card --}}
    <div class="card p-6 sm:p-4">
        <form method="GET" action="{{ route('products.index') }}" class="w-full">
            <div class="flex flex-mobile-col justify-between items-center gap-4">
                {{-- Combined Search & Filter Form --}}
                <div class="flex flex-1 w-full gap-4 sm:flex-col">
                    <div class="form-group flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1.25rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem; z-index: 1;"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Pesquisar produtos..." 
                               class="form-input w-full" style="padding-left:3rem;">
                    </div>
                    <select name="filter" class="form-select w-full md:w-48">
                        <option value="all" {{ ($filterBy??'all')=='all' ? 'selected':'' }}>Todos os Campos</option>
                        <option value="name" {{ ($filterBy??'all')=='name' ? 'selected':'' }}>Nome</option>
                        <option value="barcode" {{ ($filterBy??'all')=='barcode' ? 'selected':'' }}>Código</option>
                        <option value="category" {{ ($filterBy??'all')=='category' ? 'selected':'' }}>Categoria</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="button" onclick="toggleAdvancedFilter()" class="btn btn-secondary flex-1 md:flex-none">
                        <i class="fa-solid fa-sliders"></i> <span class="hidden md:inline">Filtros</span>
                    </button>
                    <a href="{{ route('products.create') }}" class="btn btn-primary flex-1 md:flex-none">
                        <i class="fa-solid fa-plus"></i> <span class="hidden md:inline">Novo</span>
                    </a>
                </div>
            </div>

            {{-- Advanced Filter Panel --}}
            <div id="advancedFilterPanel" class="{{ request()->hasAny(['status_filter', 'price_min', 'price_max', 'stock_filter']) ? '' : 'hidden' }} mt-6 pt-6 border-t" style="border-top:1px solid var(--border);">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="ativo" {{ request('status_filter') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ request('status_filter') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Mínimo</label>
                        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0.00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Máximo</label>
                        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="10000.00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estoque</label>
                        <select name="stock_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Baixo</option>
                            <option value="medium" {{ request('stock_filter') == 'medium' ? 'selected' : '' }}>Médio</option>
                            <option value="high" {{ request('stock_filter') == 'high' ? 'selected' : '' }}>Alto</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Limpar</a>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="card" style="border:none;">
        @if($products->count() > 0)
            <div class="table-wrap">
                <table class="table-stack">
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
                                <td data-label="Produto">
                                    <div class="font-bold text-base" style="color:var(--text-primary);">{{ $product->name }}</div>
                                    <div class="text-xs" style="color:var(--text-muted); margin-top:0.25rem;">{{ Str::limit($product->description, 50) }}</div>
                                </td>
                                <td data-label="Código">
                                    <code style="background:var(--bg-hover); padding:0.25rem 0.5rem; border-radius:4px; font-size:0.85rem; color:var(--text-secondary);">{{ $product->barcode ?? 'N/A' }}</code>
                                </td>
                                <td data-label="Estoque" style="text-align:center;">
                                    @php
                                        $qty = $product->quantity;
                                        $lvl = $product->reorder_level;
                                        $badgeClass = $qty <= $lvl ? 'badge-danger' : ($qty <= $lvl * 1.5 ? 'badge-warning' : 'badge-success');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $qty }} un
                                    </span>
                                </td>
                                <td data-label="Preço" style="text-align:right; font-weight:700;">
                                    R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                                </td>
                                <td data-label="Ações" style="text-align:center;">
                                    <div class="flex justify-center sm:justify-end gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Excluir este produto?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="color:var(--red);" title="Excluir">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-6 border-t flex flex-mobile-col justify-between items-center gap-4">
                <div class="text-sm font-semibold" style="color:var(--text-muted);">
                    Mostrando {{ $products->count() }} de {{ $products->total() }}
                </div>
                <div class="w-full md:w-auto">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <div class="empty-state p-8 sm:p-6" style="text-align: center;">
                <div style="width:80px; height:80px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                    <i class="fa-solid fa-boxes-stacked" style="font-size:2.5rem; color:var(--text-muted);"></i>
                </div>
                <h3 class="text-xl mb-2">Nenhum produto encontrado</h3>
                <p class="text-sm mb-6" style="color:var(--text-muted); max-width:320px; margin-left:auto; margin-right:auto;">Tente ajustar sua busca ou filtros.</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Cadastrar Produto
                </a>
            </div>
        @endif
    </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleAdvancedFilter() {
        const panel = document.getElementById('advancedFilterPanel');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            panel.style.animation = 'entrance 0.4s ease-out';
        }
    }
</script>
@endpush
