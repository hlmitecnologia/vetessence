<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal do Tutor' }} - {{ branding('clinic_name', 'VetEssence') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ branding_favicon_url() }}">
    <style>{!! branding_css_vars() !!}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2 text-xl font-bold" style="color: var(--brand-primary, #455e36)">
                    @php
                        $logoUrl = branding_logo_url();
                        $hasLogo = (bool) $logoUrl;
                        $showName = branding('show_clinic_name', '0') === '1';
                    @endphp
                    @if($hasLogo)
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 32px; max-width: 100%; object-fit: contain;">
                    @else
                        <i class="fas fa-paw" style="color: var(--brand-primary, #455e36)"></i>
                    @endif
                    @if($showName)
                        <span class="text-gray-800">{{ branding('clinic_name', 'VetEssence') }}</span>
                    @endif
                </a>
                @auth('tutor')
                <div class="flex items-center gap-3">
                    <a href="{{ route('portal.docs.index') }}" class="text-sm text-gray-400 hover:text-blue-600 transition" title="Manual do Tutor">
                        <i class="fas fa-question-circle text-lg"></i>
                    </a>
                    <span class="text-sm text-gray-500 hidden sm:inline">{{ Auth::guard('tutor')->user()->name }}</span>
                    <form method="POST" action="{{ route('portal.logout') }}">
                        @csrf
                        <button class="text-sm text-gray-500 hover:text-red-600 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i>Sair
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
