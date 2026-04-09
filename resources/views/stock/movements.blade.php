@extends('layouts.adminlte', ['title' => 'Movimentações de Estoque'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Movimentações de Estoque</h2>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <select name="product_id" class="px-4 py-2 border rounded-lg">
                <option value="">Todos Produtos</option>
                @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                @endforeach
            </select>
            <select name="type" class="px-4 py-2 border rounded-lg">
                <option value="">Todos Tipos</option>
                <option value="entry" {{ request('type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                <option value="exit" {{ request('type') == 'exit' ? 'selected' : '' }}>Saída</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qtd</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsável</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($movements as $mov)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $mov->product->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php $typeClass = match($mov->type) { 'entry' => 'bg-green-100 text-green-800', 'exit' => 'bg-red-100 text-red-800', 'adjustment' => 'bg-blue-100 text-blue-800', default => 'bg-gray-100' }; @endphp
                        @php $typeLabels = ['entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste', 'loss' => 'Perda', 'return' => 'Devolução']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $typeClass }}">{{ $typeLabels[$mov->type] ?? $mov->type }}</span>
                    </td>
                    <td class="px-6 py-4 text-right {{ $mov->type === 'entry' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $mov->type === 'entry' ? '+' : '-' }}{{ abs($mov->quantity) }}
                    </td>
                    <td class="px-6 py-4 text-right">{{ $mov->balance_after }}</td>
                    <td class="px-6 py-4 text-sm">{{ $mov->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhuma movimentação encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $movements->links() }}</div>
</div>
@endsection
