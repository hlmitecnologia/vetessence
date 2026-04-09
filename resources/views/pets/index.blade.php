@extends('layouts.app', ['title' => 'Pets'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Pets</h2>
        <a href="{{ route('pets.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Pet
        </a>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <select name="species" class="px-4 py-2 border rounded-lg">
                <option value="">Todas espécies</option>
                <option value="canine" {{ request('species') == 'canine' ? 'selected' : '' }}>Canino</option>
                <option value="feline" {{ request('species') == 'feline' ? 'selected' : '' }}>Felino</option>
                <option value="avian" {{ request('species') == 'avian' ? 'selected' : '' }}>Ave</option>
                <option value="exotic" {{ request('species') == 'exotic' ? 'selected' : '' }}>Exótico</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Espécie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raça</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Idade</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pets as $pet)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($pet->photo_url)
                            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <i class="fas fa-paw"></i>
                            </div>
                            @endif
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $pet->name }}</div>
                                <div class="text-sm text-gray-500">{{ $pet->gender === 'male' ? 'Macho' : 'Fêmea' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @php
                            $speciesLabels = [
                                'canine' => 'Canino',
                                'feline' => 'Felino',
                                'avian' => 'Ave',
                                'exotic' => 'Exótico',
                                'reptile' => 'Réptil',
                                'small_mammal' => 'Pequeno Mamífero'
                            ];
                        @endphp
                        {{ $speciesLabels[$pet->species] ?? $pet->species }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->breed ?? 'SRD' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $pet->tutors->first()->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $pet->age ?? '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('pets.show', $pet) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('pets.edit', $pet) }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Tem certeza?')" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Nenhum pet encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $pets->withQueryString()->links() }}
    </div>
</div>
@endsection
