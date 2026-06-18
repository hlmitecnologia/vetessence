@extends('layouts.adminlte', ['title' => 'Log de Notificação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log de Notificação</h3>
        <div class="card-tools">
            <a href="{{ route('notification-logs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $notificationLog->pet->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Tutor:</strong>
                <p>{{ $notificationLog->tutor->name ?? $notificationLog->tutor->user->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Tipo:</strong>
                @php $typeLabels = ['nfse_emission_error' => 'Erro NFSe', 'nfe_emission_error' => 'Erro NFe', 'appointment_reminder' => 'Lembrete Consulta', 'vaccination_reminder' => 'Lembrete Vacina', 'birthday' => 'Aniversário', 'payment_received' => 'Pagamento Recebido', 'invoice_overdue' => 'Fatura Vencida']; @endphp
                <p>{{ $typeLabels[$notificationLog->type] ?? $notificationLog->type }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <strong>Canal:</strong>
                <p>{{ strtoupper($notificationLog->channel ?? '-') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Destino:</strong>
                <p>{{ $notificationLog->destination ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Status:</strong>
                <p>
                    @php $statusColors = ['sent' => 'success', 'failed' => 'danger']; @endphp
                    <span class="badge badge-{{ $statusColors[$notificationLog->status] ?? 'secondary' }}">
                        {{ ucfirst($notificationLog->status) }}
                    </span>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Enviado em:</strong>
                <p>{{ $notificationLog->sent_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
        @if($notificationLog->message)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Mensagem:</strong>
                <div class="p-3 bg-light rounded">
                    {!! $notificationLog->message !!}
                </div>
            </div>
        </div>
        @endif
        @if($notificationLog->error_message)
        <div class="row mt-3">
            <div class="col-md-12">
                <strong>Erro:</strong>
                <p class="text-danger">{{ $notificationLog->error_message }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
