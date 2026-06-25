@extends('portal.layouts.app', ['title' => 'Exames'])

@section('content')
<h1 class="portal-section-title text-2xl sm:text-3xl mb-6">
    <i class="fas fa-flask"></i>
    Exames
</h1>

@forelse($exams as $e)
<div class="portal-card p-6 mb-4 portal-fade-in">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-bold text-gray-800">{{ $e->pet->name ?? '-' }}</h3>
        <span class="text-base text-gray-500">{{ $e->date ? $e->date->format('d/m/Y') : '-' }}</span>
    </div>
    <p class="text-base text-gray-600 mt-1">{{ $e->type ?? $e->name ?? 'Exame' }}</p>
    @if($e->result)
    <div class="mt-3 text-base text-gray-700 bg-gray-50 rounded-xl p-4">
        <strong>Resultado:</strong> {!! $e->result !!}
    </div>
    @endif
</div>
@empty
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-flask"></i>
    <p>Nenhum exame encontrado.</p>
</div>
@endforelse
@endsection
