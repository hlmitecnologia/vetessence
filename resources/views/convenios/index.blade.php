@extends('layouts.adminlte', ['title' => 'Convênios'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Convênios</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($convenios->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Plano</th>
                    <th>Desconto</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($convenios as $conv)
                <tr>
                    <td><strong>{{ $conv->name }}</strong></td>
                    <td>{{ $conv->plan_name ?? '-' }}</td>
                    <td>{{ $conv->discount_percent ? $conv->discount_percent . '%' : '-' }}</td>
                    <td>
                        @if($conv->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('convenios.show', $conv) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $conv->id }})" class="btn btn-action btn-primary" title="Editar">
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

<!-- Convenio Modal -->
<div class="modal fade" id="convenioModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convenioModalTitle">Novo Convênio</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('convenio-form', key('convenio-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#convenioModal').modal('hide'); });
        Livewire.on('convenio-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('convenioModalTitle').textContent = 'Novo Convênio';
        $('#convenioModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editConvenio', { id: id });
        document.getElementById('convenioModalTitle').textContent = 'Editar Convênio';
        $('#convenioModal').modal('show');
    }
@endpush
