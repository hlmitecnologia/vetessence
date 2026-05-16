@extends('layouts.mobile', ['title' => 'Receitas'])
@section('content')
    <h5 class="mb-3"><i class="fas fa-prescription-bottle text-info"></i> Últimas Receitas</h5>
    @php
        $prescriptions = \App\Models\Prescription::with(['medicalRecord.pet'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    @endphp
    @forelse($prescriptions as $p)
        <div class="card p-3 mb-2">
            <div class="d-flex justify-content-between">
                <strong>{{ $p->medicalRecord->pet->name ?? '-' }}</strong>
                <small class="text-muted">{{ $p->created_at->format('d/m') }}</small>
            </div>
            <small>{{ $p->medication }} {{ $p->dosage ? '- ' . $p->dosage : '' }}</small>
            <a href="{{ route('prescriptions.show', $p) }}" class="btn btn-sm btn-outline-info mt-2">Ver Receita</a>
        </div>
    @empty
        <p class="text-muted text-center">Nenhuma receita encontrada.</p>
    @endforelse
@endsection
