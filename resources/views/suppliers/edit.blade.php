@extends('layouts.adminlte', ['title' => 'Editar Fornecedor'])

@section('header')
    <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Fornecedor</h2>
@endsection

@section('content')
<form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Nome</label><input type="text" name="name" value="{{ $supplier->name }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label><input type="text" name="cnpj" value="{{ $supplier->cnpj }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label><input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ $supplier->email }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Contato</label><input type="text" name="contact" value="{{ $supplier->contact }}" class="w-full px-4 py-2 border rounded-lg"></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('suppliers.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
