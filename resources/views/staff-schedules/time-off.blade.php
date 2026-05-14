@extends('layouts.adminlte', ['title' => 'Solicitações de Folga'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Solicitações de Folga</h3>
        <div class="card-tools">
            <a href="{{ route('staff-schedules.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Escalas
            </a>
            <a href="{{ route('staff-schedules.time-off.store') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Solicitação
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($timeOffs->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Funcionário</th>
                    <th>Início</th>
                    <th>Término</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th style="width: 200px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeOffs as $timeOff)
                <tr>
                    <td>{{ $timeOff->user->name ?? '-' }}</td>
                    <td>{{ $timeOff->start_date->format('d/m/Y') }}</td>
                    <td>{{ $timeOff->end_date->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($timeOff->type) }}</td>
                    <td>
                        @php
                            $statusColors = ['pending' => 'badge-warning', 'approved' => 'badge-success', 'rejected' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $statusColors[$timeOff->status] ?? 'badge-secondary' }}">
                            {{ $timeOff->status == 'pending' ? 'Pendente' : ($timeOff->status == 'approved' ? 'Aprovado' : 'Rejeitado') }}
                        </span>
                    </td>
                    <td>
                        @if($timeOff->status == 'pending')
                        <form action="{{ route('staff-time-off.approve', $timeOff) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action btn-success" title="Aprovar">
                                <i class="fas fa-check"></i> Aprovar
                            </button>
                        </form>
                        <form action="{{ route('staff-time-off.reject', $timeOff) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-action btn-danger" title="Rejeitar">
                                <i class="fas fa-times"></i> Rejeitar
                            </button>
                        </form>
                        @else
                        <span class="text-muted">---</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma solicitação encontrada.</p>
        @endif
    </div>
</div>
@endsection
