@extends('layouts.adminlte', ['title' => 'Agenda'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Agenda de Consultas</h2>
        <a href="{{ route('appointments.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Agendamento
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4 items-center">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Todos status</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veterinário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($appointments as $appointment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $appointment->date->format('d/m/Y') }}</div>
                        <div class="text-sm text-gray-500">{{ substr($appointment->time, 0, 5) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium">{{ $appointment->pet->name ?? '-' }}</div>
                        <div class="text-sm text-gray-500">{{ $appointment->pet->tutors->first()->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $appointment->vet->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $typeLabels = ['consulta' => 'Consulta', 'retorno' => 'Retorno', 'emergencia' => 'Emergência', 'cirurgia' => 'Cirurgia', 'vacina' => 'Vacina', 'exame' => 'Exame'];
                        @endphp
                        {{ $typeLabels[$appointment->type] ?? $appointment->type }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusClass = match($appointment->status) {
                                'scheduled' => 'bg-blue-100 text-blue-800',
                                'confirmed' => 'bg-green-100 text-green-800',
                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-gray-100 text-gray-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'no_show' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Faltou'];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('appointments.edit', $appointment) }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Nenhuma consulta encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $appointments->withQueryString()->links() }}
    </div>
</div>
@endsection
