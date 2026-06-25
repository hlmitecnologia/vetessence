@extends('portal.layouts.app', ['title' => 'Consulta - ' . ($appointment->pet->name ?? '')])

@section('content')
@php $fromPet = str_contains(url()->previous(), route('portal.pets.show', $appointment->pet_id)); @endphp
<div class="mb-6">
    @if($fromPet)
    <a href="{{ route('portal.pets.show', $appointment->pet_id) }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>{{ $appointment->pet->name ?? 'Pet' }}
    </a>
    @else
    <a href="{{ route('portal.appointments.index') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Consultas
    </a>
    @endif
</div>

<div class="max-w-2xl mx-auto portal-card p-8 sm:p-10 portal-fade-in">
    <div class="flex items-start justify-between mb-8">
        <div class="flex items-center gap-5">
            @if($appointment->pet->photo_url)
            <img src="{{ $appointment->pet->photo_url }}" alt="{{ $appointment->pet->name }}"
                 class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
            @else
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-3xl"></i>
            </div>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $appointment->pet->name ?? 'Pet' }}</h1>
                <p class="text-lg text-gray-500">{{ strip_tags($appointment->reason) ?: 'Consulta' }}</p>
            </div>
        </div>
        @php $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu']; @endphp
        <span class="portal-badge {{ $appointment->status === 'scheduled' ? 'bg-green-100 text-green-700' : ($appointment->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
            {{ $statusLabels[$appointment->status] ?? $appointment->status }}
        </span>
    </div>

    <div class="space-y-4 text-base">
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Data</span>
            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</span>
        </div>
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Horário</span>
            <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</span>
        </div>
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Unidade</span>
            <span class="font-semibold text-gray-800">{{ $appointment->branch->name ?? '—' }}</span>
        </div>
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Veterinário</span>
            <span class="font-semibold text-gray-800">{{ $appointment->vet->name ?? '—' }}</span>
        </div>
        @if($appointment->reason)
        <div class="py-3 border-b border-gray-100">
            <span class="text-gray-500 block mb-1">Motivo</span>
            <div class="font-semibold text-gray-800 wysiwyg-render">{!! $appointment->reason !!}</div>
        </div>
        @endif
        <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-500">Tipo</span>
            <span class="font-semibold text-gray-800">{{ ucfirst($appointment->type ?? 'consulta') }}</span>
        </div>
    </div>

    <div class="mt-8 flex gap-4">
        <a href="{{ route('portal.dashboard') }}" class="portal-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
            <i class="fas fa-home"></i>
            Início
        </a>
        <a href="{{ route('portal.appointments.index') }}" class="portal-btn bg-blue-600 hover:bg-blue-700 text-white">
            <i class="fas fa-calendar-check"></i>
            Todas as consultas
        </a>
    </div>
</div>
@endsection
