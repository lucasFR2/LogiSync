@extends('layouts.app')

@section('title', 'Editar: ' . $category->name)
@section('page-title', 'Editar Categoria')
@section('page-subtitle', 'Atualize os dados da categoria')

@section('content')
<div style="max-width:600px;">

    @if($errors->any())
        <div class="alert alert-error mb-6">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        </div>
    @endif

    <div class="card anim-fade-up">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-pen-to-square"></i> Editar "{{ $category->name }}"
            </span>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST"
                  style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">
                        Nome da Categoria <span style="color:var(--red);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                           placeholder="Ex: Eletrônicos" required class="form-input" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" rows="3" class="form-textarea"
                              placeholder="Descreva brevemente essa categoria...">{{ old('description', $category->description) }}</textarea>
                </div>

                <div style="display:flex; gap:.75rem; justify-content:flex-end;
                            padding-top:.5rem; border-top:1px solid var(--border);">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
