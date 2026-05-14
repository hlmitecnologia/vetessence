@extends('layouts.adminlte', ['title' => 'Sessão de Terapia'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Sessão de Terapia</h3></div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Pet</dt>
            <dd class="col-sm-9">{{ $therapySession->pet->name ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Tipo</dt>
            <dd class="col-sm-9">{{ $therapySession->type }}</dd>
            <dt class="col-sm-3">Data/Hora</dt>
            <dd class="col-sm-9">{{ $therapySession->session_date->format('d/m/Y H:i') }}</dd>
            <dt class="col-sm-3">Terapeuta</dt>
            <dd class="col-sm-9">{{ $therapySession->therapist->name ?? '-' }}</dd>
            <dt class="col-sm-3">Duração</dt>
            <dd class="col-sm-9">{{ $therapySession->duration_minutes ? $therapySession->duration_minutes . ' min' : '-' }}</dd>
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">{{ $therapySession->status }}</dd>
            <dt class="col-sm-3">Anotações</dt>
            <dd class="col-sm-9">{{ $therapySession->notes ?? '-' }}</dd>
            <dt class="col-sm-3">Observações</dt>
            <dd class="col-sm-9">{{ $therapySession->observations ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('therapy-sessions.edit', $therapySession) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('therapy-sessions.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection
