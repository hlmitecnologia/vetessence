@extends('layouts.adminlte', ['title' => 'Painel de Fluxo'])

@section('styles')
<style>
    .kanban-board {
        overflow-x: auto;
        display: flex;
        gap: 1rem;
        padding-bottom: 1rem;
        min-height: 70vh;
    }
    .kanban-column {
        min-width: 280px;
        max-width: 320px;
        flex: 1;
        background: #f4f6f9;
        border-radius: 0.5rem;
        padding: 0.75rem;
    }
    .kanban-column-header {
        font-weight: 600;
        font-size: 0.95rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0.75rem;
    }
    .kanban-card {
        background: #fff;
        border-radius: 0.375rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-left: 4px solid #6c757d;
    }
    .kanban-card .card-title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .kanban-card .card-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .kanban-card .card-actions {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    .kanban-card .card-actions form {
        display: inline;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Painel de Fluxo - Agendamentos</h3>
    </div>
    <div class="card-body">
        <div class="kanban-board">
            @forelse($statuses as $status)
            @php $appointmentsList = $appointments[$status] ?? collect(); @endphp
            <div class="kanban-column">
                <div class="kanban-column-header">
                    {{ ucfirst($status) }}
                    <span class="badge badge-secondary float-right">{{ $appointmentsList->count() }}</span>
                </div>
                @forelse($appointmentsList as $appointment)
                <div class="kanban-card">
                    <div class="card-title">
                        <i class="fas fa-paw text-primary"></i>
                        {{ $appointment->pet->name ?? 'Pet' }}
                    </div>
                    <div class="card-meta">
                        <div><i class="far fa-clock"></i> {{ $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('H:i') : '-' }}</div>
                        <div><i class="fas fa-user-md"></i> {{ $appointment->vet->name ?? $appointment->user->name ?? 'Veterinário' }}</div>
                    </div>
                    <div class="card-actions">
                        @foreach($statuses as $next)
                            @if($next !== $status)
                            <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $next }}">
                                <button type="submit" class="btn btn-action btn-sm btn-outline-secondary" title="Mover para {{ $next }}">
                                    <i class="fas fa-arrow-right"></i> {{ $next }}
                                </button>
                            </form>
                            @endif
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-muted text-center small">Nenhum agendamento</p>
                @endforelse
            </div>
            @empty
            <p class="text-center text-muted">Nenhum status configurado.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
