@extends('portal.layouts.app', ['title' => $pet->name])

@section('content')
<div class="mb-6">
    <a href="{{ route('portal.pets.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left mr-1"></i>Meus Pets
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center gap-4 mb-4">
        @if($pet->photo_url)
        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
             class="w-16 h-16 rounded-full object-cover border border-gray-200">
        @else
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-paw text-blue-600 text-3xl"></i>
        </div>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $pet->name }}</h1>
            <p class="text-sm text-gray-500">{{ $pet->species }} - {{ $pet->breed ?? 'SRD' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <span class="text-gray-500">Sexo</span>
            <p class="font-medium text-gray-800">{{ $pet->gender ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Idade</span>
            <p class="font-medium text-gray-800">{{ $pet->age ?? '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Peso</span>
            <p class="font-medium text-gray-800">{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</p>
        </div>
        <div>
            <span class="text-gray-500">Cor</span>
            <p class="font-medium text-gray-800">{{ $pet->color ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Consultas</h2>
        @if($upcomingAppointments->isNotEmpty())
        <h3 class="text-sm font-medium text-green-600 mb-2">Próximas</h3>
        <div class="space-y-2 mb-4">
            @foreach($upcomingAppointments as $appt)
            <div class="p-3 bg-green-50 rounded-xl text-sm">
                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-gray-500">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
            @endforeach
        </div>
        @endif
        @if($pastAppointments->isNotEmpty())
        <h3 class="text-sm font-medium text-gray-600 mb-2">Histórico</h3>
        <div class="space-y-2">
            @foreach($pastAppointments as $appt)
            <div class="p-3 bg-gray-50 rounded-xl text-sm">
                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-gray-500">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
            @endforeach
        </div>
        @endif
        @if($upcomingAppointments->isEmpty() && $pastAppointments->isEmpty())
        <p class="text-sm text-gray-500">Nenhuma consulta registrada.</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Vacinas</h2>
        @if($vaccinations->isNotEmpty())
        <div class="space-y-2">
            @foreach($vaccinations as $vac)
            <div class="p-3 bg-gray-50 rounded-xl text-sm">
                <p class="font-medium text-gray-800">{{ $vac->vaccine_name ?? 'Vacina' }}</p>
                <p class="text-gray-500">Aplicada em {{ \Carbon\Carbon::parse($vac->applied_date)->format('d/m/Y') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500">Nenhuma vacina registrada.</p>
        @endif
    </div>
</div>
@endsection
