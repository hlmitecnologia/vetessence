@extends('portal.layouts.app', ['title' => 'Recuperar Senha'])

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Recuperar Senha</h1>
            <p class="text-gray-500 text-sm mt-1">Receba um link para redefinir sua senha</p>
        </div>

        <form method="POST" action="{{ route('portal.password.email') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                Enviar link
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('portal.login') }}" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Voltar ao login
            </a>
        </p>
    </div>
</div>
@endsection
