@extends('layouts.adminlte', ['title' => 'Editar Prontuário'])

@section('header')
    <a href="{{ route('medical-records.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Prontuário</h2>
@endsection

@section('content')
<form action="{{ route('medical-records.update', $medicalRecord) }}" method="POST" class="max-w-4xl mx-auto">
    @csrf @method('PUT')
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Diagnóstico e Tratamento</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                <textarea name="diagnosis" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $medicalRecord->diagnosis }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tratamento</label>
                <textarea name="treatment" rows="3" class="w-full px-4 py-2 border rounded-lg">{{ $medicalRecord->treatment }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prognóstico</label>
                    <select name="prognosis" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Selecione...</option>
                        <option value="bom" {{ $medicalRecord->prognosis == 'bom' ? 'selected' : '' }}>Bom</option>
                        <option value="reservado" {{ $medicalRecord->prognosis == 'reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="grave" {{ $medicalRecord->prognosis == 'grave' ? 'selected' : '' }}>Grave</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ $medicalRecord->notes }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('medical-records.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-save mr-2"></i> Salvar
        </button>
    </div>
</form>
@endsection
