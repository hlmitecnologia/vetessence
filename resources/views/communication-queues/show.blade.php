@extends('layouts.adminlte', ['title' => 'Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('communication-queues.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Tutor:</strong>
                <p>{{ $communicationQueue->tutor->user->name ?? $communicationQueue->tutor->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $communicationQueue->pet->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Modelo:</strong>
                <p>{{ $communicationQueue->template->name ?? '-' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <strong>Canal:</strong>
                <p>{{ strtoupper($communicationQueue->channel ?? '-') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Destino:</strong>
                <p>{{ $communicationQueue->destination ?? '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Status:</strong>
                <p>
                    @php
                        $statusLabels = ['pending' => 'Pendente', 'processing' => 'Processando', 'sent' => 'Enviado', 'failed' => 'Falhou', 'cancelled' => 'Cancelado'];
                        $statusColors = ['pending' => 'warning', 'processing' => 'info', 'sent' => 'success', 'failed' => 'danger', 'cancelled' => 'secondary'];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$communicationQueue->status] ?? 'secondary' }}">
                        {{ $statusLabels[$communicationQueue->status] ?? $communicationQueue->status }}
                    </span>
                </p>
            </div>
            <div class="col-md-3">
                <strong>Agendado:</strong>
                <p>{{ $communicationQueue->scheduled_at ? $communicationQueue->scheduled_at->format('d/m/Y H:i') : 'Imediato' }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Enviado em:</strong>
                <p>{{ $communicationQueue->sent_at ? $communicationQueue->sent_at->format('d/m/Y H:i:s') : '-' }}</p>
            </div>
        </div>
        @if($communicationQueue->message_content)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Mensagem:</strong>
                <div class="p-3 bg-light rounded">
                    {!! $communicationQueue->message_content !!}
                </div>
            </div>
        </div>
        @endif
        @if($communicationQueue->error_message)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Erro:</strong>
                <p class="text-danger">{{ $communicationQueue->error_message }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
