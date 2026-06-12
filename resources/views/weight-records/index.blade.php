@extends('layouts.adminlte', ['title' => 'Registros de Peso'])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registros de Peso</h3>
                <div class="card-tools">
                    <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Novo Registro
                    </button>
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
                                    <button onclick="openEditModal({{ $record->id }})" class="btn btn-action btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('weight-records.destroy', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-confirm="Tem certeza?" class="btn btn-action btn-danger" title="Excluir">
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

<!-- WeightRecord Modal -->
<div class="modal fade" id="weightRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="weightRecordModalTitle">Novo Registro de Peso</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('weight-record-form', key('weight-record-form'))
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

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#weightRecordModal').modal('hide'); });
        Livewire.on('weight-record-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('weightRecordModalTitle').textContent = 'Novo Registro de Peso';
        $('#weightRecordModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editWeightRecord', { id: id });
        document.getElementById('weightRecordModalTitle').textContent = 'Editar Registro de Peso';
        $('#weightRecordModal').modal('show');
    }
@endpush
