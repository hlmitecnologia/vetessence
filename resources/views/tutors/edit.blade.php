@extends('layouts.adminlte', ['title' => 'Editar Tutor'])

@section('header')
    <a href="{{ route('tutors.index') }}" class="text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="ml-4 text-lg font-semibold">Editar Tutor</h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('tutors.update', $tutor) }}" method="POST" class="bg-white rounded-xl shadow-sm p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                <input type="text" name="name" value="{{ old('name', $tutor->name) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CPF *</label>
                <input type="text" name="cpf" value="{{ old('cpf', $tutor->cpf) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $tutor->email) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                <input type="text" name="phone" value="{{ old('phone', $tutor->phone) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone Secundário</label>
                <input type="text" name="phone_secondary" value="{{ old('phone_secondary', $tutor->phone_secondary) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                <input type="text" name="address" value="{{ old('address', $tutor->address) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="city" value="{{ old('city', $tutor->city) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                <input type="text" name="state" value="{{ old('state', $tutor->state) }}" maxlength="2"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('tutors.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
