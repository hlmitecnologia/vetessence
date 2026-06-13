@extends('layouts.adminlte', ['title' => 'Triagem'])
@section('content')
    <div class="card"><div class="card-body">
        <p><strong>Pet:</strong> {{ $triage->pet->name ?? '-' }}</p>
        <p><strong>Chegada:</strong> {{ optional($triage->check_in_at)->format('d/m/Y H:i') }}</p>
        <p><strong>Severidade:</strong> <span class="badge badge-{{ $triage->severity === 'red' ? 'danger' : ($triage->severity === 'orange' ? 'warning' : ($triage->severity === 'yellow' ? 'info' : 'success')) }}">{{ strtoupper($triage->severity) }}</span></p>
        <p><strong>Queixa:</strong> {!! $triage->chief_complaint !!}</p>
        @php $statusLabels = ['waiting' => 'Aguardando', 'in_progress' => 'Em Atendimento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado']; @endphp
        <p><strong>Status:</strong> {{ $statusLabels[$triage->status] ?? $triage->status }}</p>
        <a href="{{ route('triage.edit', $triage->id) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('triage.index') }}" class="btn btn-secondary">Voltar</a>
    </div></div>
@endsection
