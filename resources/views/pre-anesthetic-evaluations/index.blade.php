@extends('layouts.adminlte', ['title' => 'Avaliações Pré-Anestésicas'])
@section('content')
    <div class="card">
        <div class="card-header">
            <button onclick="openCreateModal()" class="btn btn-primary">Nova Avaliação</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Pet</th><th>ASA</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                @foreach($evaluations as $e)
                    <tr>
                        <td>{{ $e->pet->name ?? '-' }}</td>
                        <td>{{ $e->asa_score }}</td>
                        <td>{{ $e->status }}</td>
                        <td>
                            <a href="{{ route('pre-anesthetic-evaluations.show', $e) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            <button onclick="openEditModal({{ $e->id }})" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $evaluations->links() }}</div>
    </div>

<!-- PreAnestheticEvaluation Modal -->
<div class="modal fade" id="preAnestheticEvaluationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="preAnestheticEvaluationModalTitle">Nova Avaliação</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('pre-anesthetic-evaluation-form', key('pre-anesthetic-evaluation-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#preAnestheticEvaluationModal').modal('hide'); });
        Livewire.on('pre-anesthetic-evaluation-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('preAnestheticEvaluationModalTitle').textContent = 'Nova Avaliação';
        $('#preAnestheticEvaluationModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editPreAnestheticEvaluation', { id: id });
        document.getElementById('preAnestheticEvaluationModalTitle').textContent = 'Editar Avaliação';
        $('#preAnestheticEvaluationModal').modal('show');
    }
@endpush
