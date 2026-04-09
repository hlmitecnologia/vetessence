@extends('layouts.adminlte', ['title' => 'Perfil'])

@section('header')
    <a href="{{ route('roles.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
    <h2 class="ml-4 text-lg font-semibold">{{ $role->name }}</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="font-semibold mb-4">Usuários com este perfil</h3>
        @if($role->users->count() > 0)
        <ul class="space-y-2">
            @foreach($role->users as $user)
            <li class="flex items-center"><i class="fas fa-user mr-2 text-gray-400"></i>{{ $user->name }}</li>
            @endforeach
        </ul>
        @else
        <p class="text-gray-500">Nenhum usuário com este perfil.</p>
        @endif
    </div>
    <div class="flex justify-between">
        <a href="{{ route('roles.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left mr-2"></i>Voltar</a>
        <a href="{{ route('roles.edit', $role) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar</a>
    </div>
</div>
@endsection
