@extends('layouts.adminlte', ['title' => 'Serviço'])

@section('header')
    <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $service->name }}</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div><h4 class="text-xs text-gray-500 uppercase">Preço</h4><p class="text-2xl font-bold">R$ {{ number_format($service->price, 2, ',', '.') }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Duração</h4><p>{{ $service->duration ? $service->duration . ' minutos' : '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Categoria</h4><p>{{ $service->category->name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Status</h4><span class="px-2 py-1 text-xs rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $service->is_active ? 'Ativo' : 'Inativo' }}</span></div>
        </div>
        @if($service->description)
        <div class="p-4 bg-gray-50 rounded-lg"><h4 class="text-xs text-gray-500 uppercase mb-1">Descrição</h4><p>{{ $service->description }}</p></div>
        @endif
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('services.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('services.edit', $service) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
