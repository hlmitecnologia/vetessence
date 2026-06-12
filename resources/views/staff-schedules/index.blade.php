@extends('layouts.adminlte', ['title' => 'Escalas de Trabalho'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Escalas de Trabalho</h3>
        <div class="card-tools">
            <a href="{{ route('staff-schedules.time-off') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-clock"></i> Solicitações de Folga
            </a>
            <a href="{{ route('staff-schedules.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($schedules->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Funcionário</th>
                    <th>Data</th>
                    <th>Início</th>
                    <th>Término</th>
                    <th>Tipo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->user->name ?? '-' }}</td>
                    <td>{{ $schedule->work_date->format('d/m/Y') }}</td>
                    <td>{{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '-' }}</td>
                    <td>{{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '-' }}</td>
                    <td>
                        @php
                            $shiftLabels = ['regular' => 'Regular', 'morning' => 'Manhã', 'afternoon' => 'Tarde', 'night' => 'Noturno'];
                            $shiftColors = ['regular' => 'badge-primary', 'morning' => 'badge-info', 'afternoon' => 'badge-warning', 'night' => 'badge-dark'];
                        @endphp
                        <span class="badge {{ $shiftColors[$schedule->shift_type] ?? 'badge-secondary' }}">
                            {{ $shiftLabels[$schedule->shift_type] ?? $schedule->shift_type }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('staff-schedules.edit', $schedule) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('staff-schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-confirm="Tem certeza?" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $schedules->links() }}
        </div>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection
