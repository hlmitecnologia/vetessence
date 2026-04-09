@extends('layouts.app', ['title' => 'Editar Produto'])

@section('header')
    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Produto</h2>
@endsection

@section('content')
<form action="{{ route('products.update', $product) }}" method="POST" class="max-w-3xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome</label><input type="text" name="name" value="{{ $product->name }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input type="text" name="sku" value="{{ $product->sku }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço Custo</label><input type="number" name="cost_price" value="{{ $product->cost_price }}" step="0.01" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço Venda</label><input type="number" name="sale_price" value="{{ $product->sale_price }}" step="0.01" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Estoque</label><input type="number" name="stock" value="{{ $product->stock }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Estoque Mínimo</label><input type="number" name="min_stock" value="{{ $product->min_stock }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-indigo-600"> <span class="ml-2">Produto Ativo</span></label></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('products.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
