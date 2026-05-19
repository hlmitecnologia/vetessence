@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-pills"></i> Formulario de Farmacos</h4>
        <button onclick="openCreateModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Novo</button>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead><tr><th>Farmaco</th><th>Especie</th><th>mg/kg</th><th>Dose Max</th><th>Via</th><th>Ativo</th><th></th></tr></thead>
                        <tbody>
                            @forelse($formularies as $f)
                            <tr>
                                <td>{{ $f->drug }}</td>
                                <td>{{ $f->species }}</td>
                                <td>{{ $f->dosage_mg_kg }}</td>
                                <td>{{ $f->max_dose ?? '-' }}</td>
                                <td>{{ $f->route ?? '-' }}</td>
                                <td>{!! $f->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Nao</span>' !!}</td>
                                <td>
                                    <button onclick="openEditModal({{ $f->id }})" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('drug-formulary.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">Nenhum farmaco cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $formularies->links() }}
        </div>
        <div class="col-md-4">
            @livewire('dosage-calculator')
        </div>
    </div>
</div>

<!-- DrugFormulary Modal -->
<div class="modal fade" id="drugFormularyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="drugFormularyModalTitle">Novo Fármaco</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('drug-formulary-form', key('drug-formulary-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#drugFormularyModal').modal('hide'); });
        Livewire.on('drug-formulary-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('drugFormularyModalTitle').textContent = 'Novo Fármaco';
        $('#drugFormularyModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editDrugFormulary', { id: id });
        document.getElementById('drugFormularyModalTitle').textContent = 'Editar Fármaco';
        $('#drugFormularyModal').modal('show');
    }
</script>
@endpush
