@extends('layouts.mobile', ['title' => 'Triagem'])
@section('content')
    <h5 class="mb-3"><i class="fas fa-ambulance text-warning"></i> Triagem — Aguardando</h5>
    @php
        $triage = \App\Models\TriageRecord::with('pet')
            ->whereIn('status', ['waiting', 'in_consultation'])
            ->orderByRaw("CASE severity WHEN 'red' THEN 4 WHEN 'orange' THEN 3 WHEN 'yellow' THEN 2 ELSE 1 END DESC")
            ->orderBy('check_in_at')
            ->get();
    @endphp
    @forelse($triage as $t)
        <div class="card p-3 mb-2 border-left border-{{ $t->severity === 'red' ? 'danger' : ($t->severity === 'orange' ? 'warning' : 'info') }} border-4">
            <div class="d-flex justify-content-between">
                <strong>{{ $t->pet->name ?? '-' }}</strong>
                <span class="badge badge-{{ $t->severity === 'red' ? 'danger' : ($t->severity === 'orange' ? 'warning' : 'info') }}">{{ strtoupper($t->severity) }}</span>
            </div>
            <small class="text-muted">{!! $t->chief_complaint !!}</small>
            @php $statusLabels = ['waiting' => 'Aguardando', 'in_progress' => 'Em Atendimento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado']; @endphp
            <small class="text-muted">{{ optional($t->check_in_at)->format('H:i') }} — {{ $statusLabels[$t->status] ?? $t->status }}</small>
            <a href="{{ route('triage.show', $t) }}" class="btn btn-sm btn-outline-info mt-2">Detalhes</a>
        </div>
    @empty
        <p class="text-muted text-center">Nenhum paciente aguardando.</p>
    @endforelse
@endsection
