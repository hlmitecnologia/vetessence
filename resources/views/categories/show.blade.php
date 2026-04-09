@extends('layouts.app', ['title' => 'Categoria'])

@section('header')
    <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $category->name }}</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-2 gap-6">
            <div><h4 class="text-xs text-gray-500 uppercase">Tipo</h4><p>{{ ucfirst($category->type) }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Categoria Pai</h4><p>{{ $category->parent->name ?? '-' }}</p></div>
        </div>
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('categories.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('categories.edit', $category) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
