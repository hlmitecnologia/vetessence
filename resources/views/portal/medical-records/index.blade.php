@extends('portal.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-4">Prontuarios</h2>
    @forelse($records as $r)
        <div class="bg-white rounded-lg shadow p-4 mb-3">
            <div class="flex justify-between">
                <strong>{{ $r->pet->name ?? '-' }}</strong>
                <small class="text-gray-500">{{ $r->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <p class="text-sm mt-1">{{ Str::limit($r->diagnosis ?? $r->chief_complaint ?? 'Sem diagnostico', 150) }}</p>
            <small class="text-gray-400">{{ $r->vet->name ?? 'Vet' }}</small>
        </div>
    @empty
        <p class="text-gray-500">Nenhum prontuario encontrado.</p>
    @endforelse
</div>
@endsection
