@extends('portal.layouts.app', ['title' => 'Receitas'])

@section('content')
<h1 class="portal-section-title text-2xl sm:text-3xl mb-6">
    <i class="fas fa-prescription"></i>
    Receitas
</h1>

@forelse($prescriptions as $p)
<div class="portal-card p-6 mb-4 portal-fade-in">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-lg font-bold text-gray-800">{{ $p->medicalRecord->pet->name ?? 'Pet' }}</h3>
        <span class="text-base text-gray-500">{{ $p->created_at->format('d/m/Y') }}</span>
    </div>
    <p class="text-base text-gray-700 mt-1 font-medium">{{ $p->medication ?? $p->description ?? 'Medicamento' }}</p>
    @if($p->dosage)
    <p class="text-base text-gray-600 mt-1">{{ $p->dosagem ?? $p->dosage }}</p>
    @endif
    @if($p->notes)
    <p class="text-base text-gray-500 italic mt-2">{{ Str::limit($p->notes, 200) }}</p>
    @endif
</div>
@empty
<div class="portal-card p-12 portal-empty">
    <i class="fas fa-prescription"></i>
    <p>Nenhuma receita encontrada.</p>
</div>
@endforelse
@endsection
