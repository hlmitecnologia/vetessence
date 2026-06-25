@extends('portal.layouts.app', ['title' => 'Login'])

@push('styles')
<style>
.auth-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 5rem);
    padding: 2rem 1rem;
    background: {{ branding('sidebar_bg', '#051c12') }};
}
</style>
@endpush

@section('content')
@php
    $primary = branding('primary_color', '#455e36');
    $logoUrl = branding_logo_url();
    $hasLogo = (bool) $logoUrl;
    $showName = branding('show_clinic_name', '0') === '1';
@endphp
<div class="auth-container">
    <div class="w-full max-w-md portal-card p-8 sm:p-10">
        <div class="text-center mb-8">
            @if($hasLogo)
                <img src="{{ $logoUrl }}" width="96" alt="Logo" class="mx-auto mb-4">
            @else
                <i class="fas fa-paw text-5xl mb-4" style="color: {{ $primary }}"></i>
            @endif
            @if($showName)
                <h1 class="text-3xl font-bold text-gray-800">{{ branding('clinic_name', 'VetEssence') }}</h1>
            @endif
            <p class="text-lg text-gray-500 mt-2">Portal do Tutor — Acesse sua conta</p>
        </div>

        <form method="POST" action="{{ route('portal.login.store') }}">
            @csrf

            <div class="mb-5">
                <label class="portal-label">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="portal-input pl-12">
                </div>
            </div>

            <div class="mb-5">
                <label class="portal-label">Senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="portal-input pl-12">
                </div>
            </div>

            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center gap-2 text-base text-gray-600 touch-target-sm">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 w-5 h-5">
                    Lembrar-me
                </label>
                <a href="{{ route('portal.password.request') }}" class="text-base font-medium" style="color: {{ $primary }}">
                    Esqueceu a senha?
                </a>
            </div>

            <button type="submit"
                class="portal-btn w-full text-white font-semibold text-lg"
                style="background: {{ $primary }}">
                <i class="fas fa-sign-in-alt"></i>
                Entrar
            </button>
        </form>

        <p class="text-center text-base text-gray-500 mt-8">
            Não tem conta?
            <a href="{{ route('portal.register') }}" class="font-semibold" style="color: {{ $primary }}">
                Cadastre-se
            </a>
        </p>
    </div>
</div>
@endsection
