@extends('layouts.adminlte', ['title' => 'Dashboard'])

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['todayAppointments'] ?? 0 }}</h3>
                <p>Consultas Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-check"></i></div>
            <a href="{{ route('appointments.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['totalPets'] ?? 0 }}</h3>
                <p>Total de Pets</p>
            </div>
            <div class="icon"><i class="fas fa-paw"></i></div>
            <a href="{{ route('pets.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>R$ {{ number_format($stats['monthRevenue'] ?? 0, 2, ',', '.') }}</h3>
                <p>Receita do Mês</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            <a href="{{ route('invoices.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['lowStock'] ?? 0 }}</h3>
                <p>Estoque Baixo</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('products.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['todayRevenue'] ? 'R$ ' . number_format($stats['todayRevenue'], 2, ',', '.') : 'R$ 0,00' }}</h3>
                <p>Receita Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ route('invoices.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $stats['todayProcedures'] ?? 0 }}</h3>
                <p>Atendimentos Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-stethoscope"></i></div>
            <a href="{{ route('medical-records.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['activeHospitalizations'] ?? 0 }}</h3>
                <p>Internados</p>
            </div>
            <div class="icon"><i class="fas fa-procedures"></i></div>
            <a href="{{ route('hospitalizations.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-pink">
            <div class="inner">
                <h3>{{ number_format($stats['noShowRate'] ?? 0, 1) }}%</h3>
                <p>Taxa de Ausência</p>
            </div>
            <div class="icon"><i class="fas fa-user-slash"></i></div>
            <a href="{{ route('appointments.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>{{ $stats['pendingReminders'] ?? 0 }}</h3>
                <p>Lembretes Pendentes</p>
            </div>
            <div class="icon"><i class="fas fa-bell"></i></div>
            <a href="{{ route('vaccination-reminders.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>{{ $stats['overdueParasiteControls'] ?? 0 }}</h3>
                <p>Parasitários Atrasados</p>
            </div>
            <div class="icon"><i class="fas fa-bug"></i></div>
            <a href="{{ route('parasite-controls.index') }}" class="small-box-footer">Ver mais <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Receita Mensal</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Atendimentos por Tipo</h3>
            </div>
            <div class="card-body">
                <canvas id="appointmentsChart" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribuição por Espécie</h3>
            </div>
            <div class="card-body">
                <canvas id="speciesChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Próximas Consultas</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    @forelse($upcomingAppointments ?? [] as $appointment)
                    <li class="nav-item">
                        <a href="{{ route('appointments.show', $appointment) }}" class="nav-link">
                            <i class="fas fa-calendar mr-2"></i> {{ $appointment->pet->name ?? 'Pet' }}
                            <span class="float-right text-muted text-sm">{{ $appointment->time->format('H:i') }}</span>
                        </a>
                    </li>
                    @empty
                    <li class="nav-item"><span class="nav-link text-muted">Nenhuma consulta agendada</span></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos Atendimentos</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    @forelse($recentRecords ?? [] as $record)
                    <li class="nav-item">
                        <a href="{{ route('medical-records.show', $record) }}" class="nav-link">
                            <i class="fas fa-file-medical mr-2"></i> {{ $record->pet->name ?? 'Pet' }}
                            <span class="float-right text-muted text-sm">{{ Str::limit($record->diagnosis, 20) }}</span>
                        </a>
                    </li>
                    @empty
                    <li class="nav-item"><span class="nav-link text-muted">Nenhum atendimento</span></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lembretes Atrasados</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    @forelse($overdueReminders ?? [] as $reminder)
                    <li class="nav-item">
                        <a href="{{ route('vaccination-reminders.show', $reminder) }}" class="nav-link">
                            <i class="fas fa-bell mr-2 text-warning"></i> {{ $reminder->pet->name ?? 'Pet' }}
                            <span class="float-right text-warning text-sm">{{ $reminder->scheduled_date->format('d/m') }}</span>
                        </a>
                    </li>
                    @empty
                    <li class="nav-item"><span class="nav-link text-muted">Nenhum lembrete atrasado</span></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Parasitários Atrasados</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    @forelse($overdueParasiteList ?? [] as $pc)
                    <li class="nav-item">
                        <a href="{{ route('parasite-controls.show', $pc) }}" class="nav-link">
                            <i class="fas fa-bug mr-2 text-danger"></i> {{ $pc->pet->name ?? 'Pet' }} - {{ $pc->product_name }}
                            <span class="float-right text-danger text-sm">{{ optional($pc->next_due_date)->format('d/m') }}</span>
                        </a>
                    </li>
                    @empty
                    <li class="nav-item"><span class="nav-link text-muted">Nenhum parasitário atrasado</span></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueByMonth->pluck('month')->toArray()) !!},
            datasets: [{
                label: 'Receita (R$)',
                data: {!! json_encode($revenueByMonth->pluck('total')->toArray()) !!},
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
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

    var apptLabels = {!! json_encode($appointmentsByType->pluck('type')->toArray()) !!};
    var apptData = {!! json_encode($appointmentsByType->pluck('count')->toArray()) !!};
    console.log('appointmentsByType labels:', apptLabels, 'data:', apptData);
    if (apptLabels.length) {
        var appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
        new Chart(appointmentsCtx, {
            type: 'doughnut',
            data: {
                labels: apptLabels,
                datasets: [{
                    data: apptData,
                    backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const speciesCtx = document.getElementById('speciesChart').getContext('2d');
    new Chart(speciesCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($speciesDistribution->pluck('name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($speciesDistribution->pluck('count')->toArray()) !!},
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#e83e8c']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endpush
