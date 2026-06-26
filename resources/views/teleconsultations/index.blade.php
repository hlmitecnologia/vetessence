@extends('layouts.adminlte', ['title' => 'Teleconsultas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Teleconsultas</h3>
        <div class="card-tools">
            <a href="{{ route('teleconsultations.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Teleconsulta</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar pet..."></div>
            <div class="col-md-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendada</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluída</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('teleconsultations.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if($teleconsultations->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Sala</th>
                    <th>Pet</th>
                    <th>Veterinário</th>
                    <th>Agendado</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teleconsultations as $tc)
                <tr>
                    <td>{{ $tc->room_name }}</td>
                    <td>{{ $tc->pet->name ?? 'N/A' }}</td>
                    <td>{{ $tc->vet->name ?? '-' }}</td>
                    <td data-order="{{ $tc->scheduled_at?->timestamp ?? 0 }}">{{ optional($tc->scheduled_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>
                        @if($tc->status == 'scheduled') <span class="badge badge-info">Agendada</span>
                        @elseif($tc->status == 'active') <span class="badge badge-success">Em Andamento</span>
                        @elseif($tc->status == 'completed') <span class="badge badge-secondary">Concluída</span>
                        @else <span class="badge badge-danger">Cancelada</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('teleconsultations.show', $tc) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        @if($tc->status == 'scheduled')
                        <a href="{{ route('teleconsultations.start', $tc) }}" class="btn btn-action btn-success" title="Iniciar"><i class="fas fa-play"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted my-4">Nenhuma teleconsulta encontrada.</p>
        @endif
    </div>
</div>
@endsection
