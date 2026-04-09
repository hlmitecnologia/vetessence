@extends('layouts.app', ['title' => 'Convênio'])

@section('header')
    <a href="{{ route('convenios.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $convenio->name }}</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">CNPJ</h4><p>{{ $convenio->cnpj ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Plano</h4><p>{{ $convenio->plan_name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Desconto</h4><p>{{ $convenio->discount_percent ? $convenio->discount_percent . '%' : '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Limite/Mês</h4><p>{{ $convenio->max_consults_month ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Início</h4><p>{{ $convenio->start_date ? $convenio->start_date->format('d/m/Y') : '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Fim</h4><p>{{ $convenio->end_date ? $convenio->end_date->format('d/m/Y') : '-' }}</p></div>
        </div>
        @if($convenio->coverage)
        <div class="p-4 bg-gray-50 rounded-lg"><h4 class="text-xs text-gray-500 uppercase mb-1">Coberturas</h4><p>{{ $convenio->coverage }}</p></div>
        @endif
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('convenios.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('convenios.edit', $convenio) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
