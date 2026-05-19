@extends('layouts.adminlte', ['title' => 'Solicitar Exame'])

@section('header')
    <a href="{{ route('exams.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Solicitar Exame</h2>
@endsection

@section('content')
<form action="{{ route('exams.store') }}" method="POST" class="max-w-2xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pet *</label>
                <x-tom-select name="pet_id" :value="old('pet_id', $selectedPet->id ?? '')" required>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPet->id ?? '') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Exame *</label>
                <input type="text" name="type" value="{{ old('type') }}" required class="w-full px-4 py-2 border rounded-lg" placeholder="Ex: Hemograma, Raio-X">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Solicitação *</label>
                <input type="date" name="requested_date" value="{{ old('requested_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Veterinário Solicitante *</label>
                <x-tom-select name="vet_id" :value="old('vet_id')" required>
                    @foreach($veterinarians as $vet)
                    <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('exams.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Solicitar</button>
        </div>
    </div>
</form>
@endsection
