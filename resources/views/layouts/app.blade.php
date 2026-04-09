<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VetEssence' }} - Sistema de Gestão Veterinária</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @stack('styles')
    @livewireStyles
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        
        <!-- Sidebar Mobile Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:leave="transition-opacity ease-linear duration-300"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="lg:pl-64 min-h-screen flex flex-col">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="ml-2 lg:ml-0">
                            <h1 class="text-lg font-semibold text-gray-800">{{ $header ?? 'Dashboard' }}</h1>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Menu -->
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 text-gray-500 hover:text-gray-700">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="hidden md:block">{{ Auth::user()->name ?? 'Usuário' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="userMenuOpen"
                                 @click.away="userMenuOpen = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Meu Perfil
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Configurações
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            @if(session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="mx-4 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t px-6 py-4">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} VetEssence - Sistema de Gestão Veterinária
                </p>
            </footer>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
