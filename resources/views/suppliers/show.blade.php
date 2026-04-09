@extends('layouts.app', ['title' => 'Fornecedor'])

@section('header')
    <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $supplier->name }}</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">CNPJ</h4><p>{{ $supplier->cnpj ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Telefone</h4><p>{{ $supplier->phone ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Email</h4><p>{{ $supplier->email ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Contato</h4><p>{{ $supplier->contact ?? '-' }}</p></div>
            <div class="col-span-2"><h4 class="text-xs text-gray-500 uppercase">Endereço</h4><p>{{ $supplier->address ? $supplier->address . ', ' . $supplier->city . ' - ' . $supplier->state : '-' }}</p></div>
        </div>
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
