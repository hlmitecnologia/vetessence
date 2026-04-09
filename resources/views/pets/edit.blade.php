@extends('layouts.app', ['title' => 'Editar Pet'])

@section('header')
    <a href="{{ route('pets.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Editar Pet</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                <input type="text" name="name" value="{{ old('name', $pet->name) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                @if($pet->photo_url)
                <div class="mb-2">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-full object-cover">
                </div>
                @endif
                <input type="file" name="photo" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG ou GIF. Máx 2MB.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Espécie *</label>
                <select name="species" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="canine" {{ $pet->species == 'canine' ? 'selected' : '' }}>Canino</option>
                    <option value="feline" {{ $pet->species == 'feline' ? 'selected' : '' }}>Felino</option>
                    <option value="avian" {{ $pet->species == 'avian' ? 'selected' : '' }}>Ave</option>
                    <option value="exotic" {{ $pet->species == 'exotic' ? 'selected' : '' }}>Exótico</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Raça</label>
                <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gênero *</label>
                <select name="gender" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="male" {{ $pet->gender == 'male' ? 'selected' : '' }}>Macho</option>
                    <option value="female" {{ $pet->gender == 'female' ? 'selected' : '' }}>Fêmea</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $pet->birth_date?->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                <input type="number" name="weight" value="{{ old('weight', $pet->weight) }}" step="0.01"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cor/Pelagem</label>
                <input type="text" name="color" value="{{ old('color', $pet->color) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Porte</label>
                <select name="size" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="small" {{ $pet->size == 'small' ? 'selected' : '' }}>Pequeno</option>
                    <option value="medium" {{ $pet->size == 'medium' ? 'selected' : '' }}>Médio</option>
                    <option value="large" {{ $pet->size == 'large' ? 'selected' : '' }}>Grande</option>
                    <option value="giant" {{ $pet->size == 'giant' ? 'selected' : '' }}>Gigante</option>
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
