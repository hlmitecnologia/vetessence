@extends('layouts.adminlte', ['title' => 'Fila de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Fila de Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-queues.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processando</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Enviado</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Falhou</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
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
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Data início">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($queues->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tutor</th>
                    <th>Pet</th>
                    <th>Canal</th>
                    <th>Destino</th>
                    <th>Status</th>
                    <th>Agendado</th>
                    <th>Enviado</th>
                    <th style="width: 80px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($queues as $queue)
                <tr>
                    <td>{{ $queue->tutor->user->name ?? $queue->tutor->name ?? '-' }}</td>
                    <td>{{ $queue->pet->name ?? '-' }}</td>
                    <td>{{ strtoupper($queue->channel ?? '-') }}</td>
                    <td class="text-truncate" style="max-width: 150px;">{{ $queue->destination ?? '-' }}</td>
                    <td>
                        @php
                            $statusLabels = ['pending' => 'Pendente', 'processing' => 'Processando', 'sent' => 'Enviado', 'failed' => 'Falhou', 'cancelled' => 'Cancelado'];
                            $statusColors = ['pending' => 'warning', 'processing' => 'info', 'sent' => 'success', 'failed' => 'danger', 'cancelled' => 'secondary'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$queue->status] ?? 'secondary' }}">
                            {{ $statusLabels[$queue->status] ?? $queue->status }}
                        </span>
                    </td>
                    <td data-order="{{ $queue->scheduled_at?->timestamp ?? 0 }}">{{ $queue->scheduled_at ? $queue->scheduled_at->format('d/m/Y H:i') : 'Imediato' }}</td>
                    <td data-order="{{ $queue->sent_at?->timestamp ?? 0 }}">{{ $queue->sent_at ? $queue->sent_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('communication-queues.show', $queue) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma comunicação na fila.</p>
        @endif
    </div>
</div>
@endsection
