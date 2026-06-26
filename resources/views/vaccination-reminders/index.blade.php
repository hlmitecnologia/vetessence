@extends('layouts.adminlte', ['title' => 'Lembretes de Vacinas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lembretes de Vacinas</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar por pet..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviado</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Falhou</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="pet_id" class="form-control">
                    <option value="">Todos os pets</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($reminders->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Vacina</th>
                    <th>Data Agendada</th>
                    <th>Status</th>
                    <th>Canal</th>
                    <th>Enviado em</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reminders as $reminder)
                <tr>
                    <td><strong>{{ $reminder->pet->name ?? '-' }}</strong></td>
                    <td>{{ $reminder->vaccination->vaccine ?? '-' }}</td>
                    <td data-order="{{ $reminder->scheduled_date?->timestamp ?? 0 }}">{{ $reminder->scheduled_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $statusLabels = ['pending' => 'Pendente', 'sent' => 'Enviado', 'failed' => 'Falhou'];
                            $statusColors = ['pending' => 'warning', 'sent' => 'success', 'failed' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$reminder->status] ?? 'secondary' }}">
                            {{ $statusLabels[$reminder->status] ?? $reminder->status }}
                        </span>
                    </td>
                    <td>{{ $reminder->channel ?? '-' }}</td>
                    <td data-order="{{ $reminder->sent_at?->timestamp ?? 0 }}">{{ $reminder->sent_at ? $reminder->sent_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('vaccination-reminders.show', $reminder) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $reminder->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-center text-muted">Nenhum lembrete encontrado.</p>
        @endif
    </div>
</div>

<!-- VaccinationReminder Modal -->
<div class="modal fade" id="vaccinationReminderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vaccinationReminderModalTitle">Novo Lembrete</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('vaccination-reminder-form', key('vaccination-reminder-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#vaccinationReminderModal').modal('hide'); });
        Livewire.on('vaccination-reminder-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('vaccinationReminderModalTitle').textContent = 'Novo Lembrete';
        $('#vaccinationReminderModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editVaccinationReminder', { id: id });
        document.getElementById('vaccinationReminderModalTitle').textContent = 'Editar Lembrete';
        $('#vaccinationReminderModal').modal('show');
    }
</script>
@endpush
