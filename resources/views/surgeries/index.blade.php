@extends('layouts.adminlte', ['title' => 'Cirurgias'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cirurgias</h3>
        <div class="card-tools">
            <a href="{{ route('surgeries.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($surgeries->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Veterinário</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surgeries as $surgery)
                <tr>
                    <td>{{ $surgery->scheduled_date->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $surgery->pet->name ?? '-' }}</strong></td>
                    <td>{{ $surgery->surgery_type }}</td>
                    <td>{{ $surgery->vet->name ?? '-' }}</td>
                    <td>
                        @php
                            $statusColors = ['scheduled' => 'badge-primary', 'pre_op' => 'badge-warning', 'in_progress' => 'badge-danger', 'post_op' => 'badge-purple', 'completed' => 'badge-success', 'cancelled' => 'badge-secondary'];
                            $statusLabels = ['scheduled' => 'Agendada', 'pre_op' => 'Pré-op', 'in_progress' => 'Em Andamento', 'post_op' => 'Pós-op', 'completed' => 'Realizada', 'cancelled' => 'Cancelada'];
                        @endphp
                        <span class="badge {{ $statusColors[$surgery->status] ?? 'badge-secondary' }}">{{ $statusLabels[$surgery->status] ?? $surgery->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('surgeries.show', $surgery) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('surgeries.edit', $surgery) }}" class="btn btn-action btn-primary" title="Editar">
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