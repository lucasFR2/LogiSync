@extends('layouts.app')

@section('title', 'Fornecedores')
@section('page-title', 'Fornecedores')
@section('page-subtitle', 'Gerencie seus fornecedores e parceiros comerciais')

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:1.5rem; padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Controls bar --}}
    <div class="card anim-entrance" style="padding:1.5rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
            <form method="GET" action="{{ route('suppliers.index') }}" style="display:flex; gap:0.75rem; flex:1; min-width:300px;">
                <div style="position:relative; flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou CNPJ..." class="form-input" style="padding-left:2.75rem; width:100%;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary" title="Limpar">
                        <i class="fa-solid fa-times"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Novo Fornecedor
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="card anim-entrance" style="animation-delay:0.1s;">
        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($suppliers->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nome / Razão Social</th>
                            <th>CNPJ / Documento</th>
                            <th>E-mail de Contato</th>
                            <th>Telefone</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $supplier->name }}</div>
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem; color:var(--text-secondary);">
                                    {{ $supplier->cnpj ?? '—' }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $supplier->email ?? '—' }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $supplier->phone ?? '—' }}
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; justify-content:center; gap:0.5rem;">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline;" onsubmit="return confirm('Excluir este fornecedor?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="color:var(--red);" title="Excluir">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($suppliers->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state" style="padding:6rem 2rem;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-building" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhum fornecedor encontrado</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Adicione seus fornecedores para vinculá-los às entradas de estoque.</p>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary" style="padding:1rem 2.5rem;">
                        <i class="fa-solid fa-plus"></i> Adicionar Fornecedor
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
