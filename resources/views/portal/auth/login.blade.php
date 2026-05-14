@extends('portal.layouts.app', ['title' => 'Login'])

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Portal do Tutor</h1>
            <p class="text-gray-500 text-sm mt-1">Acesse sua conta</p>
        </div>

        <form method="POST" action="{{ route('portal.login.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                    Lembrar-me
                </label>
                <a href="{{ route('portal.password.request') }}" class="text-sm text-blue-600 hover:text-blue-700">
                    Esqueceu a senha?
                </a>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                Entrar
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Não tem conta?
            <a href="{{ route('portal.register') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                Cadastre-se
            </a>
        </p>
    </div>
</div>
@endsection
