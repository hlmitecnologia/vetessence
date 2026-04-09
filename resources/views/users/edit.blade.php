@extends('layouts.adminlte', ['title' => 'Editar Usuário'])

@section('header')
    <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Editar Usuário</h2>
@endsection

@section('content')
<form action="{{ route('users.update', $user) }}" method="POST" class="max-w-xl mx-auto">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome</label><input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label><input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Deixe em branco para manter"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label><input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded text-indigo-600"> <span class="ml-2">Usuário Ativo</span></label></div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('users.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
