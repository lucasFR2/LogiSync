@extends('layouts.app')

@section('title', 'Registrar Entrada')
@section('page-title', 'Registrar Entrada')
@section('page-subtitle', 'Adicionar movimentaÃ§Ã£o de entrada no estoque')

@section('content')
<div style="max-width:600px;">
    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Registrar Nova Entrada</h3>
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.store') }}" method="POST" style="display:flex; flex-direction:column; gap:1.5rem;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Produto <span style="color:var(--red);">*</span></label>
                    <select name="product_id" required class="form-select">
                        <option value="">— Selecionar produto —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-2" style="gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Data / Hora</label>
                        <input type="datetime-local" name="entry_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantidade <span style="color:var(--red);">*</span></label>
                        <input type="number" name="quantity" min="1" required placeholder="Ex: 10" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" rows="4" class="form-textarea" placeholder="Descrição da entrada (opcional)"></textarea>
                </div>
                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Descartar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                        <i class="fa-solid fa-check"></i> Confirmar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


