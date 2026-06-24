@extends('layouts.adminlte', ['title' => 'Monitoramentos Anestésicos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Monitoramentos Anestésicos</h3>
        <div class="card-tools">
            <a href="{{ route('anesthesia-monitorings.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Monitoramento
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($monitorings->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pet</th>
                    <th>Cirurgia</th>
                    <th>Anestesista</th>
                    <th>Protocolo</th>
                    <th>Duração</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monitorings as $monitoring)
                <tr>
                    <td data-order="{{ $monitoring->created_at->format('Y-m-d') }}">{{ $monitoring->created_at->format('d/m/Y') }}</td>
                    <td><strong>{{ $monitoring->pet->name ?? '-' }}</strong></td>
                    <td>{{ $monitoring->surgery->surgery_type ?? '-' }}</td>
                    <td>{{ $monitoring->anesthetist ?? $monitoring->vet->name ?? '-' }}</td>
                    <td class="text-truncate" style="max-width: 150px;">{{ $monitoring->anesthetic_protocol ?? '-' }}</td>
                    <td>
                        @if($monitoring->monitoring_start && $monitoring->monitoring_end)
                            {{ $monitoring->monitoring_start->diffInMinutes($monitoring->monitoring_end) }} min
                        @else
                            Em andamento
                        @endif
                    </td>
                    <td>
                        @php
                            $monStatus = $monitoring->monitoring_end ? 'completed' : 'in_progress';
                            $monLabels = ['in_progress' => 'Em Andamento', 'completed' => 'Concluído'];
                            $monColors = ['in_progress' => 'warning', 'completed' => 'success'];
                        @endphp
                        <span class="badge badge-{{ $monColors[$monStatus] }}">{{ $monLabels[$monStatus] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('anesthesia-monitorings.show', $monitoring) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('anesthesia-monitorings.edit', $monitoring) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum monitoramento anestésico encontrado.</p>
        @endif
    </div>
</div>
@endsection
