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
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    {{-- Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 text-xl font-bold" style="color: var(--brand-primary, #455e36)">
                    @php
                        $logoUrl = branding_logo_url();
                        $hasLogo = (bool) $logoUrl;
                        $showName = branding('show_clinic_name', '0') === '1';
                    @endphp
                    @if($hasLogo)
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 40px; max-width: 120px; object-fit: contain;">
                    @else
                        <i class="fas fa-paw" style="color: var(--brand-primary, #455e36); font-size: 1.75rem;"></i>
                    @endif
                    @if($showName)
                        <span class="text-gray-800 hidden sm:inline">{{ branding('clinic_name', 'VetEssence') }}</span>
                    @endif
                </a>
                @auth('tutor')
                <div class="flex items-center gap-4">
                    <a href="{{ route('portal.docs.index') }}"
                       class="touch-target-sm inline-flex items-center justify-center w-10 h-10 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                       title="Manual do Tutor">
                        <i class="fas fa-question-circle text-xl"></i>
                    </a>
                    <span class="text-base text-gray-500 hidden sm:block">{{ Auth::guard('tutor')->user()->name }}</span>
                    <form method="POST" action="{{ route('portal.logout') }}" class="m-0">
                        @csrf
                        <button class="touch-target inline-flex items-center gap-2 text-base text-gray-500 hover:text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline">Sair</span>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 portal-has-bottom-nav">
        @if (session('status'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl text-base">
                <i class="fas fa-check-circle text-xl"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-base">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-xl mt-0.5"></i>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 mt-8 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-400">
            <div class="flex items-center gap-4">
                <a href="{{ route('portal.docs.index') }}" class="hover:text-blue-600 transition">Manual do Tutor</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('portal.dashboard') }}" class="hover:text-blue-600 transition">Portal</a>
            </div>
            <p>&copy; {{ date('Y') }} {{ branding('clinic_name', 'VetEssence') }}. Todos os direitos reservados.</p>
        </div>
    </footer>

    {{-- Bottom Navigation (mobile) --}}
    @auth('tutor')
    @php
        $currentRoute = request()->route()->getName();
        $navItems = [
            ['route' => 'portal.dashboard', 'icon' => 'fa-house', 'label' => 'Início'],
            ['route' => 'portal.pets.index', 'icon' => 'fa-paw', 'label' => 'Pets'],
            ['route' => 'portal.appointments.index', 'icon' => 'fa-calendar-check', 'label' => 'Consultas'],
            ['route' => 'portal.invoices.index', 'icon' => 'fa-file-invoice', 'label' => 'Faturas'],
            ['route' => 'portal.docs.index', 'icon' => 'fa-book', 'label' => 'Manual'],
        ];
    @endphp
    <nav class="portal-bottom-nav">
        @foreach($navItems as $item)
            @php
                if ($item['route'] === 'portal.docs.index') {
                    $isActive = str_starts_with($currentRoute, 'portal.docs');
                } else {
                    $isActive = $currentRoute === $item['route'];
                }
            @endphp
            <a href="{{ route($item['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                <i class="fas {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
    <script>
        function initTinyMCE(container) {
            container = container || document;
            if (typeof tinymce === 'undefined') return;
            container.querySelectorAll('textarea.wysiwyg').forEach(function(ta) {
                if (ta.id && tinymce.get(ta.id)) return;
                if (!ta.id) ta.id = 'wysiwyg-' + ('xxxx' + Math.random().toString(36).substr(2, 9)).slice(-10);
                tinymce.init({
                    license_key: 'gpl',
                    target: ta,
                    height: 300,
                    menubar: false,
                    toolbar: 'undo redo | bold italic underline strikethrough | bullist numlist | removeformat',
                    plugins: 'lists',
                    setup: function(editor) {
                        editor.on('change keyup', function() {
                            tinymce.triggerSave();
                            ta.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                        editor.on('init', function() {
                            if (!ta || !ta.id) return;
                            var e = tinymce.get(ta.id);
                            if (!e || !e.editorContainer) return;
                            if (ta.classList.contains('is-invalid')) {
                                e.editorContainer.style.border = '2px solid #dc3545';
                                e.editorContainer.style.borderRadius = '0.25rem';
                            } else {
                                e.editorContainer.style.border = '';
                                e.editorContainer.style.borderRadius = '';
                            }
                        });
                    }
                });
            });
        }
        function destroyTinyMCE(container) {
            container = container || document;
            if (typeof tinymce === 'undefined') return;
            container.querySelectorAll('textarea.wysiwyg').forEach(function(ta) {
                var editorId = ta.id;
                if (editorId && tinymce.get(editorId)) tinymce.execCommand('mceRemoveEditor', true, editorId);
            });
        }
        document.addEventListener('DOMContentLoaded', function() { initTinyMCE(); });
    </script>
    @stack('scripts')
</body>
</html>
