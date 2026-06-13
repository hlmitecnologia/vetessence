@extends('portal.layouts.app', ['title' => 'Consultas'])

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Consultas</h1>
    <a href="{{ route('portal.appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus mr-1"></i>Agendar
    </a>
</div>

@if($upcoming->isNotEmpty())
<h2 class="text-sm font-medium text-green-600 mb-3">Próximas consultas</h2>
<div class="space-y-3 mb-8">
    @foreach($upcoming as $appt)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-400">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
        </div>
        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Agendado</span>
    </div>
    @endforeach
</div>
@endif

<h2 class="text-sm font-medium text-gray-600 mb-3">Histórico</h2>
@if($past->isEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
    <i class="fas fa-calendar text-gray-300 text-5xl mb-4"></i>
    <p class="text-gray-500">Nenhum histórico de consultas.</p>
</div>
@else
<div class="space-y-3">
    @foreach($past as $appt)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-gray-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">{{ $appt->pet->name ?? 'Pet' }}</p>
                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->start_time)->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-400">{{ $appt->reason ?? 'Consulta' }}</p>
            </div>
        </div>
        @php $statusLabels = ['scheduled' => 'Agendado', 'confirmed' => 'Confirmado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'no_show' => 'Não Compareceu']; @endphp
        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">{{ $statusLabels[$appt->status] ?? $appt->status }}</span>
    </div>
    @endforeach
</div>
@endif
@endsection
