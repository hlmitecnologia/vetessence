<aside x-show="sidebarOpen" 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-16" style="background: {{ branding('primary_color', '#4f46e5') }};">
        <div class="text-center">
            <i class="fas fa-paw text-2xl text-white"></i>
            <span class="ml-2 text-xl font-bold text-white">{{ branding('clinic_name', 'VetEssence') }}</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        
        <!-- Dashboard - Todos -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
            <i class="fas fa-home w-6"></i>
            <span>Dashboard</span>
        </a>

        @role('admin|recepcionista')
        <!-- Agenda -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span>Agenda</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('appointments.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-list w-5 mr-2"></i> Listar
                </a>
                <a href="{{ route('online-bookings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-globe w-5 mr-2"></i> Agendamentos Online
                </a>
                <a href="{{ route('appointments.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-plus w-5 mr-2"></i> Novo
                </a>
            </div>
        </div>
        @endrole

        @role('admin|veterinario|recepcionista')
        <!-- Cadastros -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-users w-6"></i>
                    <span>Cadastros</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                @role('admin|recepcionista')
                <a href="{{ route('tutors.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-user-tie w-5 mr-2"></i> Tutores
                </a>
                @endrole
                <a href="{{ route('pets.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-dog w-5 mr-2"></i> Pets
                </a>
                <a href="{{ route('convenios.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-heartbeat w-5 mr-2"></i> Convênios
                </a>
                @role('admin|veterinario')
                <a href="{{ route('zoonotic-diseases.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-biohazard w-5 mr-2"></i> Zoonoses
                </a>
                @endrole
            </div>
        </div>
        @endrole

        @role('admin|veterinario')
        <!-- Clínico -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-file-medical w-6"></i>
                    <span>Clínico</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('medical-records.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-folder-open w-5 mr-2"></i> Prontuários
                </a>
                <a href="{{ route('treatment-plans.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-clipboard-list w-5 mr-2"></i> Planos de Tratamento
                </a>
                    <a href="{{ route('vaccinations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-syringe w-5 mr-2"></i> Vacinas
                    </a>
                    <a href="{{ route('vaccinations.forecast') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-calendar-alt w-5 mr-2"></i> Previsão de Vacinas
                    </a>
                <a href="{{ route('boardings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-dog w-5 mr-2"></i> Hospedagem & Banho/Tosa
                </a>
                <a href="{{ route('vaccination-reminders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-bell w-5 mr-2"></i> Lembretes de Vacinas
                </a>
                <a href="{{ route('health-certificates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-file-contract w-5 mr-2"></i> Certificados Sanitários
                </a>
                <a href="{{ route('vaccine-protocols.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-clipboard-check w-5 mr-2"></i> Protocolos de Vacinação
                </a>
                <a href="{{ route('parasite-controls.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-bug w-5 mr-2"></i> Controle Parasitário
                </a>
                <a href="{{ route('clinical-report-templates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-file-alt w-5 mr-2"></i> Modelos de Laudo
                </a>
                <a href="{{ route('hospitalizations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-procedures w-5 mr-2"></i> Internações
                </a>
                <a href="{{ route('surgeries.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-user-md w-5 mr-2"></i> Cirurgias
                </a>
                <a href="{{ route('anesthesia-monitorings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-heartbeat w-5 mr-2"></i> Anestesia
                </a>
                <a href="{{ route('laboratory-orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-microscope w-5 mr-2"></i> Laboratório
                </a>
                <a href="{{ route('imaging-exams.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-x-ray w-5 mr-2"></i> Imagem
                </a>
                <a href="{{ route('exams.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-flask w-5 mr-2"></i> Exames
                </a>
                <a href="{{ route('prescriptions.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-prescription w-5 mr-2"></i> Prescrições
                </a>
                <a href="{{ route('dental-charts.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-tooth w-5 mr-2"></i> Odontologia
                </a>
                <a href="{{ route('consent-forms.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-file-signature w-5 mr-2"></i> Termos de Consentimento
                </a>
                <a href="{{ route('weight-records.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-weight w-5 mr-2"></i> Controle de Peso
                </a>
                <a href="{{ route('referrals.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-share-alt w-5 mr-2"></i> Encaminhamentos
                </a>
                <a href="{{ route('teleconsultations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-video w-5 mr-2"></i> Teleconsultas
                </a>
                <a href="{{ route('zoonotic-diseases.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-biohazard w-5 mr-2"></i> Zoonoses
                </a>
                <a href="{{ route('drug-interactions.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-exclamation-triangle w-5 mr-2"></i> Interações Medicamentosas
                </a>
                    @can('drug-formulary.view')
                    <a href="{{ route('drug-formulary.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-pills w-5 mr-2"></i> Formulário de Fármacos
                    </a>
                    @endcan
                <a href="{{ route('zoonotic-diseases.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-biohazard w-5 mr-2"></i> Zoonoses
                </a>
                @can('emergency-protocols.view')
                <a href="{{ route('emergency-protocols.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-ambulance w-5 mr-2"></i> Protocolos de Emergência
                </a>
                @endcan
            </div>
        </div>
        @endrole

        @role('admin|financeiro')
        <!-- Financeiro -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-dollar-sign w-6"></i>
                    <span>Financeiro</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('invoices.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-file-invoice-dollar w-5 mr-2"></i> Faturas
                </a>
                <a href="{{ route('reports.financial') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-chart-bar w-5 mr-2"></i> Relatórios
                </a>
                <a href="{{ route('payment-gateways.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-credit-card w-5 mr-2"></i> Gateways Pagamento
                </a>
            </div>
        </div>
        @endrole

        @role('admin|veterinario|recepcionista')
        <!-- Notas Internas -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-sticky-note w-6"></i>
                    <span>Comunicação</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('staff-notes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-sticky-note w-5 mr-2"></i> Notas Internas
                    </a>
                    @can('chat.view')
                    <a href="{{ route('chat.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-comments w-5 mr-2"></i> Chat Interno
                    </a>
                    @endcan
            </div>
        </div>
        @endrole

        @role('admin|estoque')
        <!-- Estoque -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-boxes w-6"></i>
                    <span>Estoque</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-pills w-5 mr-2"></i> Produtos
                </a>
                <a href="{{ route('services.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-hand-holding-medical w-5 mr-2"></i> Serviços
                </a>
                    <a href="{{ route('stock.movements') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-exchange-alt w-5 mr-2"></i> Movimentações
                    </a>
                    @can('stock.transfer')
                    <a href="{{ route('stock.transfer-form') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-arrow-right w-5 mr-2"></i> Transferir
                    </a>
                    @endcan
                <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-truck w-5 mr-2"></i> Fornecedores
                </a>
                @can('purchase-orders.view')
                <a href="{{ route('purchase-orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-shopping-cart w-5 mr-2"></i> Pedidos de Compra
                </a>
                @endcan
                <a href="{{ route('controlled-substances.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-prescription-bottle w-5 mr-2"></i> Subst. Controladas
                </a>
                    <a href="{{ route('lab-equipment-integrations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-microscope w-5 mr-2"></i> Equip. Laboratório
                    </a>
                    @can('products.view')
                    <a href="{{ route('scanner.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-camera w-5 mr-2"></i> Scanner
                    </a>
                    @endcan
            </div>
        </div>
        @endrole

            @can('corporate-dashboard.view')
            <a href="{{ route('corporate-dashboard.index') }}" class="flex items-center px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <i class="fas fa-chart-pie w-6"></i>
                <span>Dashboard Corporativo</span>
            </a>
            @endcan

            @can('admin')
        <!-- Configurações -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-cog w-6"></i>
                    <span>Configurações</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-tags w-5 mr-2"></i> Categorias
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-user-cog w-5 mr-2"></i> Usuários
                </a>
                <a href="{{ route('roles.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-user-shield w-5 mr-2"></i> Perfis
                </a>
                <a href="{{ route('consent-templates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-file-contract w-5 mr-2"></i> Modelos de Termos
                </a>
                <a href="{{ route('communication-templates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-envelope-open-text w-5 mr-2"></i> Modelos de Comunicação
                </a>
                <a href="{{ route('communication-queues.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-tasks w-5 mr-2"></i> Fila de Comunicação
                </a>
                <a href="{{ route('branches.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-building w-5 mr-2"></i> Unidades
                </a>
                @can('system-update')
                <a href="{{ route('system-update.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-sync-alt w-5 mr-2"></i> Atualizar Sistema
                </a>
                @endcan
                @can('branding')
                <a href="{{ route('branding.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-paint-brush w-5 mr-2"></i> Identidade Visual
                </a>
                @endcan
                @can('docs.view')
                <a href="{{ route('docs.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-book w-5 mr-2"></i> Documentação
                </a>
                @endcan
            </div>
        </div>
        @endcan

    </nav>
</aside>
