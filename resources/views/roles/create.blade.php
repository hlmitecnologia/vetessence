@extends('layouts.app', ['title' => 'Novo Perfil'])

@section('header')
    <a href="{{ route('roles.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Perfil</h2>
@endsection

@section('content')
<form action="{{ route('roles.store') }}" method="POST" class="max-w-xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label><input type="text" name="slug" value="{{ old('slug') }}" required class="w-full px-4 py-2 border rounded-lg" placeholder="ex: veterinario"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label><textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('roles.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
