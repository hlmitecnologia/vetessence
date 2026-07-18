@extends('layouts.mobile', ['title' => 'Prontuários'])
@section('content')
    <h5 class="mb-3"><i class="fas fa-notes-medical text-success"></i> Últimos Prontuários</h5>
    @php
        $records = \App\Models\MedicalRecord::with(['pet', 'vet'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    @endphp
    @forelse($records as $r)
        <div class="card p-3 mb-2">
            <div class="d-flex justify-content-between">
                <strong>{{ $r->pet->name ?? '-' }}</strong>
                <small class="text-muted">{{ $r->created_at->format('d/m H:i') }}</small>
            </div>
            <small class="text-muted">{{ $r->vet->name ?? '-' }} | {{ Str::limit(strip_tags($r->diagnosis ?? $r->chief_complaint ?? ''), 80) }}</small>
            <a href="{{ route('medical-records.show', $r) }}" class="btn btn-sm btn-outline-success mt-2">Ver Prontuário</a>
        </div>
    @empty
        <p class="text-muted text-center">Nenhum prontuário encontrado.</p>
    @endforelse
@endsection
