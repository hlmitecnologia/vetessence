@extends('layouts.adminlte', ['title' => 'Nova Fatura'])

@section('header')
    <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Nova Fatura</h2>
@endsection

@section('content')
<form action="{{ route('invoices.store') }}" method="POST" class="max-w-3xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutor *</label>
                <select name="tutor_id" id="tutor_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($tutors as $t)
                    <option value="{{ $t->id }}" {{ old('tutor_id', $tutor->id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vencimento *</label>
                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>

        <h4 class="font-semibold mb-4">Itens</h4>
        <div id="items-container" class="space-y-3">
            <div class="item-row flex gap-4 items-end p-4 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Descrição</label>
                    <input type="text" name="items[0][description]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Descrição do item">
                </div>
                <div class="w-20">
                    <label class="block text-xs text-gray-500 mb-1">Qtd</label>
                    <input type="number" name="items[0][quantity]" value="1" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div class="w-28">
                    <label class="block text-xs text-gray-500 mb-1">Valor Unit.</label>
                    <input type="number" name="items[0][unit_price]" step="0.01" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 mb-1"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <button type="button" onclick="addItem()" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm"><i class="fas fa-plus mr-1"></i> Adicionar Item</button>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('invoices.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Criar Fatura</button>
    </div>
</form>

@push('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const html = `<div class="item-row flex gap-4 items-end p-4 bg-gray-50 rounded-lg">
        <div class="flex-1"><input type="text" name="items[${itemIndex}][description]" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Descrição"></div>
        <div class="w-20"><input type="number" name="items[${itemIndex}][quantity]" value="1" class="w-full px-3 py-2 border rounded-lg text-sm"></div>
        <div class="w-28"><input type="number" name="items[${itemIndex}][unit_price]" step="0.01" class="w-full px-3 py-2 border rounded-lg text-sm"></div>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
    </div>`;
    document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
    itemIndex++;
}
</script>
@endpush
@endsection
