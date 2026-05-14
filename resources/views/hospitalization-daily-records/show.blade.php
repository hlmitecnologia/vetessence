@extends('layouts.adminlte', ['title' => 'Registro Diário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registro Diário - {{ $hospitalizationDailyRecord->hospitalization->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalization-daily-records.edit', $hospitalizationDailyRecord) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('hospitalization-daily-records.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Data:</strong>
                <p>{{ $hospitalizationDailyRecord->record_date->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Turno:</strong>
                <p>
                    @php $shiftLabels = ['morning' => 'Manhã', 'afternoon' => 'Tarde', 'night' => 'Noite']; @endphp
                    {{ $shiftLabels[$hospitalizationDailyRecord->shift] ?? $hospitalizationDailyRecord->shift }}
                </p>
            </div>
            <div class="col-md-4">
                <strong>Pet:</strong>
                <p>{{ $hospitalizationDailyRecord->hospitalization->pet->name ?? '-' }}</p>
            </div>
        </div>

        <hr>
        <h6 class="text-uppercase text-muted">SOAP</h6>
        @if($hospitalizationDailyRecord->subjective)
        <div class="mt-3">
            <strong>Subjetivo:</strong>
            <p>{{ $hospitalizationDailyRecord->subjective }}</p>
        </div>
        @endif
        @if($hospitalizationDailyRecord->objective)
        <div class="mt-3">
            <strong>Objetivo:</strong>
            <p>{{ $hospitalizationDailyRecord->objective }}</p>
        </div>
        @endif
        @if($hospitalizationDailyRecord->assessment)
        <div class="mt-3">
            <strong>Avaliação:</strong>
            <p>{{ $hospitalizationDailyRecord->assessment }}</p>
        </div>
        @endif
        @if($hospitalizationDailyRecord->plan)
        <div class="mt-3">
            <strong>Plano:</strong>
            <p>{{ $hospitalizationDailyRecord->plan }}</p>
        </div>
        @endif

        <hr>
        <h6 class="text-uppercase text-muted">Sinais Vitais</h6>
        <div class="row mt-3">
            <div class="col-md-3">
                <strong>Temperatura:</strong>
                <p>{{ $hospitalizationDailyRecord->temperature ? $hospitalizationDailyRecord->temperature . '°C' : '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>FC:</strong>
                <p>{{ $hospitalizationDailyRecord->heart_rate ? $hospitalizationDailyRecord->heart_rate . ' bpm' : '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>FR:</strong>
                <p>{{ $hospitalizationDailyRecord->respiratory_rate ? $hospitalizationDailyRecord->respiratory_rate . ' mpm' : '-' }}</p>
            </div>
        </div>

        <hr>
        <h6 class="text-uppercase text-muted">Avaliação Clínica</h6>
        <div class="row mt-3">
            @php
                $appetiteLabels = ['normal' => 'Normal', 'reduced' => 'Reduzido', 'absent' => 'Ausente'];
                $hydrationLabels = ['normal' => 'Normal', 'dehydrated' => 'Desidratado', 'overhydrated' => 'Sobre-hidratado'];
            @endphp
            <div class="col-md-3">
                <strong>Apetite:</strong>
                <p>{{ $appetiteLabels[$hospitalizationDailyRecord->appetite] ?? $hospitalizationDailyRecord->appetite ?? '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Hidratação:</strong>
                <p>{{ $hydrationLabels[$hospitalizationDailyRecord->hydration] ?? $hospitalizationDailyRecord->hydration ?? '-' }}</p>
            </div>
            <div class="col-md-3">
                <strong>Urição:</strong>
                <p>{{ ucfirst($hospitalizationDailyRecord->urination ?? '-') }}</p>
            </div>
            <div class="col-md-3">
                <strong>Defecação:</strong>
                <p>{{ ucfirst($hospitalizationDailyRecord->defecation ?? '-') }}</p>
            </div>
        </div>

        @if($hospitalizationDailyRecord->medications_given)
        <div class="mt-3">
            <strong>Medicações Administradas:</strong>
            <p>{{ $hospitalizationDailyRecord->medications_given }}</p>
        </div>
        @endif

        @if($hospitalizationDailyRecord->observations)
        <div class="mt-3">
            <strong>Observações:</strong>
            <p>{{ $hospitalizationDailyRecord->observations }}</p>
        </div>
        @endif

        <div class="mt-3">
            <strong>Profissional:</strong>
            <p>{{ $hospitalizationDailyRecord->user->name ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
