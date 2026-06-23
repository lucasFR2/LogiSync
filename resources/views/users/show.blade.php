@extends('layouts.app')

@section('title', $user->name)
@section('page-title', 'Detalhes do Funcionário')
@section('page-subtitle', $user->name)

@section('content')
<div class="anim-entrance w-full">
    <div class="card">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Ficha Cadastral</h3>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('employees.edit', $user) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pencil"></i> Editar
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:2.5rem; padding:2.5rem;">

            {{-- Identity & Personnel Info --}}
            <div class="grid grid-3" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                         <i class="fa-solid fa-id-card" style="color:var(--accent);"></i> Identificação e Cargo
                    </h4>
                </div>
                <div style="grid-column: 1 / span 2; display:flex; align-items:center; gap:1.25rem;">
                    <div style="width:64px; height:64px; background:var(--bg-hover); border-radius:16px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.5rem; color:var(--accent); border:1px solid var(--border);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Nome Completo</div>
                        <div style="font-size:1.25rem; font-weight:700; color:var(--text-primary);">{{ $user->name }}</div>
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Cargo / Função</div>
                    <div>
                        <span class="badge" style="background:var(--bg-hover); color:var(--text-primary); font-weight:700; font-size:0.8rem; text-transform:uppercase; padding:0.4rem 0.8rem;">
                            {{ $user->role->name ?? 'Sem Cargo' }}
                        </span>
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CPF</div>
                    <div style="font-size:1.1rem; font-family:monospace; color:var(--text-primary); font-weight:600;">{{ $user->cpf }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">RG</div>
                    <div style="font-size:1.1rem; font-family:monospace; color:var(--text-primary); font-weight:600;">{{ $user->rg ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Data de Admissão</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">
                        <i class="fa-regular fa-calendar-check mr-1" style="color:var(--green);"></i>
                        {{ $user->admission_date ? $user->admission_date->format('d/m/Y') : '—' }}
                    </div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Contact Info --}}
            <div class="grid grid-2" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-address-book" style="color:var(--accent);"></i> Contato
                    </h4>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">E-mail Corporativo</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->email }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Telefone / Celular</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->phone ?: '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Address Info --}}
            <div class="grid grid-3" style="gap:2rem;">
                <div style="grid-column: 1/-1;">
                    <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <i class="fa-solid fa-map-location-dot" style="color:var(--accent);"></i> Endereço Residencial
                    </h4>
                </div>
                <div style="grid-column: 1 / span 2;">
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Logradouro</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->address ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Número</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->number ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Complemento</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600; font-style:{{ $user->complement ? 'normal' : 'italic' }};">
                        {{ $user->complement ?: 'Não informado' }}
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Bairro</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->neighborhood ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">CEP</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->zip_code ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Cidade</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->city ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Estado</div>
                    <div style="font-size:1.1rem; color:var(--text-primary); font-weight:600;">{{ $user->state ?: '—' }}</div>
                </div>
            </div>

            <div style="height:1px; background:var(--border);"></div>

            {{-- Documents Section --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <h4 style="font-family:'Outfit'; font-size:1.1rem; color:var(--text-primary); display:flex; align-items:center; gap:0.5rem; margin:0;">
                    <i class="fa-solid fa-file-pdf" style="color:var(--accent);"></i> Documentos Anexados
                </h4>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:1rem; margin-top:0.5rem;">
                    @php $docs = json_decode($user->document_path, true) ?? []; @endphp
                    @forelse($docs as $path)
                        @php
                            $fileName = basename($path);
                            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $icon = 'fa-file';
                            $color = 'var(--text-muted)';
                            if ($ext === 'pdf') { $icon = 'fa-file-pdf'; $color = 'var(--red)'; }
                            elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) { $icon = 'fa-file-image'; $color = 'var(--blue)'; }
                        @endphp
                        <a href="/storage/{{ $path }}" target="_blank" style="display:flex; align-items:center; gap:1rem; padding:1rem; text-decoration:none; color:var(--text-primary); background:var(--bg-base); border:1px solid var(--border); border-radius:var(--r-md); transition:all 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='var(--bg-base)'">
                            <i class="fa-solid {{ $icon }}" style="color:{{ $color }}; font-size:1.5rem;"></i>
                            <div style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.9rem; font-weight:600;">{{ $fileName }}</div>
                            <i class="fa-solid fa-up-right-from-square" style="font-size:0.8rem; opacity:0.4;"></i>
                        </a>
                    @empty
                        <div style="grid-column:1/-1; color:var(--text-muted); font-style:italic;">Nenhum documento anexado.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
