@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Categorias')
@section('page-subtitle', 'Gerencie as categorias dos seus produtos')

@push('styles')
<style>
    /* Estilos específicos que não estão no global ainda */
    .search-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 2rem;
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
    <div class="search-wrapper">
        <form method="GET" action="{{ route('categories.index') }}" style="display:flex; gap:0.75rem; flex:1; align-items:center;">
            <div style="position:relative; flex:1;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Pesquisar por nome..."
                       class="form-input" style="padding-left:2.75rem;">
            </div>
            <button type="submit" class="btn btn-secondary" style="height:48px; width:48px; padding:0; justify-content:center;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-secondary" style="height:48px; width:48px; padding:0; justify-content:center;" title="Limpar Filtro">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nova Categoria
        </a>
    </div>

    {{-- Table List --}}
    <div class="card">
        @if($categories->count() > 0)
            <div class="table-wrap" style="border:none; box-shadow:none;">
                <table>
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
                                <td style="font-weight:700; color:var(--text-muted);">
                                    {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">
                                        {{ $category->name }}
                                    </div>
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $category->description ?? '—' }}
                                </td>
                                <td style="color:var(--text-muted); font-size:0.875rem;">
                                    {{ $category->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="btn btn-secondary btn-sm" style="width:36px; height:36px; padding:0; justify-content:center;" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" 
                                              style="display:inline;"
                                              onsubmit="return confirm('Excluir a categoria ' + @json($category->name) + '?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="width:36px; height:36px; padding:0; justify-content:center;" title="Excluir">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div style="padding: 1.5rem; border-top: 1px solid var(--border); display:flex; justify-content:center;">
                    {{ $categories->links() }}
                </div>
            @endif
        @else
            <div style="padding: 5rem 2rem; text-align: center;">
                <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                    <i class="fa-solid fa-tags" style="font-size:3rem; color:var(--text-muted);"></i>
                </div>
                <h3 style="font-family:'Outfit'; font-size:1.5rem; margin-bottom:0.5rem;">Nenhuma categoria encontrada</h3>
                <p style="color:var(--text-muted); margin-bottom:2rem;">Comece criando sua primeira categoria de produtos.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary" style="display:inline-flex; width:auto; margin:0 auto;">
                    <i class="fa-solid fa-plus"></i> Criar Categoria
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
