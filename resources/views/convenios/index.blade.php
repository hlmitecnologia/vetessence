@extends('layouts.app', ['title' => 'Convênios'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Convênios</h2>
        <a href="{{ route('convenios.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Convênio
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plano</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desconto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($convenios as $conv)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $conv->name }}</td>
                    <td class="px-6 py-4">{{ $conv->plan_name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $conv->discount_percent ? $conv->discount_percent . '%' : '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $conv->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $conv->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('convenios.show', $conv) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('convenios.edit', $conv) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum convênio encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $convenios->links() }}</div>
</div>
@endsection
