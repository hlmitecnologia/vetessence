@extends('layouts.adminlte', ['title' => 'Vacina'])

@section('header')
    <a href="{{ route('vaccinations.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Vacina - {{ $vaccination->pet->name }}</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6">
            <div><h4 class="text-xs text-gray-500 uppercase">Pet</h4><p class="font-semibold">{{ $vaccination->pet->name }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Vacina</h4><p class="font-semibold">{{ $vaccination->vaccine }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Data</h4><p>{{ $vaccination->date->format('d/m/Y') }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Próxima Dose</h4><p>{{ $vaccination->next_date ? $vaccination->next_date->format('d/m/Y') : '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Lote</h4><p>{{ $vaccination->batch ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Fabricante</h4><p>{{ $vaccination->manufacturer ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Veterinário</h4><p>{{ $vaccination->vet->name ?? '-' }}</p></div>
        </div>
        @if($vaccination->notes)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg"><p>{{ $vaccination->notes }}</p></div>
        @endif
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('vaccinations.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('vaccinations.edit', $vaccination) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
