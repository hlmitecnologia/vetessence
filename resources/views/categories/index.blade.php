@extends('layouts.app', ['title' => 'Categorias'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Categorias</h2>
        <a href="{{ route('categories.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Nova Categoria
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pai</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $cat->name }}</td>
                    <td class="px-6 py-4">
                        @php $typeLabels = ['product' => 'Produto', 'service' => 'Serviço', 'vaccine' => 'Vacina']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100">{{ $typeLabels[$cat->type] ?? $cat->type }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $cat->parent->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('categories.edit', $cat) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Nenhuma categoria encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $categories->links() }}</div>
</div>
@endsection
