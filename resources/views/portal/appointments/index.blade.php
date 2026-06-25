@extends('portal.layouts.app', ['title' => 'Consultas'])

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="portal-section-title text-2xl sm:text-3xl mb-0">
        <i class="fas fa-calendar-check"></i>
        Consultas
    </h1>
    <a href="{{ route('portal.appointments.create') }}" class="portal-btn bg-blue-600 hover:bg-blue-700 text-white">
        <i class="fas fa-plus"></i>
        Agendar
    </a>
</div>

@if($upcoming->isNotEmpty())
<h2 class="portal-section-title text-green-600">
    <i class="fas fa-calendar-alt"></i>
    Próximas consultas
</h2>
<div class="space-y-4 mb-8">
    @foreach($upcoming as $appt)
    <div class="portal-card p-5 flex items-center justify-between portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-paw text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                <p class="text-base text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-gray-400">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
        </div>
        <span class="portal-badge bg-green-100 text-green-700">Agendado</span>
    </div>
    @endforeach
</div>
@endif

<h2 class="portal-section-title">
    <i class="fas fa-history"></i>
    Histórico
</h2>
@if($past->isEmpty())
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-calendar"></i>
    <p>Nenhum histórico de consultas.</p>
</div>
@else
<div class="space-y-4">
    @foreach($past as $appt)
    <div class="portal-card p-5 flex items-center justify-between portal-fade-in">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-paw text-gray-600 text-xl"></i>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                <p class="text-base text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-gray-400">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
        </div>
        @php $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu']; @endphp
        <span class="portal-badge bg-gray-100 text-gray-600">{{ $statusLabels[$appt->status] ?? $appt->status }}</span>
    </div>
    @endforeach
</div>
@endif
@endsection
