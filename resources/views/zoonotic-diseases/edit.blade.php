@extends('layouts.adminlte', ['title' => 'Editar Doença Zoonótica'])

@section('header')
    <a href="{{ route('zoonotic-diseases.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar: {{ $zoonoticDisease->name }}</h2>
@endsection

@section('content')
<form action="{{ route('zoonotic-diseases.update', $zoonoticDisease) }}" method="POST" class="max-w-4xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Informações Básicas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $zoonoticDisease->name) }}" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
                <select name="category" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    <option value="viral" {{ old('category', $zoonoticDisease->category) == 'viral' ? 'selected' : '' }}>Viral</option>
                    <option value="bacterial" {{ old('category', $zoonoticDisease->category) == 'bacterial' ? 'selected' : '' }}>Bacteriana</option>
                    <option value="parasitic" {{ old('category', $zoonoticDisease->category) == 'parasitic' ? 'selected' : '' }}>Parasitária</option>
                    <option value="fungal" {{ old('category', $zoonoticDisease->category) == 'fungal' ? 'selected' : '' }}>Fúngica</option>
                    <option value="prion" {{ old('category', $zoonoticDisease->category) == 'prion' ? 'selected' : '' }}>Prion</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Agente Causador</label>
                <input type="text" name="causative_agent" value="{{ old('causative_agent', $zoonoticDisease->causative_agent) }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Período de Incubação</label>
                <input type="text" name="incubation_period" value="{{ old('incubation_period', $zoonoticDisease->incubation_period) }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex items-center space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_notifiable" value="1" {{ old('is_notifiable', $zoonoticDisease->is_notifiable) ? 'checked' : '' }} class="rounded text-red-600">
                    <span class="ml-2"><i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Notificação Obrigatória</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $zoonoticDisease->is_active) ? 'checked' : '' }} class="rounded text-green-600">
                    <span class="ml-2">Ativo</span>
                </label>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Transmissão e Sintomas</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transmissão</label>
                <textarea name="transmission" rows="3" class="w-full px-4 py-2 border rounded-lg">{{ old('transmission', $zoonoticDisease->transmission) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sintomas em Animais</label>
                    <textarea name="animal_symptoms" rows="4" class="w-full px-4 py-2 border rounded-lg">{{ old('animal_symptoms', $zoonoticDisease->animal_symptoms) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sintomas em Humanos</label>
                    <textarea name="human_symptoms" rows="4" class="w-full px-4 py-2 border rounded-lg">{{ old('human_symptoms', $zoonoticDisease->human_symptoms) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Prevenção e Tratamento</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prevenção</label>
                <textarea name="prevention" rows="4" class="w-full px-4 py-2 border rounded-lg">{{ old('prevention', $zoonoticDisease->prevention) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tratamento</label>
                <textarea name="treatment" rows="4" class="w-full px-4 py-2 border rounded-lg">{{ old('treatment', $zoonoticDisease->treatment) }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Espécies Atingidas</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @php
                $speciesOptions = [
                    'canine' => 'Cães', 'feline' => 'Gatos', 'equine' => 'Equinos',
                    'bovine' => 'Bovinos', 'ovine' => 'Ovinos', 'caprine' => 'Caprinos',
                    'swine' => 'Suínos', 'avian' => 'Aves', 'reptile' => 'Répteis',
                    'rodents' => 'Roedores', 'wild_mammals' => 'Mamíferos Silvestres',
                    'wild_canids' => 'Canídeos Silvestres', 'wild_felids' => 'Felídeos Silvestres',
                    'non_human_primates' => 'Primatas Não Humanos',
                    'wild_birds' => 'Aves Silvestres', 'fish' => 'Peixes',
                    'asinine' => 'Asininos', 'mule' => 'Muares',
                    'psittacidae' => 'Psitacídeos',
                ];
                $selected = old('species_affected', $zoonoticDisease->species_affected ?? []);
            @endphp
            @foreach($speciesOptions as $key => $label)
            <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                <input type="checkbox" name="species_affected[]" value="{{ $key }}"
                    {{ in_array($key, $selected) ? 'checked' : '' }} class="rounded text-indigo-600">
                <span class="text-sm">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Observações</h3>
        <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg">{{ old('notes', $zoonoticDisease->notes) }}</textarea>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('zoonotic-diseases.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-save mr-2"></i> Salvar
        </button>
    </div>
</form>
@endsection
