@extends('layouts.app', ['title' => 'Dashboard'])

@section('header')
    Dashboard
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js">
@endpush

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Consultas Hoje</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['todayAppointments'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total de Pets</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['totalPets'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paw text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Receita do Mês</p>
                <p class="text-3xl font-bold text-gray-800">R$ {{ number_format($stats['monthRevenue'] ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Estoque Baixo</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['lowStock'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Receita Mensal</h3>
        <canvas id="revenueChart" height="250"></canvas>
    </div>

    <!-- Appointments by Type -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Atendimentos por Tipo</h3>
        <canvas id="appointmentsChart" height="250"></canvas>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Upcoming Appointments -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Próximas Consultas</h3>
            <a href="{{ route('appointments.index') }}" class="text-indigo-600 text-sm hover:underline">Ver todas</a>
        </div>
        <div class="space-y-3">
            @forelse($upcomingAppointments ?? [] as $appointment)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold text-sm">
                    {{ substr($appointment->pet->name ?? 'P', 0, 1) }}
                </div>
                <div class="ml-3 flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ $appointment->pet->name ?? 'Pet' }}</p>
                    <p class="text-xs text-gray-500">{{ $appointment->time->format('H:i') }} - {{ ucfirst($appointment->type) }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4 text-sm">Nenhuma consulta agendada</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Medical Records -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Últimos Atendimentos</h3>
            <a href="{{ route('medical-records.index') }}" class="text-indigo-600 text-sm hover:underline">Ver todos</a>
        </div>
        <div class="space-y-3">
            @forelse($recentRecords ?? [] as $record)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-sm">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ $record->pet->name ?? 'Pet' }}</p>
                    <p class="text-xs text-gray-500">{{ Str::limit($record->diagnosis, 30) ?? 'Sem diagnóstico' }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4 text-sm">Nenhum atendimento</p>
            @endforelse
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Estoque Baixo</h3>
            <a href="{{ route('products.index') }}" class="text-indigo-600 text-sm hover:underline">Ver todos</a>
        </div>
        <div class="space-y-3">
            @forelse($lowStockProducts ?? [] as $product)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-sm">
                    <i class="fas fa-box"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                    <p class="text-xs text-red-600">Estoque: {{ $product->stock }} (mín: {{ $product->min_stock }})</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4 text-sm">Nenhum produto em falta</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Species Distribution -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribuição por Espécie</h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <canvas id="speciesChart" height="200"></canvas>
        <div class="flex items-center justify-center">
            @foreach($speciesDistribution ?? [] as $species)
            <div class="text-center px-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-2xl font-bold text-indigo-600">{{ $species['count'] }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ $species['name'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueByMonth->pluck('month')->toArray()) !!},
            datasets: [{
                label: 'Receita (R$)',
                data: {!! json_encode($revenueByMonth->pluck('total')->toArray()) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($appointmentsByType->pluck('type')->toArray()) !!},
            datasets: [{
                label: 'Atendimentos',
                data: {!! json_encode($appointmentsByType->pluck('count')->toArray()) !!},
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Species Chart
    const speciesCtx = document.getElementById('speciesChart').getContext('2d');
    new Chart(speciesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(collect($speciesDistribution)->pluck('name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode(collect($speciesDistribution)->pluck('count')->toArray()) !!},
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });
});
</script>
@endpush
