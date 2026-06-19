@extends('layouts.app')

@section('title', 'Transportadoras')
@section('page-title', 'Transportadoras')
@section('page-subtitle', 'Gerencie suas transportadoras e parceiros de frete')

@section('content')
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    @if(session('success'))
        <div class="alert badge-success" style="padding:1rem; border-radius:var(--r-md); display:flex; align-items:center; gap:0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            <span style="font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Barra de controles --}}
    <div class="card anim-entrance" style="padding:1.5rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between;">
            <form method="GET" action="{{ route('carriers.index') }}" style="display:flex; gap:0.75rem; flex:1; min-width:300px;">
                <div style="position:relative; flex:1;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou CNPJ..." class="form-input" style="padding-left:2.75rem; width:100%;">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('carriers.index') }}" class="btn btn-secondary" title="Limpar"><i class="fa-solid fa-times"></i></a>
                @endif
            </form>
            <a href="{{ route('carriers.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nova Transportadora
            </a>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card anim-entrance" style="animation-delay:0.1s;">
        <div class="table-wrap" style="border:none; box-shadow:none;">
            @if($carriers->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nome / Razão Social</th>
                            <th>CNPJ</th>
                            <th>Reg. ANTT</th>
                            <th>Cidade / UF</th>
                            <th>Telefone</th>
                            <th style="text-align:center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carriers as $carrier)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary);">{{ $carrier->name }}</div>
                                    @if($carrier->contact)
                                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $carrier->contact }}</div>
                                    @endif
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem; color:var(--text-secondary);">
                                    {{ $carrier->cnpj ?? '—' }}
                                </td>
                                <td style="font-family:monospace; font-size:0.85rem; color:var(--text-secondary);">
                                    {{ $carrier->antt ?? '—' }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $carrier->city && $carrier->state ? "{$carrier->city} / {$carrier->state}" : ($carrier->city ?? '—') }}
                                </td>
                                <td style="color:var(--text-secondary); font-size:0.875rem;">
                                    {{ $carrier->phone ?? '—' }}
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; justify-content:center; gap:0.5rem;">
                                        <a href="{{ route('carriers.show', $carrier) }}" class="icon-btn" title="Visualizar">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('carriers.edit', $carrier) }}" class="icon-btn" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" style="display:inline;" onsubmit="return confirm('Excluir esta transportadora?');">
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

                @if($carriers->hasPages())
                    <div style="padding:1.5rem; border-top:1px solid var(--border); display:flex; justify-content:center;">
                        {{ $carriers->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state" style="padding:6rem 2rem;">
                    <div style="width:100px; height:100px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 2rem;">
                        <i class="fa-solid fa-truck" style="font-size:3rem; color:var(--text-muted);"></i>
                    </div>
                    <h3 style="font-family:'Outfit'; font-size:1.5rem;">Nenhuma transportadora encontrada</h3>
                    <p style="color:var(--text-muted); margin-bottom:2rem;">Cadastre suas transportadoras para vinculá-las às notas fiscais.</p>
                    <a href="{{ route('carriers.create') }}" class="btn btn-primary" style="padding:1rem 2.5rem;">
                        <i class="fa-solid fa-plus"></i> Adicionar Transportadora
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
