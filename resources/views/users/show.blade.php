@extends('layouts.adminlte', ['title' => 'Usuário'])

@section('header')
    <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $user->name }}</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-2xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold">{{ $user->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><h4 class="text-xs text-gray-500 uppercase">Email</h4><p>{{ $user->email }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Telefone</h4><p>{{ $user->phone ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Perfil</h4><p>{{ $user->role->name ?? '-' }}</p></div>
            <div><h4 class="text-xs text-gray-500 uppercase">Criado em</h4><p>{{ $user->created_at->format('d/m/Y') }}</p></div>
        </div>
    </div>
    <div class="mt-6 flex justify-between">
        <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
