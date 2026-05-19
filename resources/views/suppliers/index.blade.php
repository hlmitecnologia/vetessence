@extends('layouts.adminlte', ['title' => 'Fornecedores'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Fornecedores</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($suppliers->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $sup)
                <tr>
                    <td><strong>{{ $sup->name }}</strong></td>
                    <td>{{ $sup->cnpj ?? '-' }}</td>
                    <td>{{ $sup->phone ?? '-' }}</td>
                    <td>{{ $sup->email ?? '-' }}</td>
                    <td>
                        <a href="{{ route('suppliers.show', $sup) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $sup->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalTitle">Novo Fornecedor</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('supplier-form', key('supplier-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#supplierModal').modal('hide'); });
        Livewire.on('supplier-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('supplierModalTitle').textContent = 'Novo Fornecedor';
        $('#supplierModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editSupplier', { id: id });
        document.getElementById('supplierModalTitle').textContent = 'Editar Fornecedor';
        $('#supplierModal').modal('show');
    }
</script>
@endpush
