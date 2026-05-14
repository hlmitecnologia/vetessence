@extends('layouts.adminlte', ['title' => 'Lembrete de Vacina'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lembrete de Vacina - {{ $vaccinationReminder->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('vaccination-reminders.edit', $vaccinationReminder) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('vaccination-reminders.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $vaccinationReminder->pet->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Vacina:</strong>
                <p>{{ $vaccinationReminder->vaccination->vaccine ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Data Agendada:</strong>
                <p>{{ $vaccinationReminder->scheduled_date->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <strong>Status:</strong>
                <p>
                    @php
                        $statusLabels = ['pending' => 'Pendente', 'sent' => 'Enviado', 'failed' => 'Falhou'];
                        $statusColors = ['pending' => 'warning', 'sent' => 'success', 'failed' => 'danger'];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$vaccinationReminder->status] ?? 'secondary' }}">
                        {{ $statusLabels[$vaccinationReminder->status] ?? $vaccinationReminder->status }}
                    </span>
                </p>
            </div>
            <div class="col-md-4">
                <strong>Canal:</strong>
                <p>{{ $vaccinationReminder->channel ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <strong>Enviado em:</strong>
                <p>{{ $vaccinationReminder->sent_at ? $vaccinationReminder->sent_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>
        @if($vaccinationReminder->error_message)
        <div class="row">
            <div class="col-md-12">
                <strong>Erro:</strong>
                <p class="text-danger">{{ $vaccinationReminder->error_message }}</p>
            </div>
        </div>
        @endif
        @if($vaccinationReminder->notes)
        <div class="row">
            <div class="col-md-12">
                <strong>Observações:</strong>
                <p>{{ $vaccinationReminder->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
