@extends('layouts.app', ['title' => 'Nova Consulta'])

@section('header')
    <a href="{{ route('appointments.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Nova Consulta</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('appointments.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                <select name="pet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                        {{ $pet->name }} - {{ $pet->tutors->first()->name ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário *</label>
                <select name="vet_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    @foreach($veterinarians as $vet)
                    <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>
                        {{ $vet->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                <input type="time" name="time" value="{{ old('time') }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option value="consulta" {{ old('type') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                    <option value="retorno" {{ old('type') == 'retorno' ? 'selected' : '' }}>Retorno</option>
                    <option value="emergencia" {{ old('type') == 'emergencia' ? 'selected' : '' }}>Emergência</option>
                    <option value="cirurgia" {{ old('type') == 'cirurgia' ? 'selected' : '' }}>Cirurgia</option>
                    <option value="vacina" {{ old('type') == 'vacina' ? 'selected' : '' }}>Vacina</option>
                    <option value="exame" {{ old('type') == 'exame' ? 'selected' : '' }}>Exame</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo/Observações</label>
                <textarea name="reason" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('reason') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Serviços</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($services as $service)
                    <label class="flex items-center p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="services[]" value="{{ $service->id }}"
                               class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm">{{ $service->name }}</span>
                        <span class="ml-auto text-sm text-gray-500">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('appointments.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Agendar
            </button>
        </div>
    </form>
</div>
@endsection
