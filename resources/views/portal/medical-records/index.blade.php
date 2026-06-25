@extends('portal.layouts.app', ['title' => 'Prontuários'])

@section('content')
<h1 class="portal-section-title text-2xl sm:text-3xl mb-6">
    <i class="fas fa-notes-medical"></i>
    Prontuários
</h1>

@forelse($records as $r)
<div class="portal-card p-6 mb-4 portal-fade-in">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-bold text-gray-800">{{ $r->pet->name ?? '-' }}</h3>
        <span class="text-base text-gray-500">{{ $r->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <p class="text-base text-gray-600 mt-2">{{ Str::limit($r->diagnosis ?? $r->chief_complaint ?? 'Sem diagnóstico', 200) }}</p>
    <p class="text-sm text-gray-400 mt-2">{{ $r->vet->name ?? 'Veterinário' }}</p>
</div>
@empty
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-notes-medical"></i>
    <p>Nenhum prontuário encontrado.</p>
</div>
@endforelse
@endsection
