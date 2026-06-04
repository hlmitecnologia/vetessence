@extends('portal.layouts.app', ['title' => 'Cadastro'])

@section('content')
@php
    $primary = branding('primary_color', '#455e36');
    $logoUrl = branding_logo_url();
    $hasLogo = (bool) $logoUrl;
    $showName = branding('show_clinic_name', '0') === '1';
@endphp
<style>.portal-input:focus{box-shadow:0 0 0 2px {{ $primary }};border-color:{{ $primary }};outline:none}</style>
<div class="flex items-center justify-center overflow-y-auto" style="position: fixed; top: 4rem; left: 0; right: 0; bottom: 0; background: {{ branding('sidebar_bg', '#051c12') }};">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            @if($hasLogo)
                <img src="{{ $logoUrl }}" width="80" alt="Logo" class="mx-auto mb-3">
            @else
                <i class="fas fa-paw fa-3x mb-3" style="color: {{ $primary }}"></i>
            @endif
            @if($showName)
                <h1 class="text-2xl font-bold text-gray-800">{{ branding('clinic_name', 'VetEssence') }}</h1>
            @endif
            <p class="text-gray-500 text-sm mt-1">Portal do Tutor — Crie sua conta</p>
        </div>

        <form method="POST" action="{{ route('portal.register.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" required
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password_confirmation" required
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <button type="submit"
                class="w-full text-white font-medium py-2.5 rounded-lg transition text-sm"
                style="background: {{ $primary }}; hover:background: {{ $primary }}cc">
                Cadastrar
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Já tem conta?
            <a href="{{ route('portal.login') }}" style="color: {{ $primary }}">
                Entrar
            </a>
        </p>
    </div>
</div>
@endsection
