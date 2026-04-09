<aside x-show="sidebarOpen" 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-indigo-700">
        <div class="text-center">
            <i class="fas fa-paw text-2xl text-white"></i>
            <span class="ml-2 text-xl font-bold text-white">VetEssence</span>
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
            </div>
        </div>
        @endrole

        @role('admin|veterinario')
        <!-- Prontuário -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-200 hover:bg-gray-800 rounded-lg transition">
                <div class="flex items-center">
                    <i class="fas fa-file-medical w-6"></i>
                    <span>Prontuário</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" class="pl-4 mt-1 space-y-1">
                <a href="{{ route('medical-records.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-folder-open w-5 mr-2"></i> Registros
                </a>
                <a href="{{ route('vaccinations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-syringe w-5 mr-2"></i> Vacinas
                </a>
                <a href="{{ route('exams.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-flask w-5 mr-2"></i> Exames
                </a>
                <a href="{{ route('surgeries.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-user-md w-5 mr-2"></i> Cirurgias
                </a>
                <a href="{{ route('prescriptions.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-prescription w-5 mr-2"></i> Receitas
                </a>
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
                <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-truck w-5 mr-2"></i> Fornecedores
                </a>
            </div>
        </div>
        @endrole

        @role('admin')
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
            </div>
        </div>
        @endrole

    </nav>
</aside>
