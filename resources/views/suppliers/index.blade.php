@extends('layouts.app', ['title' => 'Fornecedores'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Fornecedores</h2>
        <a href="{{ route('suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Fornecedor
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CNPJ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($suppliers as $sup)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $sup->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $sup->cnpj ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $sup->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $sup->email ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('suppliers.show', $sup) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('suppliers.edit', $sup) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum fornecedor encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $suppliers->links() }}</div>
</div>
@endsection
