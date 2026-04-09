@extends('layouts.adminlte', ['title' => 'Novo Pet'])

@section('header')
    <a href="{{ route('pets.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Novo Pet</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG ou GIF. Máx 2MB.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutor Responsável *</label>
                <select name="tutor_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    @foreach($tutors as $tutor)
                    <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                        {{ $tutor->name }} ({{ $tutor->cpf }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Espécie *</label>
                <select name="species" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option value="canine" {{ old('species') == 'canine' ? 'selected' : '' }}>Canino</option>
                    <option value="feline" {{ old('species') == 'feline' ? 'selected' : '' }}>Felino</option>
                    <option value="avian" {{ old('species') == 'avian' ? 'selected' : '' }}>Ave</option>
                    <option value="exotic" {{ old('species') == 'exotic' ? 'selected' : '' }}>Exótico</option>
                    <option value="reptile" {{ old('species') == 'reptile' ? 'selected' : '' }}>Réptil</option>
                    <option value="small_mammal" {{ old('species') == 'small_mammal' ? 'selected' : '' }}>Pequeno Mamífero</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Raça</label>
                <input type="text" name="breed" value="{{ old('breed') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gênero *</label>
                <select name="gender" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Macho</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Fêmea</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                <input type="number" name="weight" value="{{ old('weight') }}" step="0.01"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cor/Pelagem</label>
                <input type="text" name="color" value="{{ old('color') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Microchip</label>
                <input type="text" name="microchip" value="{{ old('microchip') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Porte</label>
                <select name="size" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione...</option>
                    <option value="small" {{ old('size') == 'small' ? 'selected' : '' }}>Pequeno</option>
                    <option value="medium" {{ old('size') == 'medium' ? 'selected' : '' }}>Médio</option>
                    <option value="large" {{ old('size') == 'large' ? 'selected' : '' }}>Grande</option>
                    <option value="giant" {{ old('size') == 'giant' ? 'selected' : '' }}>Gigante</option>
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('pets.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
