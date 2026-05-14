@extends('layouts.adminlte', ['title' => 'Logs de Notificação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Logs de Notificação</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <select name="type" class="form-control">
                    <option value="">Todos os tipos</option>
                    <option value="vaccination_reminder" {{ request('type') == 'vaccination_reminder' ? 'selected' : '' }}>Lembrete de Vacina</option>
                    <option value="appointment_reminder" {{ request('type') == 'appointment_reminder' ? 'selected' : '' }}>Lembrete de Consulta</option>
                    <option value="birthday" {{ request('type') == 'birthday' ? 'selected' : '' }}>Aniversário</option>
                    <option value="recall" {{ request('type') == 'recall' ? 'selected' : '' }}>Recall</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="channel" class="form-control">
                    <option value="">Todos os canais</option>
                    <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>E-mail</option>
                    <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ request('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviado</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Falhou</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($logs->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Tutor</th>
                    <th>Tipo</th>
                    <th>Canal</th>
                    <th>Destino</th>
                    <th>Status</th>
                    <th>Enviado em</th>
                    <th style="width: 80px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->pet->name ?? '-' }}</td>
                    <td>{{ $log->tutor->user->name ?? $log->tutor->name ?? '-' }}</td>
                    <td>
                        @php
                            $typeLabels = ['vaccination_reminder' => 'Lembrete Vacina', 'appointment_reminder' => 'Lembrete Consulta', 'birthday' => 'Aniversário', 'recall' => 'Recall'];
                        @endphp
                        {{ $typeLabels[$log->type] ?? $log->type }}
                    </td>
                    <td>{{ strtoupper($log->channel ?? '-') }}</td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $log->destination ?? '-' }}</td>
                    <td>
                        @php $statusColors = ['sent' => 'success', 'failed' => 'danger']; @endphp
                        <span class="badge badge-{{ $statusColors[$log->status] ?? 'secondary' }}">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td>{{ $log->sent_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('notification-logs.show', $log) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
        @else
        <p class="text-center text-muted">Nenhum log encontrado.</p>
        @endif
    </div>
</div>
@endsection
