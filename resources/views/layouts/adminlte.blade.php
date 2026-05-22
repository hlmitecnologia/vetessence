<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? branding('clinic_name', 'VetEssence') }} - {{ branding('clinic_name', 'VetEssence') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ branding_favicon_url() }}">
    <style>{!! branding_css_vars() !!}</style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .btn-action { padding: 0.25rem 0.5rem; }
        .btn-action i { margin: 0; }
        .ts-wrapper .ts-control { min-height: 38px; }
        .main-sidebar.sidebar-dark-primary,
        .main-sidebar .nav-sidebar > .nav-item > .nav-link.active {
            background: var(--sidebar-bg, #051c12) !important;
        }
        .btn-secondary-custom { background: var(--brand-secondary, #9aaa7e); border-color: var(--brand-secondary, #9aaa7e); color: #fff; }
        .btn-secondary-custom:hover { filter: brightness(0.9); color: #fff; }
        .text-accent { color: var(--brand-accent, #d6c38d); }
        .badge-accent { background: var(--brand-accent, #d6c38d); color: #1a1a1a; }
    </style>
    @stack('styles')
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
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="far fa-user"></i> {{ Auth::user()->name ?? 'Usuário' }}
                    </a>
                </li>
                @can('docs.view')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('docs.index') }}">
                        <i class="fas fa-book"></i> Manual
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        @php $sidebarBg = branding('sidebar_bg', '#051c12'); @endphp
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: {{ $sidebarBg }} !important;">
            @php
                $logoUrl = branding_logo_url();
                $hasLogo = $logoUrl && !str_contains($logoUrl, 'logo-default.png');
                $showName = branding('show_clinic_name', '1') === '1';
                $pos = branding('clinic_name_position', 'right');
                $logoW = branding('sidebar_logo_width', 40);
            @endphp
            <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
                @if($hasLogo)
                    <img src="{{ $logoUrl }}" width="{{ $logoW }}" alt="Logo" class="brand-image img-circle">
                @else
                    <i class="fas fa-paw brand-image"></i>
                @endif
                @if($showName)
                    <span class="brand-text font-weight-light ml-2">{{ branding('clinic_name', 'VetEssence') }}</span>
                @endif
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

                        <!-- CADASTROS -->
                        @if(Gate::allows('tutores') || Gate::allows('pets') || Gate::allows('convenios'))
                        <li class="nav-item has-treeview {{ request()->routeIs('tutors.*') || request()->routeIs('pets.*') || request()->routeIs('convenios.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-address-book"></i>
                                <p>Cadastros <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('tutores')
                                <li class="nav-item">
                                    <a href="{{ route('tutors.index') }}" class="nav-link {{ request()->routeIs('tutors.*') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon"></i>
                                        <p>Tutores</p>
                                    </a>
                                </li>
                                @endcan
                                @can('pets')
                                <li class="nav-item">
                                    <a href="{{ route('pets.index') }}" class="nav-link {{ request()->routeIs('pets.*') ? 'active' : '' }}">
                                        <i class="fas fa-paw nav-icon"></i>
                                        <p>Pets</p>
                                    </a>
                                </li>
                                @endcan
                                @can('convenios')
                                <li class="nav-item">
                                    <a href="{{ route('convenios.index') }}" class="nav-link {{ request()->routeIs('convenios.*') ? 'active' : '' }}">
                                        <i class="fas fa-handshake nav-icon"></i>
                                        <p>Convênios</p>
                                    </a>
                                </li>
                                @endcan
                                @can('prontuarios')
                                <li class="nav-item">
                                    <a href="{{ route('zoonotic-diseases.index') }}" class="nav-link {{ request()->routeIs('zoonotic-diseases.*') ? 'active' : '' }}">
                                        <i class="fas fa-biohazard nav-icon"></i>
                                        <p>Zoonoses</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- ATENDIMENTO -->
                        @if(Gate::allows('atendimentos') || Gate::allows('prontuarios') || Gate::allows('vacinas') || Gate::allows('exames') || Gate::allows('cirurgias') || Gate::allows('prescricoes') || Gate::allows('hospitalizacao') || Gate::allows('laboratorio') || Gate::allows('imagem') || Gate::allows('referral') || Gate::allows('lembrete-vacinas') || Gate::allows('protocolo-vacinas') || Gate::allows('parasitario') || Gate::allows('certificado-sanitario') || Gate::allows('modelo-laudo') || Gate::allows('interacao-medicamentosa') || Gate::allows('hospedagem') || Gate::allows('agendamento-online') || Gate::allows('teleconsulta') || Gate::allows('triage') || Gate::allows('pre-anesthetic') || Gate::allows('diet-plans') || Gate::allows('convenio-claims'))
                        <li class="nav-item has-treeview {{ request()->routeIs('appointments.*') || request()->routeIs('medical-records.*') || request()->routeIs('vaccinations.*') || request()->routeIs('vaccination-reminders.*') || request()->routeIs('vaccine-protocols.*') || request()->routeIs('parasite-controls.*') || request()->routeIs('health-certificates.*') || request()->routeIs('clinical-report-templates.*') || request()->routeIs('drug-interactions.*') || request()->routeIs('boardings.*') || request()->routeIs('online-bookings.*') || request()->routeIs('teleconsultations.*') || request()->routeIs('exams.*') || request()->routeIs('surgeries.*') || request()->routeIs('prescriptions.*') || request()->routeIs('hospitalizations.*') || request()->routeIs('hospitalization-daily-records.*') || request()->routeIs('anesthesia-monitorings.*') || request()->routeIs('dental-charts.*') || request()->routeIs('weight-records.*') || request()->routeIs('treatment-plans.*') || request()->routeIs('consent-forms.*') || request()->routeIs('laboratory-orders.*') || request()->routeIs('imaging-exams.*') || request()->routeIs('referrals.*') || request()->routeIs('triage.*') || request()->routeIs('pre-anesthetic-evaluations.*') || request()->routeIs('diet-plans.*') || request()->routeIs('convenio-claims.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-stethoscope"></i>
                                <p>Atendimento <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                        @can('atendimentos')
                        <li class="nav-item">
                            <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt nav-icon"></i>
                                <p>Agendamentos</p>
                            </a>
                        </li>
                        @endcan
                        @can('triage')
                        <li class="nav-item">
                            <a href="{{ route('triage.index') }}" class="nav-link {{ request()->routeIs('triage.*') ? 'active' : '' }}">
                                <i class="fas fa-sort-amount-up nav-icon"></i>
                                <p>Triagem</p>
                            </a>
                        </li>
                        @endcan
                        @can('agendamento-online')
                        <li class="nav-item">
                            <a href="{{ route('online-bookings.index') }}" class="nav-link {{ request()->routeIs('online-bookings.*') ? 'active' : '' }}">
                                <i class="fas fa-globe nav-icon"></i>
                                <p>Agendamentos Online</p>
                            </a>
                        </li>
                        @endcan
                                @can('prontuarios')
                                <li class="nav-item">
                                    <a href="{{ route('medical-records.index') }}" class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-medical nav-icon"></i>
                                        <p>Prontuários</p>
                                    </a>
                                </li>
                                @endcan
                        @can('prontuarios')
                        <li class="nav-item">
                            <a href="{{ route('treatment-plans.index') }}" class="nav-link {{ request()->routeIs('treatment-plans.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Planos de Tratamento</p>
                            </a>
                        </li>
                        @endcan
                        @can('modelo-laudo')
                        <li class="nav-item">
                            <a href="{{ route('clinical-report-templates.index') }}" class="nav-link {{ request()->routeIs('clinical-report-templates.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Modelos de Laudo</p>
                            </a>
                        </li>
                        @endcan
@can('vacinas')
                        <li class="nav-item">
                            <a href="{{ route('vaccinations.index') }}" class="nav-link {{ request()->routeIs('vaccinations.*') ? 'active' : '' }}">
                                <i class="fas fa-syringe nav-icon"></i>
                                <p>Vacinas</p>
                            </a>
                        </li>
                        @endcan
                        @can('hospedagem')
                        <li class="nav-item">
                            <a href="{{ route('boardings.index') }}" class="nav-link {{ request()->routeIs('boardings.*') ? 'active' : '' }}">
                                <i class="fas fa-dog nav-icon"></i>
                                <p>Hospedagem & Banho/Tosa</p>
                            </a>
                        </li>
                        @endcan
                                @can('protocolo-vacinas')
                                <li class="nav-item">
                                    <a href="{{ route('vaccine-protocols.index') }}" class="nav-link {{ request()->routeIs('vaccine-protocols.*') ? 'active' : '' }}">
                                        <i class="fas fa-clipboard-check nav-icon"></i>
                                        <p>Protocolos de Vacinação</p>
                                    </a>
                                </li>
                                @endcan
                                @can('parasitario')
                                <li class="nav-item">
                                    <a href="{{ route('parasite-controls.index') }}" class="nav-link {{ request()->routeIs('parasite-controls.*') ? 'active' : '' }}">
                                        <i class="fas fa-bug nav-icon"></i>
                                        <p>Controle Parasitário</p>
                                    </a>
                                </li>
                                @endcan
                        @can('certificado-sanitario')
                        <li class="nav-item">
                            <a href="{{ route('health-certificates.index') }}" class="nav-link {{ request()->routeIs('health-certificates.*') ? 'active' : '' }}">
                                <i class="fas fa-file-contract nav-icon"></i>
                                <p>Certificados Sanitários</p>
                            </a>
                        </li>
                        @endcan
                        @can('lembrete-vacinas')
                        <li class="nav-item">
                            <a href="{{ route('vaccination-reminders.index') }}" class="nav-link {{ request()->routeIs('vaccination-reminders.*') ? 'active' : '' }}">
                                <i class="fas fa-bell nav-icon"></i>
                                <p>Lembretes de Vacinas</p>
                            </a>
                        </li>
                        @endcan
                                @can('hospitalizacao')
                                <li class="nav-item">
                                    <a href="{{ route('hospitalizations.index') }}" class="nav-link {{ request()->routeIs('hospitalizations.*') ? 'active' : '' }}">
                                        <i class="fas fa-procedures nav-icon"></i>
                                        <p>Internações</p>
                                    </a>
                                </li>
                                @endcan
                                @can('cirurgias')
                                <li class="nav-item">
                                    <a href="{{ route('anesthesia-monitorings.index') }}" class="nav-link {{ request()->routeIs('anesthesia-monitorings.*') ? 'active' : '' }}">
                                        <i class="fas fa-heartbeat nav-icon"></i>
                                        <p>Anestesia</p>
                                    </a>
                                </li>
                                @can('pre-anesthetic')
                                <li class="nav-item">
                                    <a href="{{ route('pre-anesthetic-evaluations.index') }}" class="nav-link {{ request()->routeIs('pre-anesthetic-evaluations.*') ? 'active' : '' }}">
                                        <i class="fas fa-stethoscope nav-icon"></i>
                                        <p>Aval. Pré-Anestésica</p>
                                    </a>
                                </li>
                                @endcan
                                <li class="nav-item">
                                    <a href="{{ route('surgeries.index') }}" class="nav-link {{ request()->routeIs('surgeries.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-md nav-icon"></i>
                                        <p>Cirurgias</p>
                                    </a>
                                </li>
                                @endcan
                                @can('laboratorio')
                                <li class="nav-item">
                                    <a href="{{ route('laboratory-orders.index') }}" class="nav-link {{ request()->routeIs('laboratory-orders.*') ? 'active' : '' }}">
                                        <i class="fas fa-microscope nav-icon"></i>
                                        <p>Laboratório</p>
                                    </a>
                                </li>
                                @endcan
                                @can('imagem')
                                <li class="nav-item">
                                    <a href="{{ route('imaging-exams.index') }}" class="nav-link {{ request()->routeIs('imaging-exams.*') ? 'active' : '' }}">
                                        <i class="fas fa-x-ray nav-icon"></i>
                                        <p>Imagem</p>
                                    </a>
                                </li>
                                @endcan
                                @can('exames')
                                <li class="nav-item">
                                    <a href="{{ route('exams.index') }}" class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                                        <i class="fas fa-flask nav-icon"></i>
                                        <p>Exames</p>
                                    </a>
                                </li>
                                @endcan
                        @can('prescricoes')
                        <li class="nav-item">
                            <a href="{{ route('prescriptions.index') }}" class="nav-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
                                <i class="fas fa-prescription nav-icon"></i>
                                <p>Prescrições</p>
                            </a>
                        </li>
                        @endcan
                        @can('interacao-medicamentosa')
                        <li class="nav-item">
                            <a href="{{ route('drug-interactions.index') }}" class="nav-link {{ request()->routeIs('drug-interactions.*') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle nav-icon"></i>
                                <p>Interações Medicamentosas</p>
                            </a>
                        </li>
                        @endcan
                                @can('prontuarios')
                                <li class="nav-item">
                                    <a href="{{ route('dental-charts.index') }}" class="nav-link {{ request()->routeIs('dental-charts.*') ? 'active' : '' }}">
                                        <i class="fas fa-tooth nav-icon"></i>
                                        <p>Odontologia</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('consent-forms.index') }}" class="nav-link {{ request()->routeIs('consent-forms.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-signature nav-icon"></i>
                                        <p>Termos de Consentimento</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('weight-records.index') }}" class="nav-link {{ request()->routeIs('weight-records.*') ? 'active' : '' }}">
                                        <i class="fas fa-weight nav-icon"></i>
                                        <p>Controle de Peso</p>
                                    </a>
                                </li>
                                @can('diet-plans')
                                <li class="nav-item">
                                    <a href="{{ route('diet-plans.index') }}" class="nav-link {{ request()->routeIs('diet-plans.*') ? 'active' : '' }}">
                                        <i class="fas fa-utensils nav-icon"></i>
                                        <p>Planos Alimentares</p>
                                    </a>
                                </li>
                                @endcan
                                @endcan
                        @can('referral')
                        <li class="nav-item">
                            <a href="{{ route('referrals.index') }}" class="nav-link {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
                                <i class="fas fa-share-alt nav-icon"></i>
                                <p>Encaminhamentos</p>
                            </a>
                        </li>
                        @endcan
                        @can('convenio-claims')
                        <li class="nav-item">
                            <a href="{{ route('convenio-claims.index') }}" class="nav-link {{ request()->routeIs('convenio-claims.*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice nav-icon"></i>
                                <p>Reembolsos Convênios</p>
                            </a>
                        </li>
                        @endcan
                        @can('teleconsulta')
                        <li class="nav-item">
                            <a href="{{ route('teleconsultations.index') }}" class="nav-link {{ request()->routeIs('teleconsultations.*') ? 'active' : '' }}">
                                <i class="fas fa-video nav-icon"></i>
                                <p>Teleconsultas</p>
                            </a>
                        </li>
                        @endcan
                                @can('prontuarios')
                                <li class="nav-item">
                                    <a href="{{ route('zoonotic-diseases.index') }}" class="nav-link {{ request()->routeIs('zoonotic-diseases.*') ? 'active' : '' }}">
                                        <i class="fas fa-biohazard nav-icon"></i>
                                        <p>Zoonoses</p>
                                    </a>
                                </li>
                                @endcan
                                @can('bank-reconciliation.view')
                                <li class="nav-item">
                                    <a href="{{ route('bank-reconciliation.index') }}" class="nav-link {{ request()->routeIs('bank-reconciliation.*') || request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                                        <i class="fas fa-handshake nav-icon"></i>
                                        <p>Conciliação Bancária</p>
                                    </a>
                                </li>
                                @endcan
                                @can('commissions.view')
                                <li class="nav-item">
                                    <a href="{{ route('commissions.index') }}" class="nav-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}">
                                        <i class="fas fa-percentage nav-icon"></i>
                                        <p>Comissões</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- COMUNICAÇÃO -->
                        @if(Gate::allows('notificacoes') || Gate::allows('nota-interna') || Gate::allows('chat'))
                        <li class="nav-item has-treeview {{ request()->routeIs('notification-logs.*') || request()->routeIs('staff-notes.*') || request()->routeIs('chat.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Comunicação <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @can('chat')
                                <li class="nav-item">
                                    <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                                        <i class="fas fa-comments nav-icon"></i>
                                        <p>Chat Interno <span id="chat-unread-badge" class="badge badge-info right" style="display:none;">0</span></p>
                                    </a>
                                </li>
                                @endcan
                                @can('nota-interna')
                                <li class="nav-item">
                                    <a href="{{ route('staff-notes.index') }}" class="nav-link {{ request()->routeIs('staff-notes.*') ? 'active' : '' }}">
                                        <i class="fas fa-sticky-note nav-icon"></i>
                                        <p>Notas Internas</p>
                                    </a>
                                </li>
                                @endcan
                                @can('notificacoes')
                                <li class="nav-item">
                                    <a href="{{ route('notification-logs.index') }}" class="nav-link {{ request()->routeIs('notification-logs.*') ? 'active' : '' }}">
                                        <i class="fas fa-history nav-icon"></i>
                                        <p>Logs de Notificação</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- AGENDA -->
                        @can('agenda-equipe')
                        <li class="nav-item has-treeview {{ request()->routeIs('staff-schedules.*') || request()->routeIs('staff-time-off.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Agenda <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('staff-schedules.index') }}" class="nav-link {{ request()->routeIs('staff-schedules.index') || request()->routeIs('staff-schedules.create') || request()->routeIs('staff-schedules.edit') ? 'active' : '' }}">
                                        <i class="fas fa-clock nav-icon"></i>
                                        <p>Escalas</p>
                                    </a>
                                </li>
                                @can('schedules-on-call.view')
                                <li class="nav-item">
                                    <a href="{{ route('staff-schedules.on-call-calendar') }}" class="nav-link {{ request()->routeIs('staff-schedules.on-call-calendar') ? 'active' : '' }}">
                                        <i class="fas fa-phone-alt nav-icon"></i>
                                        <p>Plantão</p>
                                    </a>
                                </li>
                                @endcan
                                <li class="nav-item">
                                    <a href="{{ route('staff-schedules.time-off') }}" class="nav-link {{ request()->routeIs('staff-schedules.time-off') ? 'active' : '' }}">
                                        <i class="fas fa-umbrella-beach nav-icon"></i>
                                        <p>Folgas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan

                        <!-- FINANCEIRO -->
                        @if(Gate::allows('financeiro') || Gate::allows('gateway-pagamento') || Gate::allows('nfse.view') || Gate::allows('bank-reconciliation.view'))
                        <li class="nav-item has-treeview {{ request()->routeIs('invoices.*') || request()->routeIs('reports.*') || request()->routeIs('payment-gateways.*') || request()->routeIs('nfse.*') || request()->routeIs('bank-reconciliation.*') || request()->routeIs('bank-accounts.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-dollar-sign"></i>
                                <p>Financeiro <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                        <p>Faturas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                                        <i class="fas fa-chart-bar nav-icon"></i>
                                        <p>Relatórios</p>
                                    </a>
                                </li>
                                @can('gateway-pagamento')
                                <li class="nav-item">
                                    <a href="{{ route('payment-gateways.index') }}" class="nav-link {{ request()->routeIs('payment-gateways.*') ? 'active' : '' }}">
                                        <i class="fas fa-credit-card nav-icon"></i>
                                        <p>Gateways de Pagamento</p>
                                    </a>
                                </li>
                                @endcan
                                @can('nfse.view')
                                <li class="nav-item">
                                    <a href="{{ route('nfse.index') }}" class="nav-link {{ request()->routeIs('nfse.index') || request()->routeIs('nfse.show') ? 'active' : '' }}">
                                        <i class="fas fa-file-invoice nav-icon"></i>
                                        <p>NFSe</p>
                                    </a>
                                </li>
                                @endcan
                                @can('nfse-config.edit')
                                <li class="nav-item">
                                    <a href="{{ route('nfse.config') }}" class="nav-link {{ request()->routeIs('nfse.config') ? 'active' : '' }}">
                                        <i class="fas fa-cog nav-icon"></i>
                                        <p>Config. NFSe</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- ESTOQUE -->
                        @can('estoque')
                        <li class="nav-item has-treeview {{ request()->routeIs('products.*') || request()->routeIs('stock.*') || request()->routeIs('suppliers.*') || request()->routeIs('controlled-substances.*') || request()->routeIs('lab-equipment-integrations.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-boxes"></i>
                                <p>Estoque <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                        <i class="fas fa-box nav-icon"></i>
                                        <p>Produtos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('stock.movements') }}" class="nav-link {{ request()->routeIs('stock.movements') ? 'active' : '' }}">
                                        <i class="fas fa-exchange-alt nav-icon"></i>
                                        <p>Movimentações</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('controlled-substances.index') }}" class="nav-link {{ request()->routeIs('controlled-substances.*') ? 'active' : '' }}">
                                        <i class="fas fa-prescription-bottle nav-icon"></i>
                                        <p>Substâncias Controladas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                                        <i class="fas fa-truck nav-icon"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                                @can('integracao-equipamentos')
                                <li class="nav-item">
                                    <a href="{{ route('lab-equipment-integrations.index') }}" class="nav-link {{ request()->routeIs('lab-equipment-integrations.*') ? 'active' : '' }}">
                                        <i class="fas fa-microscope nav-icon"></i>
                                        <p>Equip. Laboratório</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcan

                        <!-- ADMINISTRAÇÃO -->
                        @can('admin')
                        <li class="nav-header"><i class="fas fa-cog"></i> ADMINISTRAÇÃO</li>
                        <li class="nav-item has-treeview {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('services.*') || request()->routeIs('categories.*') || request()->routeIs('consent-templates.*') || request()->routeIs('communication-templates.*') || request()->routeIs('communication-queues.*') || request()->routeIs('branches.*') || request()->routeIs('departments.*') || request()->routeIs('positions.*') || request()->routeIs('employees.*') || request()->routeIs('configuracoes.branding.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>Configurações <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-cog nav-icon"></i>
                                        <p>Usuários</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield nav-icon"></i>
                                        <p>Perfis/Permissões</p>
                                    </a>
                                </li>
                                @can('employees.view')
                                <li class="nav-item">
                                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                        <i class="fas fa-id-badge nav-icon"></i>
                                        <p>Funcionários</p>
                                    </a>
                                </li>
                                @endcan
                                @can('departments.view')
                                <li class="nav-item">
                                    <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                        <i class="fas fa-building nav-icon"></i>
                                        <p>Departamentos</p>
                                    </a>
                                </li>
                                @endcan
                                @can('positions.view')
                                <li class="nav-item">
                                    <a href="{{ route('positions.index') }}" class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                                        <i class="fas fa-briefcase nav-icon"></i>
                                        <p>Cargos</p>
                                    </a>
                                </li>
                                @endcan
                                <li class="nav-item">
                                    <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                                        <i class="fas fa-list-ul nav-icon"></i>
                                        <p>Serviços</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                        <i class="fas fa-tags nav-icon"></i>
                                        <p>Categorias</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('consent-templates.index') }}" class="nav-link {{ request()->routeIs('consent-templates.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-contract nav-icon"></i>
                                        <p>Modelos de Termos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('communication-templates.index') }}" class="nav-link {{ request()->routeIs('communication-templates.*') ? 'active' : '' }}">
                                        <i class="fas fa-envelope-open-text nav-icon"></i>
                                        <p>Modelos de Comunicação</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('communication-queues.index') }}" class="nav-link {{ request()->routeIs('communication-queues.*') ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Fila de Comunicação</p>
                                    </a>
                                </li>
                                @can('unidades')
                                <li class="nav-item">
                                    <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                                        <i class="fas fa-building nav-icon"></i>
                                        <p>Unidades</p>
                                    </a>
                                </li>
                                @endcan
                                @can('system-update')
                                <li class="nav-item">
                                    <a href="{{ route('system-update.index') }}" class="nav-link {{ request()->routeIs('system-update.*') ? 'active' : '' }}">
                                        <i class="fas fa-sync-alt nav-icon"></i>
                                        <p>Atualizar Sistema</p>
                                    </a>
                                </li>
                                @endcan
                                @can('configuracoes.branding')
                                <li class="nav-item">
                                    <a href="{{ route('configuracoes.branding.index') }}" class="nav-link {{ request()->routeIs('configuracoes.branding.*') ? 'active' : '' }}">
                                        <i class="fas fa-paint-brush nav-icon"></i>
                                        <p>Personalização</p>
                                    </a>
                                </li>
                                @endcan
                                @can('docs.view')
                                <li class="nav-item">
                                    <a href="{{ route('docs.index') }}" class="nav-link {{ request()->routeIs('docs.*') ? 'active' : '' }}">
                                        <i class="fas fa-book nav-icon"></i>
                                        <p>Documentação</p>
                                    </a>
                                </li>
                                @endcan
                            </ul>
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
                <strong>{{ branding('clinic_name', 'VetEssence') }}</strong> v1.0
            </div>
            <strong>&copy; {{ date('Y') }} Clínica Veterinária</strong>
        </footer>
    </div>
    @stack('modals')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Legacy n() function for views that still call it
        window.n = function() { return true; };

        // Global SweetAlert2 confirmation for all DELETE form submissions
        document.addEventListener('submit', function(e) {
            var form = e.target;
            var methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput || methodInput.value !== 'DELETE') return;
            if (form.dataset.swalConfirmed) return;

            e.preventDefault();
            var deleteBtn = form.querySelector('.btn-danger');
            var message = deleteBtn && deleteBtn.dataset.confirm
                ? deleteBtn.dataset.confirm
                : 'Tem certeza que deseja excluir este registro?';

            Swal.fire({
                title: 'Confirmação',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.dataset.swalConfirmed = '1';
                    form.submit();
                }
            });
        });

        function initTomSelects(container) {
            container = container || document;
            container.querySelectorAll('select.tom-select:not([data-ts-ready])').forEach(function(el) {
                var wrapper = el.closest('.tom-select-wrapper');
                var initialValue = wrapper ? wrapper.dataset.value : '';
                if (initialValue) el.value = initialValue;

                el.dataset.tsReady = '1';
                new TomSelect(el, {
                    maxOptions: 200,
                    onChange: function(value) {
                        var wireModel = el.dataset.wire;
                        if (wireModel && window.Livewire) {
                            var componentEl = el.closest('[wire\\:id]');
                            if (componentEl) {
                                var component = Livewire.find(componentEl.getAttribute('wire:id'));
                                if (component) component.set(wireModel, value);
                            }
                        }
                    }
                });
            });
        }

        function destroyTomSelects(container) {
            container = container || document;
            container.querySelectorAll('.ts-wrapper').forEach(function(wrapper) {
                if (wrapper.tomselect) wrapper.tomselect.destroy();
            });
            container.querySelectorAll('select.tom-select[data-ts-ready]').forEach(function(el) {
                delete el.dataset.tsReady;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initTomSelects();

            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable === 'function') {
                jQuery('table.table-bordered').each(function() {
                    if (!jQuery(this).find('thead').length) return;
                    jQuery(this).DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                        "language": {
                            "sEmptyTable": "Nenhum registro encontrado",
                            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                            "sLengthMenu": "_MENU_ registros por página",
                            "sLoadingRecords": "Carregando...",
                            "sProcessing": "Processando...",
                            "sZeroRecords": "Nenhum registro encontrado",
                            "oPaginate": {
                                "sNext": "Próximo",
                                "sPrevious": "Anterior",
                                "sFirst": "Primeiro",
                                "sLast": "Último"
                            },
                            "oAria": {
                                "sSortAscending": ": Ordenar colunas de forma ascendente",
                                "sSortDescending": ": Ordenar colunas de forma descendente"
                            }
                        }
                    });
                });
            }
        });

        // Livewire integration
        document.addEventListener('livewire:initialized', function() {
            Livewire.hook('morph.updated', function(params) {
                setTimeout(function() {
                    destroyTomSelects(params.el);
                    initTomSelects(params.el);
                }, 0);
            });
        });

        // Bootstrap modal integration
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('shown.bs.modal', '.modal', function() {
                initTomSelects(this);
            });
        }

        @stack('scripts')
    </script>
</body>
</html>
