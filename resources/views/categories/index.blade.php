@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Categorias')
@section('page-subtitle', 'Gerencie as categorias dos seus produtos')

@push('styles')
<style>
    /* Custom styles using system tokens for theme compatibility */
    .search-container {
        background: var(--bg-surface);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--r-lg);
        padding: 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    .search-input-custom {
        width: 100%;
        background: var(--bg-base);
        border: 1.5px solid var(--border);
        border-radius: var(--r-md);
        padding: 0.875rem 1.25rem 0.875rem 3.25rem;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
        font-size: 0.9375rem;
        transition: all 0.3s;
    }

    .search-input-custom:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 4px var(--accent-glow);
    }

    .btn-search {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        color: var(--text-primary);
        width: 48px;
        height: 48px;
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-search:hover {
        background: var(--bg-hover);
        transform: translateY(-2px);
        border-color: var(--border-strong);
    }

    .btn-new-custom {
        background: var(--accent);
        color: var(--accent-fg);
        padding: 0.875rem 1.5rem;
        border-radius: var(--r-md);
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-family: 'Outfit', sans-serif;
        transition: all 0.3s;
        border: none;
        white-space: nowrap;
        box-shadow: var(--shadow-md);
    }

    .btn-new-custom:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* Table styling with theme variables */
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
    }

    .custom-table thead th {
        padding: 0.5rem 1.5rem;
        text-align: left;
        color: var(--text-muted);
        font-family: 'Outfit', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .custom-table tbody td {
        padding: 1.25rem 1.5rem;
        background: var(--bg-surface);
        border-top: 1px solid var(--glass-border);
        border-bottom: 1px solid var(--glass-border);
        transition: background 0.3s;
    }

    .custom-table tbody td:first-child {
        border-left: 1px solid var(--glass-border);
        border-radius: var(--r-md) 0 0 var(--r-md);
    }

    .custom-table tbody td:last-child {
        border-right: 1px solid var(--glass-border);
        border-radius: 0 var(--r-md) var(--r-md) 0;
    }

    .custom-table tbody tr:hover td {
        background: var(--bg-hover);
    }

    .id-column {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        color: var(--text-muted);
        width: 60px;
    }

    .action-btns {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--r-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-base);
        color: var(--text-secondary);
        transition: all 0.2s;
        border: 1px solid var(--border);
    }

    .action-btn:hover {
        background: var(--accent);
        color: var(--accent-fg);
        border-color: var(--accent);
    }

    .action-btn.delete:hover {
        background: var(--red);
        color: white;
        border-color: var(--red);
    }
</style>
@endpush

@section('content')
<div class="anim-entrance">
    
    @if(session('success'))
        <div class="alert alert-success mb-6">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Controls bar --}}
    <div class="search-container">
        <form method="GET" action="{{ route('categories.index') }}" style="display:flex; gap:0.75rem; flex:1; align-items:center;">
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Pesquisar por nome..."
                       class="search-input-custom">
            </div>
            <button type="submit" class="btn-search" title="Pesquisar">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn-search" title="Limpar Filtro">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
        <a href="{{ route('categories.create') }}" class="btn-new-custom">
            <i class="fa-solid fa-plus"></i> Nova Categoria
        </a>
    </div>

    {{-- Table List --}}
    <div style="margin-top: 1rem;">
        @if($categories->count() > 0)
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>NOME DA CATEGORIA</th>
                        <th>DESCRIÇÃO</th>
                        <th style="width: 150px;">CRIADA EM</th>
                        <th style="text-align:right;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td class="id-column">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary); font-size:1rem;">
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td style="color:var(--text-secondary); font-size:0.875rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $category->description ?? '—' }}
                            </td>
                            <td style="color:var(--text-muted); font-size:0.875rem;">
                                {{ $category->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('categories.edit', $category) }}" 
                                       class="action-btn" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" 
                                          style="display:inline;"
                                                                                    onsubmit="return confirm('Excluir a categoria ' + @json($category->name) + '?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($categories->hasPages())
                <div style="margin-top:2rem; display:flex; justify-content:center;">
                    {{ $categories->links() }}
                </div>
            @endif
        @else
            <div class="card" style="padding: 5rem 2rem; text-align: center;">
                <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                    <i class="fa-solid fa-tags" style="font-size:3rem; color:var(--text-muted);"></i>
                </div>
                <h3 style="font-family:'Outfit'; font-size:1.5rem; margin-bottom:0.5rem;">Nenhuma categoria encontrada</h3>
                <p style="color:var(--text-muted); margin-bottom:2rem;">Comece criando sua primeira categoria de produtos.</p>
                <a href="{{ route('categories.create') }}" class="btn-new-custom" style="display:inline-flex; width:auto; margin:0 auto;">
                    <i class="fa-solid fa-plus"></i> Criar Categoria
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
