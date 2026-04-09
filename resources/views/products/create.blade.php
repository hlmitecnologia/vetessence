@extends('layouts.adminlte', ['title' => 'Novo Produto'])

@section('header')
    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Produto</h2>
@endsection

@section('content')
<form action="{{ route('products.store') }}" method="POST" class="max-w-3xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU *</label><input type="text" name="sku" value="{{ old('sku') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Código Barras</label><input type="text" name="barcode" value="{{ old('barcode') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
                <select name="supplier_id" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Unidade</label><input type="text" name="unit" value="{{ old('unit', 'un') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço Custo</label><input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" step="0.01" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço Venda *</label><input type="number" name="sale_price" value="{{ old('sale_price', 0) }}" step="0.01" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Estoque Atual</label><input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Estoque Mínimo</label><input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Validade</label><input type="date" name="expiration_date" value="{{ old('expiration_date') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label><textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('products.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
