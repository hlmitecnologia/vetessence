@extends('portal.layouts.app', ['title' => $pet->name])

@section('content')
@php
    $speciesMap = ['canine' => 'Cão', 'feline' => 'Gato', 'equine' => 'Cavalo', 'bovine' => 'Boi', 'ovine' => 'Ovelha', 'caprine' => 'Cabra', 'swine' => 'Porco', 'avian' => 'Ave', 'reptile' => 'Réptil'];
@endphp

<div class="mb-6">
    <a href="{{ route('portal.pets.index') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Meus Pets
    </a>
</div>

<div class="portal-card p-6 sm:p-8 mb-6 portal-fade-in">
    <div class="flex items-center gap-5 mb-6">
        @if($pet->photo_url)
        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
             class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
        @else
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-paw text-blue-600 text-4xl"></i>
        </div>
        @endif
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $pet->name }}</h1>
            <p class="text-lg text-gray-500">{{ $speciesMap[$pet->species] ?? $pet->species }} - {{ $pet->breed ?? 'SRD' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-base">
        <div>
            <span class="text-gray-500 block">Sexo</span>
            <p class="font-semibold text-gray-800 text-lg">{{ $pet->gender ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500 block">Idade</span>
            <p class="font-semibold text-gray-800 text-lg">{{ $pet->age ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500 block">Peso</span>
            <p class="font-semibold text-gray-800 text-lg">{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500 block">Cor</span>
            <p class="font-semibold text-gray-800 text-lg">{{ $pet->color ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="portal-card p-6 sm:p-8 portal-fade-in">
        <h2 class="portal-section-title">
            <i class="fas fa-calendar-check"></i>
            Consultas
        </h2>
        @if($upcomingAppointments->isNotEmpty())
        <h3 class="text-base font-semibold text-green-600 mb-3">Próximas</h3>
        <div class="space-y-3 mb-6">
            @foreach($upcomingAppointments as $appt)
            <div class="p-4 bg-green-50 rounded-xl">
                <p class="font-semibold text-gray-800 text-base">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-base text-gray-500">{{ strip_tags($appt->reason) ?: 'Consulta' }}</p>
            </div>
            @endforeach
        </div>
        @endif
        @if($pastAppointments->isNotEmpty())
        <h3 class="text-base font-semibold text-gray-600 mb-3">Histórico</h3>
        <div class="space-y-3">
            @foreach($pastAppointments as $appt)
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="font-semibold text-gray-800 text-base">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-base text-gray-500">{{ strip_tags($appt->reason) ?: 'Consulta' }}</p>
            </div>
            @endforeach
        </div>
        @endif
        @if($upcomingAppointments->isEmpty() && $pastAppointments->isEmpty())
        <p class="text-base text-gray-500">Nenhuma consulta registrada.</p>
        @endif
    </div>

    <div class="portal-card p-6 sm:p-8 portal-fade-in">
        <h2 class="portal-section-title">
            <i class="fas fa-syringe"></i>
            Vacinas
        </h2>
        @if($vaccinations->isNotEmpty())
        <div class="space-y-3">
            @foreach($vaccinations as $vac)
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="font-semibold text-gray-800 text-base">{{ $vac->vaccine_name ?? 'Vacina' }}</p>
                <p class="text-base text-gray-500">Aplicada em {{ \Carbon\Carbon::parse($vac->applied_date)->format('d/m/Y') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-base text-gray-500">Nenhuma vacina registrada.</p>
        @endif
    </div>
</div>
@endsection
