@extends('portal.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-4">{{ $pet->name }}</h2>
    @forelse($pet->medicalRecords as $r)
        <div class="bg-white rounded-lg shadow p-4 mb-3">
            <div class="flex justify-between">
                <strong>{{ $r->created_at->format('d/m/Y H:i') }}</strong>
                <small class="text-gray-500">{{ $r->vet->name ?? 'Vet' }}</small>
            </div>
            <p class="text-sm mt-1">{{ $r->diagnosis ?? $r->chief_complaint ?? 'Sem diagnostico' }}</p>
        </div>
    @empty
        <p class="text-gray-500">Nenhum prontuario encontrado.</p>
    @endforelse
</div>
@endsection
