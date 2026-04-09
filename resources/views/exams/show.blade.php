@extends('layouts.adminlte', ['title' => 'Exame'])

@section('header')
    <a href="{{ route('exams.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Exame - {{ $exam->pet->name ?? '-' }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">Pet</h4><p class="font-semibold">{{ $exam->pet->name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Tipo</h4><p class="font-semibold">{{ $exam->type }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Data Solicitação</h4><p>{{ $exam->requested_date->format('d/m/Y') }}</p></div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase">Status</h4>
                @php $statusLabels = ['requested' => 'Solicitado', 'collected' => 'Coletado', 'analyzing' => 'Analisando', 'ready' => 'Pronto', 'delivered' => 'Entregue']; @endphp
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $statusLabels[$exam->status] ?? $exam->status }}</span>
            </div>
        </div>
        @if($exam->result)
        <div class="p-4 bg-green-50 rounded-lg mb-4">
            <h4 class="text-xs text-gray-500 uppercase mb-2">Resultado</h4>
            <p class="whitespace-pre-line">{{ $exam->result }}</p>
        </div>
        @endif
        @if($exam->notes)
        <div class="p-4 bg-gray-50 rounded-lg"><h4 class="text-xs text-gray-500 uppercase mb-1">Observações</h4><p>{{ $exam->notes }}</p></div>
        @endif
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('exams.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('exams.edit', $exam) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
