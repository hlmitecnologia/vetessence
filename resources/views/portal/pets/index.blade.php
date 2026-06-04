@extends('portal.layouts.app', ['title' => 'Meus Pets'])

@section('content')
@php
    $speciesMap = ['canine' => 'Cão', 'feline' => 'Gato', 'equine' => 'Cavalo', 'bovine' => 'Boi', 'ovine' => 'Ovelha', 'caprine' => 'Cabra', 'swine' => 'Porco', 'avian' => 'Ave', 'reptile' => 'Réptil'];
@endphp
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Meus Pets</h1>
</div>

@if($pets->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
    <i class="fas fa-paw text-gray-300 text-5xl mb-4"></i>
    <p class="text-gray-500">Nenhum pet cadastrado ainda.</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($pets as $pet)
    <a href="{{ route('portal.pets.show', $pet->id) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition block">
        <div class="flex items-center gap-4 mb-3">
            @if($pet->photo_url)
            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                 class="w-14 h-14 rounded-full object-cover border border-gray-200">
            @else
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-2xl"></i>
            </div>
            @endif
            <div>
                <h3 class="font-semibold text-gray-800">{{ $pet->name }}</h3>
                <p class="text-sm text-gray-500">{{ $speciesMap[$pet->species] ?? $pet->species }} - {{ $pet->breed ?? 'SRD' }}</p>
            </div>
        </div>
        <div class="flex gap-3 text-xs text-gray-500">
            <span><i class="fas fa-venus-mars mr-1"></i>{{ $pet->gender ?? '-' }}</span>
            <span><i class="fas fa-calendar mr-1"></i>{{ $pet->age ?? '-' }}</span>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
