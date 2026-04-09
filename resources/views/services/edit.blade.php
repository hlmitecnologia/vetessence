@extends('layouts.app', ['title' => 'Editar Serviço'])

@section('header')
    <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Serviço</h2>
@endsection

@section('content')
<form action="{{ route('services.update', $service) }}" method="POST" class="max-w-2xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Nome</label><input type="text" name="name" value="{{ $service->name }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Preço</label><input type="number" name="price" value="{{ $service->price }}" step="0.01" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Duração (min)</label><input type="number" name="duration" value="{{ $service->duration }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div class="md:col-span-2"><label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="rounded text-indigo-600"> <span class="ml-2">Serviço Ativo</span></label></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('services.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
