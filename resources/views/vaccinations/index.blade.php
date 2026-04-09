@extends('layouts.app', ['title' => 'Vacinas'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Vacinas</h2>
        <a href="{{ route('vaccinations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Nova Vacina
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <select name="pet_id" class="px-4 py-2 border rounded-lg">
                <option value="">Todos os Pets</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacina</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Próxima</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veterinário</th>
                    <th class="px-6 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($vaccinations as $vac)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $vac->pet->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $vac->vaccine }}</td>
                    <td class="px-6 py-4 text-sm">{{ $vac->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $vac->next_date ? $vac->next_date->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $vac->vet->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('vaccinations.show', $vac) }}" class="text-blue-600 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('vaccinations.edit', $vac) }}" class="text-gray-600"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $vaccinations->links() }}</div>
</div>
@endsection
