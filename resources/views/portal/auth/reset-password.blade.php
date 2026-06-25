@extends('portal.layouts.app', ['title' => 'Redefinir Senha'])

@push('styles')
<style>
.auth-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 5rem);
    padding: 2rem 1rem;
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
            <p class="text-lg text-gray-500 mt-2">Escolha uma nova senha</p>
        </div>

        <form method="POST" action="{{ route('portal.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-5">
                <label class="portal-label">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                        class="portal-input pl-12 bg-gray-50">
                </div>
            </div>

            <div class="mb-5">
                <label class="portal-label">Nova senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="password" required
                        class="portal-input pl-12">
                </div>
            </div>

            <div class="mb-8">
                <label class="portal-label">Confirmar nova senha</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="password" name="password_confirmation" required
                        class="portal-input pl-12">
                </div>
            </div>

            <button type="submit"
                class="portal-btn w-full text-white font-semibold text-lg"
                style="background: {{ $primary }}">
                <i class="fas fa-key"></i>
                Redefinir senha
            </button>
        </form>
    </div>
</div>
@endsection
