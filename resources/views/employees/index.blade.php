@extends('layouts.adminlte', ['title' => 'Funcionários'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Funcionários</h3>
        <div class="card-tools">
            @can('employees.create')
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Funcionário
            </button>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('employees.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <x-tom-select name="department_id" :value="request('department_id')">
                            @foreach($departments as $id => $name)
                            <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <x-tom-select name="position_id" :value="request('position_id')">
                            @foreach($positions as $id => $name)
                            <option value="{{ $id }}" {{ request('position_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <x-tom-select name="branch_id" :value="request('branch_id')">
                            @foreach($branches as $id => $name)
                            <option value="{{ $id }}" {{ request('branch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <select name="contract_type" class="form-control">
                            <option value="">Todos Contratos</option>
                            @foreach($contractTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('contract_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        @if($employees->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Departamento</th>
                    <th>Cargo</th>
                    <th>Unidade</th>
                    <th>Contrato</th>
                    <th>Status</th>
                    <th style="width: 140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>
                        <strong>{{ $employee->name }}</strong>
                        @unless($employee->role_id)
                            <span class="badge badge-warning ml-1">Sem perfil</span>
                        @endunless
                        @unless($employee->department_id)
                            <span class="badge badge-info ml-1">Dados incompletos</span>
                        @endunless
                    </td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->department->name ?? '-' }}</td>
                    <td>{{ $employee->position->name ?? '-' }}</td>
                    <td>{{ $employee->branch->name ?? '-' }}</td>
                    <td>{{ $contractTypes[$employee->contract_type] ?? $employee->contract_type ?? '-' }}</td>
                    <td>
                        @if($employee->is_active)
                        <span class="badge badge-success">Ativo</span>
                        @else
                        <span class="badge badge-secondary">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-action btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                        @can('employees.edit')
                        <button onclick="openEditModal({{ $employee->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @else
        <p class="text-center text-muted my-4">Nenhum funcionário encontrado.</p>
        @endif
    </div>
</div>

<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalTitle">Novo Funcionário</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('employee-form', key('employee-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#employeeModal').modal('hide'); });
        Livewire.on('user-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('employeeModalTitle').textContent = 'Novo Funcionário';
        $('#employeeModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editUser', { id: id });
        document.getElementById('employeeModalTitle').textContent = 'Editar Funcionário';
        $('#employeeModal').modal('show');
    }
</script>
@endpush
