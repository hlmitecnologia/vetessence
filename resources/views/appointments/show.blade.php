@extends('layouts.adminlte', ['title' => 'Consulta'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Consulta - {{ $appointment->date->format('d/m/Y') }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><td><strong>Pet:</strong></td><td>{{ $appointment->pet->name ?? '-' }}</td></tr>
                    <tr>
                        <td><strong>Tutor:</strong></td>
                        <td>
                            @if($appointment->pet && $appointment->pet->tutors && $appointment->pet->tutors->first())
                                {{ $appointment->pet->tutors->first()->name }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr><td><strong>Veterinário:</strong></td><td>{{ $appointment->vet->name ?? '-' }}</td></tr>
                    <tr><td><strong>Data/Hora:</strong></td><td>{{ $appointment->date->format('d/m/Y') }} às {{ substr($appointment->time, 0, 5) }}</td></tr>
                    <tr><td><strong>Tipo:</strong></td><td>{{ ucfirst($appointment->type) }}</td></tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @php
                                $statusColors = [
                                    'scheduled' => 'badge badge-primary',
                                    'confirmed' => 'badge badge-success',
                                    'in_progress' => 'badge badge-warning',
                                    'completed' => 'badge badge-secondary',
                                    'cancelled' => 'badge badge-danger',
                                    'no_show' => 'badge badge-dark'
                                ];
                                $statusLabels = [
                                    'scheduled' => 'Agendado',
                                    'confirmed' => 'Confirmado',
                                    'in_progress' => 'Em Andamento',
                                    'completed' => 'Concluído',
                                    'cancelled' => 'Cancelado',
                                    'no_show' => 'Faltou'
                                ];
                            @endphp
                            <span class="{{ $statusColors[$appointment->status] ?? 'badge badge-secondary' }}">
                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($appointment->reason)
        <div class="mt-3">
            <strong>Motivo:</strong>
            <p>{!! $appointment->reason !!}</p>
        </div>
        @endif

        @if($appointment->services->count() > 0)
        <div class="mt-4">
            <strong>Serviços:</strong>
            <table class="table table-striped mt-2">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th class="text-center">Qtd</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointment->services as $service)
                    <tr>
                        <td>{{ $service->service->name }}</td>
                        <td class="text-center">{{ $service->quantity }}</td>
                        <td class="text-right">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                        <td class="text-right"><strong>R$ {{ number_format($appointment->services->sum('price'), 2, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('appointments.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Voltar</a>
        @if($appointment->status === 'scheduled' || $appointment->status === 'confirmed')
        <a href="{{ route('medical-records.create') }}?appointment_id={{ $appointment->id }}&pet_id={{ $appointment->pet_id }}" class="btn btn-success">
            <i class="fas fa-file-medical"></i> Iniciar Atendimento
        </a>
        @endif
        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
    </div>
</div>
@endsection
