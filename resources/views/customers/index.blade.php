@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')
@section('page-subtitle', 'Gerencie o cadastro de clientes para emissão de notas')

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
            <form method="GET" action="{{ route('customers.index') }}" style="display:flex; gap:0.75rem; flex:1; min-width:300px;">
                <div style="position:relative; flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou CPF/CNPJ..." class="form-input" style="padding-left:2.75rem; width:100%;">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary" title="Limpar">
                        <i class="fa-solid fa-times"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Novo Cliente
            </a>
        </div>
    </div>

    {{-- Table --}}
    <div class="card anim-entrance" style="animation-delay:0.1s;">
        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($customers->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nome / Razão Social</th>
                            <th>Documento</th>
                            <th>Tipo</th>
                            <th>Contato</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $customer->name }}</div>
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem; color:var(--text-secondary);">
                                    {{ $customer->document }}
                                </td>
                                <td>
                                    @if($customer->type == 'individual')
                                        <span class="badge" style="background:var(--blue-bg); color:var(--blue);">Pessoa Física</span>
                                    @else
                                        <span class="badge" style="background:var(--purple-bg); color:var(--purple);">Pessoa Jurídica</span>
                                    @endif
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    <div style="display:flex; flex-direction:column; gap:2px;">
                                        <span>{{ $customer->email ?? '—' }}</span>
                                        <span style="font-size:0.75rem; opacity:0.8;">{{ $customer->phone ?? '—' }}</span>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; justify-content:center; gap:0.5rem;">
                                        <a href="{{ route('customers.show', $customer) }}" class="icon-btn" title="Detalhes">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" style="display:inline;" onsubmit="return confirm('Excluir este cliente?');">
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

                @if($customers->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $customers->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state" style="padding:6rem 2rem;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-users" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhum cliente cadastrado</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Cadastre seus clientes para emitir faturas e gerenciar pedidos.</p>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary" style="padding:1rem 2.5rem;">
                        <i class="fa-solid fa-plus"></i> Adicionar Primeiro Cliente
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
