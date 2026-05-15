@extends('layouts.app')

@section('title', 'Consulta de Produtos')
@section('page-title', 'Produtos')
@section('page-subtitle', 'Gerencie seu catálogo de produtos com eficiência')

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    {{-- Success / Error alerts --}}
    @if(session('success'))
        <div class="alert badge-success" style="padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Main Controls Card --}}
    <div class="card" style="padding:1.5rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
            
            {{-- Combined Search & Filter Form --}}
            <form method="GET" action="{{ route('products.index') }}" style="width: 100%;">
                <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
                    <div style="display:flex; gap:0.75rem; flex:1; min-width:320px;">
                <div class="form-group" style="flex:1; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1.25rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Pesquisar produtos, códigos ou categorias..." 
                           class="form-input" style="padding-left:3rem; border-radius:var(--r-md);">
                </div>
                <select name="filter" class="form-select" style="width:160px; border-radius:var(--r-md);">
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
                    <div class="flex" style="gap:0.75rem;">
                        <button type="button" onclick="toggleAdvancedFilter()" class="btn btn-secondary">
                            <i class="fa-solid fa-sliders"></i> <span>Filtros</span>
                        </button>
                        @if(auth()->user()->hasPermission('products.create'))
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> <span>Novo Produto</span>
                        </a>
                        @endif
                    </div>
                </div>
        </div>

        {{-- Advanced Filter Panel --}}
        <div id="advancedFilterPanel" class="{{ request()->hasAny(['status_filter', 'price_min', 'price_max', 'stock_filter']) ? '' : 'hidden' }}" style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Status do Produto</label>
                        <select name="status_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="ativo" {{ request('status_filter') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ request('status_filter') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Mínimo (R$)</label>
                        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0.00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Preço Máximo (R$)</label>
                        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="10.000,00" step="0.01" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nível de Estoque</label>
                        <select name="stock_filter" class="form-select">
                            <option value="">Todos</option>
                            <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Abaixo do Mínimo</option>
                            <option value="medium" {{ request('stock_filter') == 'medium' ? 'selected' : '' }}>Estoque Médio</option>
                            <option value="high" {{ request('stock_filter') == 'high' ? 'selected' : '' }}>Estoque Alto</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Limpar Filtros</a>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card" style="border:none;">
        @if($products->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Identificação do Produto</th>
                            <th>Código</th>
                            <th style="text-align:center;">Estoque Atual</th>
                            <th style="text-align:right;">Preço Unitário</th>
                            <th style="text-align:center;">Nível Reabast.</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div style="font-family:'Outfit'; font-weight:700; font-size:1rem; color:var(--text-primary);">{{ $product->name }}</div>
                                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">{{ Str::limit($product->description, 60) }}</div>
                                </td>
                                <td>
                                    <code style="background:var(--bg-hover); padding:0.25rem 0.5rem; border-radius:4px; font-size:0.85rem; color:var(--text-secondary);">{{ $product->barcode ?? 'N/A' }}</code>
                                </td>
                                <td style="text-align:center;">
                                    @php
                                        $qty = $product->quantity;
                                        $lvl = $product->reorder_level;
                                        $badgeClass = $qty <= $lvl ? 'badge-danger' : ($qty <= $lvl * 1.5 ? 'badge-warning' : 'badge-success');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}" style="min-width:60px; text-align:center; display:inline-block;">
                                        {{ $qty }} un
                                    </span>
                                </td>
                                <td style="text-align:right; font-family:'Outfit'; font-weight:700; color:var(--text-primary);">
                                    R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                                </td>
                                <td style="text-align:center; color:var(--text-muted); font-weight:600;">
                                    {{ $product->reorder_level }}
                                </td>
                                <td style="text-align:center;">
                                    <div class="flex" style="justify-content:center; gap:0.5rem;">
                                        @if(auth()->user()->hasPermission('products.edit'))
                                        <a href="{{ route('products.edit', $product) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('products.delete'))
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="color:var(--red);" title="Excluir">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
                <div style="font-size:0.875rem; color:var(--text-muted); font-weight:500;">
                    Exibindo <strong>{{ $products->count() }}</strong> de <strong>{{ $products->total() }}</strong> produtos
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <div class="empty-state" style="padding:6rem 2rem;">
                <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                    <i class="fa-solid fa-boxes-stacked" style="font-size:3rem; color:var(--text-muted);"></i>
                </div>
                <h3 style="font-family:'Outfit'; font-size:1.5rem; margin-bottom:0.5rem;">Nenhum produto encontrado</h3>
                <p style="color:var(--text-muted); max-width:400px; margin:0 auto 2rem;">Sua busca não retornou resultados ou o catálogo está vazio. Tente ajustar os filtros ou cadastrar um novo item.</p>
                @if(auth()->user()->hasPermission('products.create'))
                <a href="{{ route('products.create') }}" class="btn btn-primary" style="padding:1rem 2rem;">
                    <i class="fa-solid fa-plus"></i> Cadastrar Primeiro Produto
                </a>
                @endif
            </div>
        @endif
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
