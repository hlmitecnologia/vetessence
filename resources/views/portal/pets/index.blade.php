@extends('portal.layouts.app', ['title' => 'Meus Pets'])

@section('content')
@php
    $speciesMap = ['canine' => 'Cão', 'feline' => 'Gato', 'equine' => 'Cavalo', 'bovine' => 'Boi', 'ovine' => 'Ovelha', 'caprine' => 'Cabra', 'swine' => 'Porco', 'avian' => 'Ave', 'reptile' => 'Réptil'];
@endphp

<div class="flex items-center justify-between mb-6">
    <h1 class="portal-section-title text-2xl sm:text-3xl mb-0">
        <i class="fas fa-paw"></i>
        Meus Pets
    </h1>
</div>

@if($pets->isEmpty())
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-paw"></i>
    <p>Nenhum pet cadastrado ainda.</p>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($pets as $pet)
    <a href="{{ route('portal.pets.show', $pet->id) }}" class="portal-card p-6 hover:shadow-lg transition block portal-fade-in">
        <div class="flex items-center gap-5 mb-4">
            @if($pet->photo_url)
            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                 class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
            @else
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-3xl"></i>
            </div>
            @endif
            <div>
                <h3 class="text-xl font-bold text-gray-800">{{ $pet->name }}</h3>
                <p class="text-base text-gray-500">{{ $speciesMap[$pet->species] ?? $pet->species }} - {{ $pet->breed ?? 'SRD' }}</p>
            </div>
        </div>
        <div class="flex gap-4 text-base text-gray-500">
            <span><i class="fas fa-venus-mars mr-1.5"></i>{{ $pet->gender ?? '-' }}</span>
            <span><i class="fas fa-calendar mr-1.5"></i>{{ $pet->age ?? '-' }}</span>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
