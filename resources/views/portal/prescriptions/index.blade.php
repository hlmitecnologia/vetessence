@extends('portal.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-4">Receitas</h2>
    @forelse($prescriptions as $p)
        <div class="bg-white rounded-lg shadow p-4 mb-3">
            <div class="flex justify-between">
                <strong>{{ $p->medicalRecord->pet->name ?? 'Pet' }}</strong>
                <small class="text-gray-500">{{ $p->created_at->format('d/m/Y') }}</small>
            </div>
            <p class="text-sm mt-1">{{ $p->medication ?? $p->description ?? 'Medicamento' }}</p>
            @if($p->dosage)<p class="text-sm text-gray-600">{{ $p->dosagem ?? $p->dosage }}</p>@endif
            @if($p->notes)<p class="text-sm text-gray-500 italic">{{ Str::limit($p->notes, 100) }}</p>@endif
        </div>
    @empty
        <p class="text-gray-500">Nenhuma receita encontrada.</p>
    @endforelse
</div>
@endsection
