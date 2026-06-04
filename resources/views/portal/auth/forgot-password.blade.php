@extends('portal.layouts.app', ['title' => 'Recuperar Senha'])

@section('content')
@php
    $primary = branding('primary_color', '#455e36');
    $logoUrl = branding_logo_url();
    $hasLogo = (bool) $logoUrl;
    $showName = branding('show_clinic_name', '0') === '1';
@endphp
<style>.portal-input:focus{box-shadow:0 0 0 2px {{ $primary }};border-color:{{ $primary }};outline:none}</style>
<div class="min-h-[70vh] flex items-center justify-center" style="background: {{ branding('sidebar_bg', '#051c12') }};">
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
            <p class="text-gray-500 text-sm mt-1">Receba um link para redefinir sua senha</p>
        </div>

        <form method="POST" action="{{ route('portal.password.email') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="portal-input w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg transition text-sm">
                </div>
            </div>

            <button type="submit"
                class="w-full text-white font-medium py-2.5 rounded-lg transition text-sm"
                style="background: {{ $primary }}; hover:background: {{ $primary }}cc">
                Enviar link
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('portal.login') }}" style="color: {{ $primary }}">
                <i class="fas fa-arrow-left mr-1"></i>Voltar ao login
            </a>
        </p>
    </div>
</div>
@endsection
