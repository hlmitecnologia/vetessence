<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? branding('clinic_name', 'VetEssence') }} - {{ branding('clinic_name', 'VetEssence') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ branding_favicon_url() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>{!! branding_css_vars() !!}</style>
    <style>
        .btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: var(--brand-primary, #455e36) !important;
            border-color: var(--brand-primary, #455e36) !important;
        }
        a { color: var(--brand-primary, #455e36); }
        a:hover { color: color-mix(in srgb, var(--brand-primary, #455e36) 80%, black); }
        body { background: {{ branding('login_background', '#f9edc3') }} !important; }
    </style>
    @stack('styles')
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="/" class="d-flex align-items-center justify-content-center" style="gap:0.5rem">
                @php $logoUrl = branding_logo_url(); $hasLogo = $logoUrl && !str_contains($logoUrl, 'logo-default.png'); $showName = branding('show_clinic_name', '1') === '1'; @endphp
                @if($hasLogo)
                    <img src="{{ $logoUrl }}" width="60" alt="Logo">
                @else
                    <i class="fas fa-paw fa-2x"></i>
                @endif
                @if($showName)
                    <b>{{ branding('clinic_name', 'VetEssence') }}</b>
                @endif
            </a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Acesse sua conta</p>
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
