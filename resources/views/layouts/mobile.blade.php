<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', branding('clinic_name', 'VetEssence') . ' Mobile') — {{ branding('clinic_name', 'VetEssence') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ branding_favicon_url() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <style>
        body { background: #f4f6f9; font-size: 16px; padding-bottom: 70px; }
        .mobile-header { background: #343a40; color: #fff; padding: 12px 16px; position: sticky; top: 0; z-index: 100; }
        .mobile-header h4 { margin: 0; font-size: 18px; }
        .mobile-header a { color: #fff; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 12px; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; border-radius: 12px 12px 0 0 !important; font-weight: bold; }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #dee2e6; display: flex; z-index: 100; }
        .bottom-nav a { flex: 1; text-align: center; padding: 10px 4px; color: #6c757d; font-size: 11px; }
        .bottom-nav a.active { color: #007bff; }
        .bottom-nav a i { display: block; font-size: 20px; margin-bottom: 2px; }
        .btn-block { border-radius: 8px; padding: 12px; font-size: 16px; }
        .list-group-item { border-radius: 8px !important; margin-bottom: 4px; border: 1px solid #eee; }
        .badge { font-size: 12px; padding: 4px 8px; }
        @media (min-width: 768px) { body { padding-bottom: 0; } .bottom-nav { display: none; } }
    </style>
</head>
<body>
    <div class="mobile-header d-flex align-items-center justify-content-between">
        <a href="{{ url('/m') }}" class="text-white"><i class="fas fa-paw"></i> {{ branding('clinic_name', 'VetEssence') }}</a>
        <div>
            <a href="{{ route('logout') }}" class="text-white ml-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>

    <div class="container-fluid p-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @yield('content')
    </div>

    <nav class="bottom-nav">
        <a href="{{ url('/m') }}" class="{{ request()->is('m') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Início
        </a>
        <a href="{{ url('/m/triage') }}" class="{{ request()->is('m/triage') ? 'active' : '' }}">
            <i class="fas fa-ambulance"></i> Triagem
        </a>
        <a href="{{ url('/m/prescriptions') }}" class="{{ request()->is('m/prescriptions') ? 'active' : '' }}">
            <i class="fas fa-prescription-bottle"></i> Receitas
        </a>
        <a href="{{ url('/m/records') }}" class="{{ request()->is('m/records') ? 'active' : '' }}">
            <i class="fas fa-notes-medical"></i> Prontuários
        </a>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
