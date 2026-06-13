@extends('layouts.adminlte', ['title' => 'Comunicação - ' . $tutor->name])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Histórico de Comunicação</h3>
        <div class="card-tools">
            <a href="{{ route('tutors.show', $tutor) }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Dados do Tutor</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Nome:</strong> {{ $tutor->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Telefone:</strong> {{ $tutor->phone ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Email:</strong> {{ $tutor->email ?? '-' }}
                            </div>
                        </div>
                        @if($tutor->pets->count() > 0)
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <strong>Pets:</strong>
                                @foreach($tutor->pets as $pet)
                                    <span class="badge badge-info">{{ $pet->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($logs->count() > 0)
            @php $currentDate = null; @endphp
            @foreach($logs as $log)
                @php $logDate = $log->sent_at ? $log->sent_at->format('Y-m-d') : $log->created_at->format('Y-m-d'); @endphp
                @if($currentDate !== $logDate)
                    @php $currentDate = $logDate; @endphp
                    <h5 class="mt-3 mb-3 text-muted border-bottom pb-2">
                        {{ $log->sent_at ? $log->sent_at->format('d/m/Y') : $log->created_at->format('d/m/Y') }}
                    </h5>
                @endif
                <div class="timeline-item mb-3 pl-4 border-left border-primary">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @php $typeLabels = ['vaccination_reminder' => 'Lembrete Vacina', 'appointment_reminder' => 'Lembrete Consulta', 'birthday' => 'Aniversário', 'recall' => 'Recall', 'custom' => 'Personalizado']; $statusLabels = ['pending' => 'Pendente', 'sent' => 'Enviado', 'failed' => 'Falhou']; @endphp
                            <span class="badge badge-info">{{ $typeLabels[$log->type] ?? $log->type }}</span>
                            <span class="badge badge-secondary">{{ $log->channel }}</span>
                            <span class="badge {{ $log->status == 'sent' ? 'badge-success' : ($log->status == 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                {{ $statusLabels[$log->status] ?? $log->status }}
                            </span>
                        </div>
                        <small class="text-muted">
                            {{ $log->sent_at ? $log->sent_at->format('H:i') : $log->created_at->format('H:i') }}
                        </small>
                    </div>
                    <div class="mt-1">
                        <strong>Destino:</strong> {{ $log->destination ?? '-' }}
                    </div>
                    <div class="mt-1">
                        <strong>Mensagem:</strong>
                        <p class="mb-0 text-muted">{{ Str::limit($log->message, 200) }}</p>
                    </div>
                </div>
            @endforeach
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @else
        <p class="text-center text-muted">Nenhuma comunicação registrada.</p>
        @endif
    </div>
</div>
@endsection
