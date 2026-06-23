@extends('layouts.app')

@section('title', $carrier->name)
@section('page-title', $carrier->name)
@section('page-subtitle', 'Ficha completa da transportadora')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:44px; height:44px; background:var(--bg-hover); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-truck" style="font-size:1.25rem; color:var(--accent);"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-family:'Outfit';">{{ $carrier->name }}</h3>
                    @if($carrier->cnpj)
                        <span style="font-size:0.8rem; color:var(--text-muted); font-family:monospace;">CNPJ: {{ $carrier->cnpj }}</span>
                    @endif
                </div>
            </div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('carriers.edit', $carrier) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-pencil"></i> Editar
                </a>
                <a href="{{ route('carriers.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card-body" style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">

            {{-- Coluna Esquerda --}}
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <div>
                    <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin:0 0 0.5rem 0;">IDENTIFICAÇÃO</p>
                    <div class="table-wrap" style="box-shadow:none; border:1px solid var(--border); border-radius:var(--r-md);">
                        <table>
                            <tbody>
                                <tr><td style="width:40%; color:var(--text-muted); font-size:0.8rem;">Razão Social</td><td style="font-weight:600;">{{ $carrier->name }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">CNPJ</td><td style="font-family:monospace;">{{ $carrier->cnpj ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Insc. Estadual</td><td>{{ $carrier->state_registration ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Contato</td><td>{{ $carrier->contact ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Telefone</td><td>{{ $carrier->phone ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">E-mail</td><td>{{ $carrier->email ?? '—' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin:0 0 0.5rem 0;">ENDEREÇO</p>
                    <div class="table-wrap" style="box-shadow:none; border:1px solid var(--border); border-radius:var(--r-md);">
                        <table>
                            <tbody>
                                <tr><td style="width:40%; color:var(--text-muted); font-size:0.8rem;">Logradouro</td><td>{{ $carrier->street ?? '—' }}{{ $carrier->number ? ', Nº ' . $carrier->number : '' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Complemento</td><td>{{ $carrier->complement ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Bairro</td><td>{{ $carrier->neighborhood ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Cidade / UF</td><td>{{ $carrier->city ?? '—' }}{{ $carrier->state ? ' / ' . $carrier->state : '' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">CEP</td><td style="font-family:monospace;">{{ $carrier->zip_code ?? '—' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Coluna Direita --}}
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <div>
                    <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); margin:0 0 0.5rem 0;">DADOS DE TRANSPORTE</p>
                    <div class="table-wrap" style="box-shadow:none; border:1px solid var(--border); border-radius:var(--r-md);">
                        <table>
                            <tbody>
                                <tr><td style="width:40%; color:var(--text-muted); font-size:0.8rem;">Reg. ANTT/RNTRC</td><td style="font-family:monospace; font-weight:700; color:var(--accent);">{{ $carrier->antt ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Tipo de Veículo</td><td>{{ $carrier->vehicle_type ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">Placa Padrão</td><td style="font-family:monospace; font-weight:700;">{{ $carrier->vehicle_plate ?? '—' }}</td></tr>
                                <tr><td style="color:var(--text-muted); font-size:0.8rem;">UF da Placa</td><td>{{ $carrier->vehicle_uf ?? '—' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Ações de perigo --}}
                <div style="margin-top:auto; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--red); margin:0 0 0.75rem 0;">ZONA DE PERIGO</p>
                    <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" onsubmit="return confirm('Tem certeza que deseja excluir {{ addslashes($carrier->name) }}? Esta ação não pode ser desfeita.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="background:var(--red-bg); color:var(--red); border:1px solid var(--red-bg); width:100%;">
                            <i class="fa-solid fa-trash"></i> Excluir Transportadora
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
