<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VetEssence' }} - Sistema de Gestão Veterinária</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @stack('styles')
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i> {{ Auth::user()->name ?? 'Usuário' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><i class="fas fa-user mr-2"></i> Meu Perfil</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Sair</button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light"><strong>Vet</strong>Essence</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        
                        @can('tutores')
                        <li class="nav-item">
                            <a href="{{ route('tutors.index') }}" class="nav-link {{ request()->routeIs('tutors.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Tutores</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('pets')
                        <li class="nav-item">
                            <a href="{{ route('pets.index') }}" class="nav-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-paw"></i>
                                <p>Pets</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('atendimentos')
                        <li class="nav-item">
                            <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Agendamentos</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('prontuarios')
                        <li class="nav-item">
                            <a href="{{ route('medical-records.index') }}" class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-medical"></i>
                                <p>Prontuários</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('vacinas')
                        <li class="nav-item">
                            <a href="{{ route('vaccinations.index') }}" class="nav-link {{ request()->routeIs('vaccinations.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-syringe"></i>
                                <p>Vacinas</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('exames')
                        <li class="nav-item">
                            <a href="{{ route('exams.index') }}" class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-flask"></i>
                                <p>Exames</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('cirurgias')
                        <li class="nav-item">
                            <a href="{{ route('surgeries.index') }}" class="nav-link {{ request()->routeIs('surgeries.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-md"></i>
                                <p>Cirurgias</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('financeiro')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-dollar-sign"></i>
                                <p>Financeiro <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Faturas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Relatórios</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        
                        @can('estoque')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>Estoque <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produtos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('stock.movements') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Movimentações</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        
                        @can('convenios')
                        <li class="nav-item">
                            <a href="{{ route('convenios.index') }}" class="nav-link {{ request()->routeIs('convenios.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-handshake"></i>
                                <p>Convênios</p>
                            </a>
                        </li>
                        @endcan
                        
                        @can('admin')
                        <li class="nav-header">ADMINISTRAÇÃO</li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Usuários</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>Perfis/Permissões</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list-ul"></i>
                                <p>Serviços</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Categorias</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ $title ?? 'Dashboard' }}</h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="icon fas fa-check"></i> {{ session('success') }}
                    </div>
                    @endif
                    @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                    </div>
                    @endif
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                <strong>VetEssence</strong> v1.0
            </div>
            <strong>&copy; {{ date('Y') }} Clínica Veterinária</strong>
        </footer>
    </div>
    @stack('modals')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
