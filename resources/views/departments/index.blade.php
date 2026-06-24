@extends('layouts.adminlte', ['title' => 'Departamentos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Departamentos</h3>
        <div class="card-tools">
            @can('departments.create')
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Departamento</button>
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
        @if($departments->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Cargos</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td><strong>{{ $department->name }}</strong></td>
                    <td>{{ $department->description ?? '-' }}</td>
                    <td>{{ $department->positions_count }}</td>
                    <td>
                        <a href="{{ route('departments.show', $department) }}" class="btn btn-action btn-info"><i class="fas fa-eye"></i></a>
                        @can('departments.edit')
                        <button onclick="openEditModal({{ $department->id }})" class="btn btn-action btn-primary"><i class="fas fa-edit"></i></button>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-center text-muted my-4">Nenhum departamento cadastrado.</p>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalTitle">Nova Departamento</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('department-form', key('department-form'))
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#departmentModal').modal('hide'); });
        Livewire.on('department-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('departmentModalTitle').textContent = 'Nova Departamento';
        $('#departmentModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editDepartment', { id: id });
        document.getElementById('departmentModalTitle').textContent = 'Editar Departamento';
        $('#departmentModal').modal('show');
    }
</script>
@endpush
