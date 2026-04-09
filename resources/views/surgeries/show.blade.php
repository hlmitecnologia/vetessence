@extends('layouts.app', ['title' => 'Cirurgia'])

@section('header')
    <a href="{{ route('surgeries.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Cirurgia - {{ $surgery->pet->name ?? '-' }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">Pet</h4><p class="font-semibold">{{ $surgery->pet->name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Cirurgião</h4><p>{{ $surgery->vet->name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Data</h4><p>{{ $surgery->scheduled_date->format('d/m/Y H:i') }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Tipo</h4><p>{{ $surgery->surgery_type }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Anestesia</h4><p>{{ $surgery->anesthesia_type ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Duração</h4><p>{{ $surgery->surgery_duration ? $surgery->surgery_duration . ' min' : '-' }}</p></div>
        </div>
        @if($surgery->pre_op_diagnosis)
        <div class="p-4 bg-gray-50 rounded-lg mb-4"><h4 class="text-xs text-gray-500 uppercase mb-1">Diagnóstico Pré-op</h4><p>{{ $surgery->pre_op_diagnosis }}</p></div>
        @endif
        @if($surgery->protocol)
        <div class="p-4 bg-gray-50 rounded-lg mb-4"><h4 class="text-xs text-gray-500 uppercase mb-1">Protocolo</h4><p>{{ $surgery->protocol }}</p></div>
        @endif
        @if($surgery->post_op_notes)
        <div class="p-4 bg-green-50 rounded-lg mb-4"><h4 class="text-xs text-gray-500 uppercase mb-1">Pós-operatório</h4><p>{{ $surgery->post_op_notes }}</p></div>
        @endif
        @if($surgery->complications)
        <div class="p-4 bg-red-50 rounded-lg mb-4"><h4 class="text-xs text-gray-500 uppercase mb-1">Complicações</h4><p>{{ $surgery->complications }}</p></div>
        @endif
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('surgeries.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('surgeries.edit', $surgery) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
