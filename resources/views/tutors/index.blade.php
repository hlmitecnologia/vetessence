@extends('layouts.app', ['title' => 'Tutores'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Tutores</h2>
        <a href="{{ route('tutors.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Tutor
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, CPF ou email..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPF</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pets</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tutors as $tutor)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold">
                                {{ substr($tutor->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $tutor->name }}</div>
                                <div class="text-sm text-gray-500">{{ $tutor->city ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $tutor->cpf }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $tutor->phone }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $tutor->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            {{ $tutor->pets->count() ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('tutors.show', $tutor) }}" class="text-blue-600 hover:text-blue-800" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('tutors.edit', $tutor) }}" class="text-gray-600 hover:text-gray-800" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('tutors.destroy', $tutor) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Tem certeza?')" class="text-red-600 hover:text-red-800" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Nenhum tutor encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $tutors->withQueryString()->links() }}
    </div>
</div>
@endsection
