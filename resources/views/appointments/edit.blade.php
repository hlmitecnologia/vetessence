@extends('layouts.app', ['title' => 'Editar Consulta'])

@section('header')
    <a href="{{ route('appointments.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Editar Consulta</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('appointments.update', $appointment) }}" method="POST" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                <select name="pet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ $appointment->pet_id == $pet->id ? 'selected' : '' }}>
                        {{ $pet->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário *</label>
                <select name="vet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach($veterinarians as $vet)
                    <option value="{{ $vet->id }}" {{ $appointment->vet_id == $vet->id ? 'selected' : '' }}>
                        {{ $vet->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                <input type="date" name="date" value="{{ $appointment->date->format('Y-m-d') }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                <input type="time" name="time" value="{{ substr($appointment->time, 0, 5) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach(['consulta', 'retorno', 'emergencia', 'cirurgia', 'vacina', 'exame'] as $type)
                    <option value="{{ $type }}" {{ $appointment->type == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach(['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'] as $status)
                    <option value="{{ $status }}" {{ $appointment->status == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                <textarea name="reason" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ $appointment->reason }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('appointments.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
