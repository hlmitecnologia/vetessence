@extends('layouts.adminlte', ['title' => 'Fila de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Fila de Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-queue.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Mensagem
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select name="status" id="status_filter" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(['pending' => 'Pendente', 'sent' => 'Enviado', 'failed' => 'Falhou', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="channel_filter">Canal</label>
                        <select name="channel" id="channel_filter" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            @foreach(['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'] as $val => $label)
                                <option value="{{ $val }}" {{ request('channel') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('communication-queue.index') }}" class="btn btn-default">Limpar Filtros</a>
                </div>
            </div>
        </form>

        @if($queue->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Destinatário</th>
                        <th>Tutor</th>
                        <th>Pet</th>
                        <th>Canal</th>
                        <th>Modelo</th>
                        <th>Agendado Para</th>
                        <th>Enviado Em</th>
                        <th>Status</th>
                        <th style="width: 80px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($queue as $item)
                    <tr>
                        <td>{{ $item->destination }}</td>
                        <td>{{ $item->tutor->name ?? '-' }}</td>
                        <td>{{ $item->pet->name ?? '-' }}</td>
                        <td>
                            @php
                                $channelIcons = ['whatsapp' => 'fab fa-whatsapp', 'email' => 'fas fa-envelope', 'sms' => 'fas fa-sms', 'push' => 'fas fa-bell'];
                                $channelLabels = ['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'];
                            @endphp
                            <i class="{{ $channelIcons[$item->channel] ?? 'fas fa-comment' }}"></i>
                            {{ $channelLabels[$item->channel] ?? $item->channel }}
                        </td>
                        <td>{{ $item->template->name ?? '-' }}</td>
                        <td>{{ $item->scheduled_at ? $item->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $item->sent_at ? $item->sent_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @php
                                $statusLabels = ['pending' => 'Pendente', 'sent' => 'Enviado', 'failed' => 'Falhou', 'cancelled' => 'Cancelado'];
                                $statusColors = ['pending' => 'warning', 'sent' => 'success', 'failed' => 'danger', 'cancelled' => 'secondary'];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                {{ $statusLabels[$item->status] ?? $item->status }}
                            </span>
                            @if($item->error_message)
                                <i class="fas fa-exclamation-circle text-danger ml-1" title="{{ $item->error_message }}"></i>
                            @endif
                        </td>
                        <td>
                            @if($item->status === 'pending')
                            <form action="{{ route('communication-queue.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Cancelar esta mensagem?')" class="btn btn-action btn-danger" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $queue->links() }}
        @else
        <p class="text-center text-muted">Nenhuma mensagem na fila.</p>
        @endif
    </div>
</div>
@endsection
