@extends('layouts.adminlte', ['title' => 'Agendamentos Online'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Agendamentos Online</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar tutor/pet...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmados</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitados</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('online-bookings.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if($bookings->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tutor</th>
                    <th>Pet</th>
                    <th>Data Pref.</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr class="{{ $booking->status == 'pending' ? 'table-warning' : '' }}">
                    <td>{{ $booking->tutor_name }}<br><small>{{ $booking->tutor_email }}</small></td>
                    <td>{{ $booking->pet_name }} ({{ $booking->pet_species }})</td>
                    <td>{{ $booking->preferred_date->format('d/m/Y') }}</td>
                    <td>
                        @if($booking->status == 'pending') <span class="badge badge-warning">Pendente</span>
                        @elseif($booking->status == 'confirmed') <span class="badge badge-success">Confirmado</span>
                        @elseif($booking->status == 'rejected') <span class="badge badge-danger">Rejeitado</span>
                        @else <span class="badge badge-secondary">{{ $booking->status }}</span> @endif
                    </td>
                    <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('online-bookings.show', $booking) }}" class="btn btn-action btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                        <form action="{{ route('online-bookings.destroy', $booking) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-action btn-danger" title="Excluir" data-confirm="Excluir?"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $bookings->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhum agendamento online recebido.</p>
        @endif
    </div>
</div>
@endsection
