@extends('portal.layouts.app', ['title' => 'Próximas Vacinas'])

@section('content')
<div class="mb-4">
    <a href="{{ route('portal.dashboard') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Início
    </a>
</div>

<div class="flex items-center justify-between mb-6">
    <h1 class="portal-section-title text-2xl sm:text-3xl mb-0">
        <i class="fas fa-syringe"></i>
        Próximas Vacinas
    </h1>
</div>

@if($vaccinations->isEmpty())
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-syringe"></i>
    <p>Nenhuma vacina próxima encontrada.</p>
</div>
@else
<div class="space-y-4">
    @foreach($vaccinations as $vac)
    <div class="portal-card p-5 flex items-center justify-between portal-fade-in">
        <div class="flex items-center gap-4">
            @if($vac->pet->photo_url)
            <img src="{{ $vac->pet->photo_url }}" alt="{{ $vac->pet->name }}"
                 class="w-14 h-14 rounded-full object-cover border-2 border-gray-200">
            @else
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-syringe text-orange-600 text-xl"></i>
            </div>
            @endif
            <div>
                <p class="text-lg font-semibold text-gray-800">{{ $vac->pet->name ?? 'Pet' }}</p>
                <p class="text-base text-gray-500">{{ $vac->vaccine ?? 'Vacina' }}</p>
                @if($vac->date)
                <p class="text-sm text-gray-400">Última dose: {{ $vac->date->format('d/m/Y') }}</p>
                @endif
                <p class="text-sm text-orange-600 font-medium">Próxima dose: {{ $vac->next_date->format('d/m/Y') }}</p>
            </div>
        </div>
        <a href="{{ route('portal.appointments.create', ['pet_id' => $vac->pet_id, 'reason' => 'Vacinação - ' . ($vac->vaccine ?? 'Vacina')]) }}"
           class="portal-btn bg-orange-500 hover:bg-orange-600 text-white whitespace-nowrap">
            <i class="fas fa-calendar-plus"></i>
            Agendar
        </a>
    </div>
    @endforeach
</div>
@endif
@endsection
