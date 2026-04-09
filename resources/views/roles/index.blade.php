@extends('layouts.app', ['title' => 'Perfis'])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Perfis de Acesso</h2>
        <a href="{{ route('roles.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fas fa-plus mr-2"></i> Novo Perfil
        </a>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($roles as $role)
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-lg">{{ $role->name }}</h3>
            <span class="px-2 py-1 text-xs rounded-full bg-gray-100">{{ $role->users_count ?? 0 }} usuários</span>
        </div>
        <p class="text-gray-500 text-sm mb-4">{{ $role->description ?? 'Sem descrição' }}</p>
        <div class="flex justify-end gap-2">
            <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-edit"></i></a>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center text-gray-500 py-12">Nenhum perfil encontrado.</div>
    @endforelse
</div>
@endsection
