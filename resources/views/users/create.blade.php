@extends('layouts.adminlte', ['title' => 'Novo Usuário'])

@section('header')
    <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">Novo Usuário</h2>
@endsection

@section('content')
<form action="{{ route('users.store') }}" method="POST" class="max-w-xl mx-auto">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Senha *</label><input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha *</label><input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label><input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border rounded-lg"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil</label>
                <select name="role_id" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Selecione...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('users.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg"><i class="fas fa-save mr-2"></i> Salvar</button>
        </div>
    </div>
</form>
@endsection
