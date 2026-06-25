@extends('portal.layouts.app', ['title' => 'Prontuários - ' . $pet->name])

@section('content')
<div class="mb-4">
    <a href="{{ route('portal.dashboard') }}" class="text-base text-blue-600 hover:text-blue-700 touch-target-sm inline-flex items-center gap-1">
        <i class="fas fa-arrow-left"></i>Início
    </a>
</div>

<h1 class="portal-section-title text-2xl sm:text-3xl mb-6">
    <i class="fas fa-notes-medical"></i>
    Prontuários — {{ $pet->name }}
</h1>

@forelse($pet->medicalRecords as $r)
<div class="portal-card p-6 mb-4 portal-fade-in">
    <div class="flex justify-between items-start mb-2">
        <span class="text-lg font-bold text-gray-800">{{ $r->created_at->format('d/m/Y H:i') }}</span>
        <span class="text-base text-gray-500">{{ $r->vet->name ?? 'Veterinário' }}</span>
    </div>
    <p class="text-base text-gray-600 mt-2">{{ $r->diagnosis ?? $r->chief_complaint ?? 'Sem diagnóstico' }}</p>
</div>
@empty
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-notes-medical"></i>
    <p>Nenhum prontuário encontrado.</p>
</div>
@endforelse
@endsection
