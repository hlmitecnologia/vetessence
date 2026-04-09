@extends('layouts.app', ['title' => 'Produto'])

@section('header')
    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $product->name }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">SKU</h4><p class="font-mono">{{ $product->sku }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Estoque</h4><p class="{{ $product->isLowStock ? 'text-red-600 font-bold' : '' }}">{{ $product->stock }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Preço Custo</h4><p>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Preço Venda</h4><p class="font-semibold">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Margem</h4><p>{{ number_format($product->margin, 1) }}%</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Estoque Mín.</h4><p>{{ $product->min_stock }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Validade</h4><p>{{ $product->expiration_date ? $product->expiration_date->format('d/m/Y') : '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Categoria</h4><p>{{ $product->category->name ?? '-' }}</p></div>
        </div>
        @if($product->description)
        <div class="p-4 bg-gray-50 rounded-lg"><h4 class="text-xs text-gray-500 uppercase mb-1">Descrição</h4><p>{{ $product->description }}</p></div>
        @endif
    </div>
    <div class="flex justify-between">
        <a href="{{ route('products.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('products.edit', $product) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
