@extends('layouts.adminlte', ['title' => 'Registros de Peso'])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registros de Peso</h3>
                <div class="card-tools">
                    <a href="{{ route('weight-records.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Novo Registro
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(isset($pet) && $weightRecords->count() > 1)
                <div class="mb-4">
                    <canvas id="weightChart" height="100"></canvas>
                </div>
                <hr>
                @endif

                @if($weightRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Pet</th>
                                <th>Peso (kg)</th>
                                <th>ECC</th>
                                <th>Data</th>
                                <th>Medido Por</th>
                                <th>Observações</th>
                                <th style="width: 80px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weightRecords as $record)
                            <tr>
                                <td><strong>{{ $record->pet->name ?? '-' }}</strong></td>
                                <td>{{ number_format($record->weight, 2, ',', '.') }} kg</td>
                                <td>{{ $record->bcs ?? '-' }}</td>
                                <td>{{ $record->measurement_date->format('d/m/Y') }}</td>
                                <td>{{ $record->measuredBy->name ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $record->notes ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('weight-records.destroy', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Nenhum registro de peso encontrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
@if(isset($pet) && $weightRecords->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('weightChart').getContext('2d');
    var records = @json($weightRecords->sortBy('measurement_date')->values());
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: records.map(function(r) { return new Date(r.measurement_date).toLocaleDateString('pt-BR'); }),
            datasets: [{
                label: 'Peso (kg)',
                data: records.map(function(r) { return r.weight; }),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ctx.parsed.y + ' kg'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: 'Peso (kg)' }
                }
            }
        }
    });
});
</script>
@endif
@endpush
