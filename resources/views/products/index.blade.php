@extends('layouts.app', ['title' => 'Produtos'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Produtos</h2>
        <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Produto
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar produto..." class="flex-1 px-4 py-2 border rounded-lg">
            <select name="category_id" class="px-4 py-2 border rounded-lg">
                <option value="">Todas Categorias</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço Venda</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $prod)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm">{{ $prod->sku }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $prod->name }}</div>
                        @if($prod->isLowStock)<span class="text-xs text-red-600"><i class="fas fa-exclamation-triangle"></i> Estoque baixo</span>@endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $prod->isLowStock ? 'text-red-600 font-semibold' : '' }}">{{ $prod->stock }}</span>
                    </td>
                    <td class="px-6 py-4">R$ {{ number_format($prod->sale_price, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $prod->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $prod->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('products.show', $prod) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('products.edit', $prod) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhum produto encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $products->links() }}</div>
</div>
@endsection
