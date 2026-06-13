@extends('layouts.adminlte', ['title' => 'Sessões de Terapia'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sessões de Terapia</h3>
        <div class="card-tools">
            <a href="{{ route('therapy-sessions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Sessão</a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Terapeuta</th>
                    <th>Duração</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $s)
                <tr>
                    <td>{{ $s->pet->name ?? 'N/A' }}</td>
                    <td>{{ $s->type }}</td>
                    <td>{{ $s->session_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->therapist->name ?? '-' }}</td>
                    <td>{{ $s->duration_minutes ? $s->duration_minutes . ' min' : '-' }}</td>
                    @php $statusLabels = ['scheduled' => 'Agendada', 'in_progress' => 'Em Andamento', 'completed' => 'Concluída', 'cancelled' => 'Cancelada']; @endphp
                    <td>{!! $s->status == 'completed' ? '<span class="badge badge-success">Concluída</span>' : ($s->status == 'scheduled' ? '<span class="badge badge-primary">Agendada</span>' : '<span class="badge badge-secondary">'.$statusLabels[$s->status].'</span>') !!}</td>
                    <td>
                        <a href="{{ route('therapy-sessions.show', $s) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('therapy-sessions.edit', $s) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('therapy-sessions.destroy', $s) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" data-confirm="Confirmar?"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">Nenhuma sessão encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $sessions->links() }}</div>
</div>
@endsection
