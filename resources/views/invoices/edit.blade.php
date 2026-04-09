@extends('layouts.adminlte', ['title' => 'Editar Fatura'])

@section('header')
    <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Fatura</h2>
@endsection

@section('content')
<form action="{{ route('invoices.update', $invoice) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Vencimento</label><input type="date" name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg"></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('invoices.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
