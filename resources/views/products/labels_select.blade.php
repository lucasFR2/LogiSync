@extends('layouts.app')

@section('title', 'Selecionar Etiquetas')
@section('page-title', 'Impressão de Etiquetas')
@section('page-subtitle', 'Selecione os produtos para gerar o lote de etiquetas PDF')

@section('content')
<div class="anim-entrance">
    <div class="card overflow-hidden">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;">Catálogo de Produtos Disponíveis</h3>
            <div style="display:flex; gap:0.5rem;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Voltar ao Painel</a>
            </div>
        </div>
        
        <div class="card-body">
            <form action="{{ route('products.labels') }}" method="GET" target="_blank">
                <input type="hidden" name="from_selection" value="1">
                
                {{-- Search Bar --}}
                <div style="display:flex; gap:1rem; margin-bottom:2rem;">
                    <div style="flex:1; position:relative;">
                        <i class="fa-solid fa-search" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left:2.5rem;" placeholder="Filtrar por nome ou código...">
                    </div>
                    <button type="submit" formaction="{{ route('products.labels.select') }}" class="btn btn-secondary">
                        Filtrar
                    </button>
                </div>

                {{-- Products Table --}}
                <div class="table-wrap mb-8" style="max-height: 500px; overflow-y: auto;">
                    <table class="table-stack">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="select-all" style="cursor:pointer; width:18px; height:18px;">
                                </th>
                                <th>Produto</th>
                                <th>SKU / Barcode</th>
                                <th>Estoque Atual</th>
                                <th>Preço Unitário</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox" style="cursor:pointer; width:18px; height:18px;">
                                </td>
                                <td>
                                    <div style="font-weight:600; color:var(--text-primary);">{{ $product->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $product->category ?? 'Sem Categoria' }}</div>
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem;">{{ $product->barcode ?? $product->sku }}</td>
                                <td>
                                    @if($product->quantity <= $product->reorder_level)
                                        <span class="badge badge-warning">{{ $product->quantity }} {{ $product->unit ?? 'un' }}</span>
                                    @else
                                        <span class="badge badge-info">{{ $product->quantity }} {{ $product->unit ?? 'un' }}</span>
                                    @endif
                                </td>
                                <td style="font-weight:700; color:var(--accent);">R$ {{ number_format($product->unit_price, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center p-8 text-muted">Nenhum produto encontrado com estes critérios.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Action Bar --}}
                <div style="background:var(--bg-hover); padding:1.5rem; border-radius:var(--r-md); display:flex; justify-content:space-between; align-items:center;">
                    <div style="color:var(--text-secondary); font-size:0.9rem;">
                        <i class="fa-solid fa-circle-info" style="color:var(--blue);"></i> 
                        Selecione os produtos e clique em gerar para abrir o PDF em uma nova aba.
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding-left:2rem; padding-right:2rem;">
                        <i class="fa-solid fa-file-pdf"></i> Gerar Etiquetas Selecionadas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.product-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }
    });
</script>
@endpush
@endsection
