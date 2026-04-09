@extends('layouts.adminlte', ['title' => 'Prontuários'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Prontuário Médico</h2>
        <a href="{{ route('medical-records.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Registro
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4 items-center">
            <select name="pet_id" class="px-4 py-2 border rounded-lg">
                <option value="">Todos os Pets</option>
                @foreach($pets as $pet)
                <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border rounded-lg">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border rounded-lg">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veterinário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnóstico</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($records as $record)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $record->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $record->pet->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $record->vet->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php $typeLabels = ['consulta' => 'Consulta', 'cirurgia' => 'Cirurgia', 'emergencia' => 'Emergência', 'vacina' => 'Vacina', 'retorno' => 'Retorno', 'exame' => 'Exame']; @endphp
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $typeLabels[$record->type] ?? $record->type }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $record->diagnosis ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('medical-records.show', $record) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('medical-records.edit', $record) }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $records->withQueryString()->links() }}</div>
</div>
@endsection
