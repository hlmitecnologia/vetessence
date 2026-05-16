@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-chart-pie"></i> Dashboard Corporativo</h4>

    <form method="GET" class="mb-3">
        <select name="branch_id" class="form-control w-auto d-inline" onchange="this.form.submit()">
            <option value="">Todas as unidades</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info"><div class="inner"><h3>{{ $stats['total_appointments'] ?? 0 }}</h3><p>Total de Consultas</p></div><div class="icon"><i class="fas fa-calendar-check"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success"><div class="inner"><h3>{{ $stats['today_appointments'] ?? 0 }}</h3><p>Consultas Hoje</p></div><div class="icon"><i class="fas fa-clock"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning"><div class="inner"><h3>{{ $stats['total_pets'] ?? 0 }}</h3><p>Total de Pets</p></div><div class="icon"><i class="fas fa-dog"></i></div></div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger"><div class="inner"><h3>R$ {{ number_format($stats['total_invoiced'] ?? 0, 2, ',', '.') }}</h3><p>Total Faturado</p></div><div class="icon"><i class="fas fa-dollar-sign"></i></div></div>
        </div>
    </div>

    @if($branches->count() > 1)
    <div class="card">
        <div class="card-header"><h5>Comparativo por Unidade</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Unidade</th><th>Consultas</th><th>Pets</th><th>Pacientes (este mes)</th><th>Faturamento Total</th></tr></thead>
                <tbody>
                    @foreach($branchStats as $bs)
                    <tr>
                        <td><strong>{{ $bs->branch->name }}</strong></td>
                        <td>{{ $bs->appointments }}</td>
                        <td>{{ $bs->pets }}</td>
                        <td>{{ $bs->patients ?? 0 }}</td>
                        <td>R$ {{ number_format($bs->invoiced, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Faturamento Mensal por Unidade ({{ now()->year }})</h5></div>
        <div class="card-body">
            <canvas id="corpChart" height="100"></canvas>
        </div>
    </div>
    @endif

    <p class="text-muted"><i class="fas fa-users"></i> {{ $users }} usuarios cadastrados</p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('corpChart');
    if (ctx) {
        const months = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        const datasets = [];
        const labels = months.filter((_, i) => i < new Date().getMonth() + 1);

        @foreach($branchStats as $bs)
        datasets.push({
            label: '{{ $bs->branch->name }}',
            data: [@for($m = 1; $m <= now()->month; $m++){{ $bs->monthly[$m] ?? 0 }},@endfor],
            borderColor: 'hsl({{ loop->index * 60 }}, 70%, 50%)',
            fill: false,
        });
        @endforeach

        new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
</script>
@endpush
