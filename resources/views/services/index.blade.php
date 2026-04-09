@extends('layouts.adminlte', ['title' => 'Serviços'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Serviços</h2>
        <a href="{{ route('services.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Serviço
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duração</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($services as $svc)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $svc->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $svc->category->name ?? '-' }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($svc->price, 2, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $svc->duration ? $svc->duration . ' min' : '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('services.show', $svc) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('services.edit', $svc) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Nenhum serviço encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $services->links() }}</div>
</div>
@endsection
