@extends('layouts.adminlte', ['title' => 'Registros de Óbito'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registros de Óbito</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Registro
            </button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Data do Óbito</th>
                    <th>Causa</th>
                    <th>Veterinário</th>
                    <th>Destinação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $record->pet->name ?? 'N/A' }}</td>
                    <td>{{ $record->death_date->format('d/m/Y') }}</td>
                    <td>{{ $record->cause ?? '-' }}</td>
                    <td>{{ $record->attending_vet ?? '-' }}</td>
                    <td>{{ $record->disposition ?? '-' }}</td>
                    <td>
                        <a href="{{ route('pet-death-records.show', $record) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <button onclick="openEditModal({{ $record->id }})" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('pet-death-records.destroy', $record) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar exclusão?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $records->links() }}</div>
</div>

<!-- PetDeathRecord Modal -->
<div class="modal fade" id="petDeathRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="petDeathRecordModalTitle">Novo Registro de Óbito</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('pet-death-record-form', key('pet-death-record-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#petDeathRecordModal').modal('hide'); });
        Livewire.on('pet-death-record-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('petDeathRecordModalTitle').textContent = 'Novo Registro de Óbito';
        $('#petDeathRecordModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editPetDeathRecord', { id: id });
        document.getElementById('petDeathRecordModalTitle').textContent = 'Editar Registro de Óbito';
        $('#petDeathRecordModal').modal('show');
    }
</script>
@endpush
