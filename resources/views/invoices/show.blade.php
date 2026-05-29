@extends('layouts.app')

@section('title', 'Detalhes da Nota Fiscal')
@section('page-title', $invoice->number)
@section('page-subtitle', 'Série ' . $invoice->series . ' • ' . ($invoice->type === 'saida' ? 'Saída' : 'Entrada'))

@section('content')
<div class="anim-entrance" style="display:flex; flex-direction:column; gap:2rem;">

    @if(session('success'))
        <div class="alert badge-success" style="margin-bottom:0.5rem; font-size:0.875rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Action Bar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            @php
                $statusConfig = [
                    'rascunho' => ['color' => 'var(--orange)', 'bg' => 'var(--orange-bg)', 'icon' => 'fa-pen'],
                    'emitida' => ['color' => 'var(--green)', 'bg' => 'var(--green-bg)', 'icon' => 'fa-check-circle'],
                    'cancelada' => ['color' => 'var(--red)', 'bg' => 'var(--red-bg)', 'icon' => 'fa-ban']
                ][$invoice->status] ?? ['color' => 'var(--text-muted)', 'bg' => 'var(--bg-hover)', 'icon' => 'fa-circle'];
            @endphp
            <div class="badge" style="background: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; padding: 0.5rem 1rem; font-size: 0.9rem; font-weight: 700;">
                <i class="fa-solid {{ $statusConfig['icon'] }} mr-2"></i> {{ $invoice->statusLabel() }}
            </div>
            <span style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500;">
                <i class="fa-solid fa-clock-rotate-left mr-1"></i> {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : 'Pendente' }}
            </span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            @if($invoice->status === 'emitida')
                <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="btn btn-primary" style="background: var(--green); border-color: var(--green); box-shadow: 0 8px 16px -4px var(--green-bg);">
                    <i class="fa-solid fa-file-pdf mr-2"></i> Visualizar DANFE
                </a>
            @endif

            @if($invoice->status === 'rascunho')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary" style="background: var(--orange); border-color: var(--orange); box-shadow: 0 8px 16px -4px var(--orange-bg);">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Editar Rascunho
                </a>
            @endif

            @if($invoice->status !== 'cancelada')
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta nota?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="color: var(--red); border-color: var(--red-bg);">
                        <i class="fa-solid fa-ban mr-2"></i> Cancelar Nota
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
        {{-- Issuer Card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--blue-bg); color:var(--blue); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Dados do Emitente</h3>
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1.15rem;">{{ $invoice->issuer_name }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted); display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem;">
                        <span>CNPJ:</span> <span style="color: var(--text-primary); font-weight: 600;">{{ $invoice->issuer_cnpj }}</span>
                        <span>Endereço:</span> <span style="color: var(--text-primary);">{{ $invoice->issuer_address }}</span>
                        <span>Cidade/UF:</span> <span style="color: var(--text-primary);">{{ $invoice->issuer_city }} / {{ $invoice->issuer_state }}</span>
                        <span>CEP:</span> <span style="color: var(--text-primary);">{{ $invoice->issuer_zip }}</span>
                    </div>
                </div>
            </div>
            <div class="anim-float" style="position: absolute; right: -10px; bottom: -10px; font-size: 8rem; opacity: 0.03; pointer-events: none;">
                <i class="fa-solid fa-building-circle-check"></i>
            </div>
        </div>

        {{-- Recipient Card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:32px; height:32px; background:var(--accent-subtle); color:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h3 style="margin:0; font-family:'Outfit';">Dados do Destinatário</h3>
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1.15rem;">{{ $invoice->recipient_name }}</div>
                    <div style="font-size: 0.9rem; color: var(--text-muted); display: grid; grid-template-columns: 80px 1fr; gap: 0.5rem;">
                        <span>CPF/CNPJ:</span> <span style="color: var(--text-primary); font-weight: 600;">{{ $invoice->recipient_document ?: '-' }}</span>
                        <span>Contato:</span> <span style="color: var(--text-primary);">{{ $invoice->recipient_phone ?: '-' }}</span>
                        <span>Email:</span> <span style="color: var(--text-primary);">{{ $invoice->recipient_email ?: '-' }}</span>
                        <span>Local:</span> <span style="color: var(--text-primary);">{{ $invoice->recipient_city }} / {{ $invoice->recipient_state }}</span>
                    </div>
                </div>
            </div>
            <div class="anim-float" style="position: absolute; right: -10px; bottom: -10px; font-size: 8rem; opacity: 0.03; pointer-events: none;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div class="stat-card">
            <div class="stat-label" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Pagamento</div>
            <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-top: 0.5rem;">
                <i class="fa-solid fa-wallet mr-2" style="color: var(--blue);"></i> {{ $invoice->paymentLabel() }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Subtotal Itens</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-top: 0.5rem; font-family: 'Outfit';">
                R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Descontos / Frete</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: var(--red); margin-top: 0.5rem; font-family: 'Outfit';">
                - R$ {{ number_format($invoice->discount, 2, ',', '.') }}
                @if($invoice->shipping > 0)
                    <span style="color: var(--blue); font-size: 0.9rem; font-weight: 600;"> / + R$ {{ number_format($invoice->shipping, 2, ',', '.') }}</span>
                @endif
            </div>
        </div>
        <div class="stat-card" style="background: var(--accent); border: none; box-shadow: 0 15px 30px -10px var(--accent-glow);">
            <div class="stat-label" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.7);">VALOR TOTAL</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: white; margin-top: 0.5rem; font-family: 'Outfit';">
                R$ {{ number_format($invoice->total, 2, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Conference Card --}}
    <div class="card" style="border: 1px solid var(--accent-subtle);">
        <div class="card-header" style="background: var(--bg-hover);">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:32px; height:32px; background:var(--orange-bg); color:var(--orange); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h3 style="margin:0; font-family:'Outfit';">Conferência da Nota Fiscal</h3>
            </div>
            @php
                $confColors = [
                    'Pendente' => ['bg' => 'var(--orange-bg)', 'color' => 'var(--orange)', 'icon' => 'fa-clock'],
                    'Conferida' => ['bg' => 'var(--green-bg)', 'color' => 'var(--green)', 'icon' => 'fa-circle-check'],
                    'Divergente' => ['bg' => 'var(--red-bg)', 'color' => 'var(--red)', 'icon' => 'fa-circle-xmark'],
                ][$invoice->conference_status] ?? ['bg' => 'var(--orange-bg)', 'color' => 'var(--orange)', 'icon' => 'fa-clock'];
            @endphp
            <div class="badge" style="background: {{ $confColors['bg'] }}; color: {{ $confColors['color'] }}; font-weight: 800; font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                <i class="fa-solid {{ $confColors['icon'] }} mr-2"></i> {{ $invoice->conference_status ?? 'Pendente' }}
            </div>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem;">
            
            {{-- Info / Detail Block --}}
            <div id="conference-details-panel" style="{{ $invoice->conference_status !== 'Pendente' ? '' : 'display:none;' }}">
                <div class="grid grid-3" style="gap:1.5rem; margin-bottom:1.5rem;">
                    <div>
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Responsável</div>
                        <div style="font-size:1rem; font-weight:600; color:var(--text-primary);">
                            <i class="fa-solid fa-user-circle mr-1" style="color:var(--text-secondary);"></i>
                            {{ $invoice->conferredBy->name ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Data / Hora</div>
                        <div style="font-size:1rem; font-weight:600; color:var(--text-primary);">
                            <i class="fa-solid fa-calendar mr-1" style="color:var(--text-secondary);"></i>
                            {{ $invoice->conferred_at ? $invoice->conferred_at->format('d/m/Y H:i') : '—' }}
                        </div>
                    </div>
                </div>
                
                @if($invoice->conference_notes)
                    <div style="background:var(--bg-base); border:1px solid var(--border); border-radius:var(--r-md); padding:1rem; margin-bottom:1.5rem;">
                        <div style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem; display:flex; align-items:center; gap:0.4rem;">
                            <i class="fa-solid fa-comment-dots"></i> Observações da Conferência
                        </div>
                        <p style="margin:0; font-size:0.9rem; color:var(--text-primary); white-space:pre-wrap;">{{ $invoice->conference_notes }}</p>
                    </div>
                @endif

                <button type="button" class="btn btn-secondary" onclick="toggleConferenceForm()">
                    <i class="fa-solid fa-arrows-rotate mr-2"></i> Alterar Status / Observações
                </button>
            </div>

            {{-- Form Block --}}
            <div id="conference-form-panel" style="{{ $invoice->conference_status === 'Pendente' ? '' : 'display:none;' }}">
                <form action="{{ route('invoices.confer', $invoice) }}" method="POST" style="display:flex; flex-direction:column; gap:1.25rem;">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Status da Conferência <span style="color:var(--red);">*</span></label>
                        <select name="conference_status" id="conference_status_select" required class="form-select" style="max-width:320px;">
                            <option value="Pendente" {{ $invoice->conference_status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Conferida" {{ $invoice->conference_status === 'Conferida' ? 'selected' : '' }}>Conferida (Sem divergências)</option>
                            <option value="Divergente" {{ $invoice->conference_status === 'Divergente' ? 'selected' : '' }}>Divergente (Possui pendências/erros)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Observações da Conferência</label>
                        <textarea name="conference_notes" rows="3" class="form-textarea" placeholder="Descreva observações, divergências encontradas, justificativas, etc.">{{ $invoice->conference_notes }}</textarea>
                    </div>

                    <div style="display:flex; gap:0.75rem; align-items:center;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save mr-2"></i> Salvar Conferência
                        </button>
                        
                        @if($invoice->conference_status !== 'Pendente')
                            <button type="button" class="btn btn-secondary" onclick="toggleConferenceForm()">
                                Cancelar
                            </button>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Items Table --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:32px; height:32px; background:var(--blue-bg); color:var(--blue); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                    <i class="fa-solid fa-list-ol"></i>
                </div>
                <h3 style="margin:0; font-family:'Outfit';">Itens da Nota Fiscal</h3>
            </div>
            <div class="badge" style="background: var(--bg-hover); color: var(--text-primary); font-weight: 700;">{{ $invoice->items->count() }} PRODUTOS</div>
        </div>
        <div class="table-wrap">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center; padding: 1rem;">#</th>
                        <th style="padding: 1rem;">Produto / Descrição</th>
                        <th style="text-align: center; padding: 1rem;">NCM / CFOP</th>
                        <th style="text-align: center; padding: 1rem;">Unid.</th>
                        <th style="text-align: right; padding: 1rem;">Qtd.</th>
                        <th style="text-align: right; padding: 1rem;">Preço Unit.</th>
                        <th style="text-align: right; padding: 1rem;">Subtotal</th>
                        <th style="text-align: right; padding: 1rem;">Tributos (ICMS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $idx => $item)
                        <tr class="anim-entrance" style="animation-delay: {{ $idx * 0.05 }}s;">
                            <td style="text-align: center; color: var(--text-muted); font-size: 0.8rem;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 700; color: var(--text-primary);">{{ $item->description }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">SKU: {{ $item->product_id ?: 'MANUAL' }}</div>
                            </td>
                            <td style="text-align: center; padding: 1rem;">
                                <span style="font-family: monospace; font-size: 0.75rem; background: var(--bg-hover); padding: 2px 6px; border-radius: 4px; color: var(--text-secondary);">{{ $item->ncm }}</span>
                                <div style="margin-top: 4px;">
                                    <span style="font-family: monospace; font-size: 0.75rem; background: var(--blue-bg); padding: 2px 6px; border-radius: 4px; color: var(--blue);">{{ $item->cfop }}</span>
                                </div>
                            </td>
                            <td style="text-align: center; color: var(--text-secondary); font-weight: 600;">{{ strtoupper($item->unit) }}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--text-primary);">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                            <td style="text-align: right; color: var(--text-secondary);">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 800; color: var(--accent); font-family: 'Outfit';">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            <td style="text-align: right; padding: 1rem;">
                                <div style="font-size: 0.75rem; line-height: 1.4;">
                                    <div style="color: var(--blue); font-weight: 600;">ICMS: R$ {{ number_format($item->icms_value, 2, ',', '.') }} ({{ number_format($item->icms_rate, 1) }}%)</div>
                                    <div style="color: var(--text-muted); font-size: 0.65rem;">IPI/PIS/COF: R$ {{ number_format($item->ipi_value + $item->pis_value + $item->cofins_value, 2, ',', '.') }}</div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($invoice->notes)
        <div class="card" style="border-left: 4px solid var(--accent);">
            <div class="card-body" style="padding: 1.5rem;">
                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom: 1rem;">
                    <i class="fa-solid fa-comment-dots" style="color: var(--accent);"></i>
                    <h3 style="margin:0; font-family:'Outfit'; font-size:1.1rem;">Observações da Nota</h3>
                </div>
                <p style="margin:0; font-size:0.95rem; color:var(--text-secondary); line-height:1.7; white-space: pre-wrap;">{{ $invoice->notes }}</p>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function toggleConferenceForm() {
        const details = document.getElementById('conference-details-panel');
        const form = document.getElementById('conference-form-panel');
        
        if (form.style.display === 'none') {
            form.style.display = '';
            details.style.display = 'none';
        } else {
            form.style.display = 'none';
            details.style.display = '';
        }
    }
</script>
@endpush
@endsection
