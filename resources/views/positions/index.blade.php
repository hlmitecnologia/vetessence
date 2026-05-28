@extends('layouts.adminlte', ['title' => 'Cargos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cargos</h3>
        <div class="card-tools">
            @can('positions.create')
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Cargo</button>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($positions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Departamento</th>
                    <th>Descrição</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($positions as $position)
                <tr>
                    <td><strong>{{ $position->name }}</strong></td>
                    <td>{{ $position->department->name ?? '-' }}</td>
                    <td>{{ $position->description ?? '-' }}</td>
                    <td>
                        <a href="{{ route('positions.show', $position) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        @can('positions.edit')
                        <button onclick="openEditModal({{ $position->id }})" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></button>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $positions->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted my-4">Nenhum cargo cadastrado.</p>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Position Modal -->
<div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionModalTitle">Novo Cargo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('position-form', key('position-form'))
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#positionModal').modal('hide'); });
        Livewire.on('position-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('positionModalTitle').textContent = 'Novo Cargo';
        $('#positionModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editPosition', { id: id });
        document.getElementById('positionModalTitle').textContent = 'Editar Cargo';
        $('#positionModal').modal('show');
    }
</script>
@endpush
