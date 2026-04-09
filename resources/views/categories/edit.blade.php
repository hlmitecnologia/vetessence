@extends('layouts.app', ['title' => 'Editar Categoria'])

@section('header')
    <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Categoria</h2>
@endsection

@section('content')
<form action="{{ route('categories.update', $category) }}" method="POST" class="max-w-xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="type" class="w-full px-4 py-2 border rounded-lg">
                    <option value="product" {{ $category->type == 'product' ? 'selected' : '' }}>Produto</option>
                    <option value="service" {{ $category->type == 'service' ? 'selected' : '' }}>Serviço</option>
                    <option value="vaccine" {{ $category->type == 'vaccine' ? 'selected' : '' }}>Vacina</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" value="{{ $category->name }}" class="w-full px-4 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('categories.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
