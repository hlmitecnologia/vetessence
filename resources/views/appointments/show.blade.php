@extends('layouts.adminlte', ['title' => 'Consulta'])

@section('header')
    <a href="{{ route('appointments.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Consulta - {{ $appointment->date->format('d/m/Y') }}</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm text-gray-500">Pet</h3>
                <p class="font-semibold">{{ $appointment->pet->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $appointment->pet->tutors->first()->name ?? '' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500">Veterinário</h3>
                <p class="font-semibold">{{ $appointment->vet->name ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500">Data e Hora</h3>
                <p class="font-semibold">{{ $appointment->date->format('d/m/Y') }} às {{ substr($appointment->time, 0, 5) }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500">Tipo</h3>
                <p class="font-semibold">{{ ucfirst($appointment->type) }}</p>
            </div>
            <div>
                <h3 class="text-sm text-gray-500">Status</h3>
                @php
                    $statusClass = match($appointment->status) {
                        'scheduled' => 'bg-blue-100 text-blue-800',
                        'confirmed' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-gray-100 text-gray-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
            </div>
        </div>

        @if($appointment->reason)
        <div class="mb-6">
            <h3 class="text-sm text-gray-500 mb-1">Motivo</h3>
            <p>{{ $appointment->reason }}</p>
        </div>
        @endif

        @if($appointment->services->count() > 0)
        <div class="mb-6">
            <h3 class="text-sm text-gray-500 mb-2">Serviços</h3>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Serviço</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qtd</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($appointment->services as $service)
                    <tr>
                        <td class="px-4 py-2">{{ $service->service->name }}</td>
                        <td class="px-4 py-2 text-right">{{ $service->quantity }}</td>
                        <td class="px-4 py-2 text-right">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-right font-semibold">Total:</td>
                        <td class="px-4 py-2 text-right font-semibold">R$ {{ number_format($appointment->total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        <div class="flex justify-between">
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <div class="flex gap-2">
                @if($appointment->status === 'scheduled' || $appointment->status === 'confirmed')
                <a href="{{ route('medical-records.create') }}?appointment_id={{ $appointment->id }}&pet_id={{ $appointment->pet_id }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-medical mr-2"></i> Iniciar Atendimento
                </a>
                @endif
                <a href="{{ route('appointments.edit', $appointment) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
