@extends('layouts.adminlte', ['title' => 'Agenda'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Agenda de Consultas</h3>
        <div class="card-tools">
            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($appointments->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Pet</th>
                    <th>Tutor</th>
                    <th>Veterinário</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                <tr>
                    <td>
                        <strong>{{ $appointment->date->format('d/m/Y') }}</strong><br>
                        <small>{{ substr($appointment->time, 0, 5) }}</small>
                    </td>
                    <td>{{ $appointment->pet->name ?? '-' }}</td>
                    <td>
                        @if($appointment->pet && $appointment->pet->tutors && $appointment->pet->tutors->first())
                            {{ $appointment->pet->tutors->first()->name }}
                        @endif
                    </td>
                    <td>{{ $appointment->vet->name ?? '-' }}</td>
                    <td>
                        @php
                            $typeLabels = [
                                'consulta' => 'Consulta',
                                'retorno' => 'Retorno',
                                'emergencia' => 'Emergência',
                                'cirurgia' => 'Cirurgia',
                                'vacina' => 'Vacina',
                                'exame' => 'Exame'
                            ];
                        @endphp
                        {{ $typeLabels[$appointment->type] ?? $appointment->type }}
                    </td>
                    <td>
                        @php
                            $statusLabels = [
                                'scheduled' => 'Agendado',
                                'confirmed' => 'Confirmado',
                                'in_progress' => 'Em Andamento',
                                'completed' => 'Concluído',
                                'cancelled' => 'Cancelado',
                                'no_show' => 'Faltou'
                            ];
                            $statusColors = [
                                'scheduled' => 'badge-primary',
                                'confirmed' => 'badge-success',
                                'in_progress' => 'badge-warning',
                                'completed' => 'badge-secondary',
                                'cancelled' => 'badge-danger',
                                'no_show' => 'badge-dark'
                            ];
                            $color = $statusColors[$appointment->status] ?? 'badge-secondary';
                            $label = $statusLabels[$appointment->status] ?? $appointment->status;
                        @endphp
                        <span class="badge {{ $color }}">{{ $label }}</span>
                    </td>
                    <td>
                        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection