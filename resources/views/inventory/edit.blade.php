@extends('layouts.app')

@section('title', 'Editar Entrada')
@section('page-title', 'Editar Entrada')
@section('content')
<div class="w-full">
    <div class="card anim-entrance">
        <div class="card-header">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:10px; height:24px; background:var(--accent); border-radius:4px;"></div>
                <h3 style="margin:0;">Editar Movimentação</h3>
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('inventory.update', $inventory) }}" method="POST" style="display:flex; flex-direction:column; gap:1.5rem;">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Produto <span style="color:var(--red);">*</span></label>
                    <select name="product_id" required class="form-select">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $inventory->product_id == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->barcode ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-2" style="gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Data / Hora</label>
                        <input type="datetime-local" name="entry_date" value="{{ optional($inventory->entry_date)->format('Y-m-d\TH:i') }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantidade <span style="color:var(--red);">*</span></label>
                        <input type="number" name="quantity" min="1" required value="{{ $inventory->quantity }}" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" rows="4" class="form-textarea">{{ $inventory->notes }}</textarea>
                </div>
                <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:1.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Descartar</a>
                    <button type="submit" class="btn btn-primary" style="padding-left:2.5rem; padding-right:2.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


