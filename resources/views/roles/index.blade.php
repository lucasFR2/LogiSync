@extends('layouts.app')

@section('title', 'Cargos e Funções')
@section('page-title', 'Cargos e Funções')
@section('page-subtitle', 'Gerencie as funções e permissões de acesso do sistema')

@section('content')
<div class="anim-entrance">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:1.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full">
        {{-- List --}}
        <div class="card">
            <div class="card-header" style="padding: 1.5rem; display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:12px; height:24px; background:var(--accent); border-radius:4px; box-shadow: 0 0 15px var(--accent-glow);"></div>
                    <h3 style="margin:0; font-size: 1.25rem;">Funções Ativas</h3>
                </div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary" style="padding:0.625rem 1.25rem; font-size:0.875rem; border-radius:12px; box-shadow: var(--shadow-sm);">
                    <i class="fa-solid fa-plus mr-2"></i> Novo Cargo
                </a>
            </div>
            <div class="table-wrap">
                <table style="width:100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="padding: 1rem 1.5rem;">Nome do Cargo</th>
                            <th style="padding: 1rem 1.5rem;">Permissões</th>
                            <th style="padding: 1rem 1.5rem; text-align:right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="font-weight:700; color:var(--text-primary); font-size:1rem;">{{ $role->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">{{ $role->description ?? 'Sem descrição' }}</div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                                        @if($role->name === 'Administrador')
                                            <span class="badge" style="font-size:0.7rem; background: var(--accent); color: var(--accent-fg);">Acesso Total</span>
                                        @else
                                            @forelse($role->permissions->take(8) as $p)
                                                <span class="badge" style="font-size:0.65rem; background: var(--bg-base); border: 1px solid var(--border); color: var(--text-muted);">{{ $p->label }}</span>
                                            @empty
                                                <span style="font-size:0.75rem; color:var(--text-muted); font-style:italic;">Nenhuma</span>
                                            @endforelse
                                            @if($role->permissions->count() > 8)
                                                <span style="font-size:0.75rem; color:var(--accent); font-weight:600;">+{{ $role->permissions->count() - 8 }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align:right;">
                                    <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="icon-btn" style="background:var(--bg-base); border:1px solid var(--border);" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @if($role->name !== 'Administrador')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este cargo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn" title="Remover" style="background:var(--bg-base); border:1px solid var(--red-alpha); color:var(--red);">
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
        </div>
    </div>
</div>

<style>
    .badge {
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endsection
