@extends('portal.layouts.app')
@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <h2 class="text-2xl font-bold mb-4">Exames</h2>
    @forelse($exams as $e)
        <div class="bg-white rounded-lg shadow p-4 mb-3">
            <div class="flex justify-between">
                <strong>{{ $e->pet->name ?? '-' }}</strong>
                <small class="text-gray-500">{{ $e->date ? $e->date->format('d/m/Y') : '-' }}</small>
            </div>
            <p class="text-sm mt-1">{{ $e->type ?? $e->name ?? 'Exame' }}</p>
            @if($e->result)<p class="text-sm text-gray-600">Resultado: {{ $e->result }}</p>@endif
        </div>
    @empty
        <p class="text-gray-500">Nenhum exame encontrado.</p>
    @endforelse
</div>
@endsection
