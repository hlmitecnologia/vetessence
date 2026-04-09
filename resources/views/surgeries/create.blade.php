@extends('layouts.app', ['title' => 'Nova Cirurgia'])

@section('header')
    <a href="{{ route('surgeries.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Agendar Cirurgia</h2>
@endsection

@section('content')
<form action="{{ route('surgeries.store') }}" method="POST" class="max-w-2xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                <select name="pet_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cirurgião *</label>
                <select name="vet_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($veterinarians as $vet)
                    <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data e Hora *</label>
                <input type="datetime-local" name="scheduled_date" value="{{ old('scheduled_date') }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cirurgia *</label>
                <input type="text" name="surgery_type" value="{{ old('surgery_type') }}" required class="w-full px-4 py-2 border rounded-lg" placeholder="Ex: Castração, Remoção de tumor">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Anestesia</label>
                <input type="text" name="anesthesia_type" value="{{ old('anesthesia_type') }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico Pré-operatório</label>
                <textarea name="pre_op_diagnosis" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('pre_op_diagnosis') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Protocolo</label>
                <textarea name="protocol" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('protocol') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('surgeries.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Agendar</button>
        </div>
    </div>
</form>
@endsection
