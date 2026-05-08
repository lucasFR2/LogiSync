@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="card-dark rounded-lg p-6">
        <h2 class="text-xl font-bold text-white mb-4">Registrar Nova Entrada</h2>

        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm text-[#94A3B8]">Produto</label>
                <select name="product_id" required class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md">
                    <option value="">-- Selecione --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->barcode ?? '—' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-[#94A3B8]">Data/Hora</label>
                <input type="datetime-local" name="entry_date" class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md">
            </div>

            <div>
                <label class="text-sm text-[#94A3B8]">Quantidade</label>
                <input type="number" name="quantity" min="1" required class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md">
            </div>

            <div>
                <label class="text-sm text-[#94A3B8]">Observações</label>
                <textarea name="notes" rows="3" class="w-full mt-2 px-4 py-2 bg-[#020617] border border-[#1E293B] text-white rounded-md"></textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 mr-3 bg-gray-600 text-white rounded">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Registrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
