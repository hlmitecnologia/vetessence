@extends('layouts.adminlte', ['title' => $employee->name])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $employee->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('employees.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
            @can('employees.edit')
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary btn-sm" onclick="event.preventDefault(); Livewire.dispatch('editUser', { id: {{ $employee->id }} }); $('#employeeModal').modal('show');">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Nome:</strong><p>{{ $employee->name }}</p></div>
            <div class="col-md-4"><strong>Email:</strong><p>{{ $employee->email }}</p></div>
            <div class="col-md-4"><strong>Telefone:</strong><p>{{ $employee->phone ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Departamento:</strong><p>{{ $employee->department->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Cargo:</strong><p>{{ $employee->position->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Unidade:</strong><p>{{ $employee->branch->name ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Tipo de Contrato:</strong><p>{{ $contractTypes[$employee->contract_type] ?? $employee->contract_type ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Data de Contratação:</strong><p>{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '-' }}</p></div>
            <div class="col-md-4"><strong>Status:</strong><p>@if($employee->is_active) <span class="badge badge-success">Ativo</span> @else <span class="badge badge-secondary">Inativo</span> @endif</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Perfil:</strong><p>{{ $employee->role->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>CRMV:</strong><p>{{ $employee->crmv ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4"><strong>Contato de Emergência:</strong><p>{{ $employee->emergency_contact ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Telefone de Emergência:</strong><p>{{ $employee->emergency_phone ?? '-' }}</p></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6"><strong>Cadastrado em:</strong><p>{{ $employee->created_at->format('d/m/Y H:i') }}</p></div>
            <div class="col-md-6"><strong>Atualizado em:</strong><p>{{ $employee->updated_at->format('d/m/Y H:i') }}</p></div>
        </div>
    </div>
</div>
@endsection

@can('employees.edit')
<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Funcionário</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('employee-form', key('employee-form-show'))
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#employeeModal').modal('hide'); });
        Livewire.on('user-saved', function() { location.reload(); });
    });
</script>
@endpush
@endcan
