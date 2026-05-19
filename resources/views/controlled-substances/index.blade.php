@extends('layouts.adminlte', ['title' => 'Substâncias Controladas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Substâncias Controladas</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Substância
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($substances->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Princípio Ativo</th>
                    <th>Lista/Controle</th>
                    <th>Registro ANVISA</th>
                    <th>Estoque Atual</th>
                    <th>Estoque Mínimo</th>
                    <th>Status</th>
                    <th style="width: 140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($substances as $substance)
                <tr>
                    <td><strong>{{ $substance->name }}</strong></td>
                    <td>{{ $substance->active_ingredient ?? '-' }}</td>
                    <td>{{ $substance->schedule ?? '-' }}</td>
                    <td>{{ $substance->anvisa_register ?? '-' }}</td>
                    <td class="{{ $substance->current_stock <= $substance->min_stock ? 'text-danger font-weight-bold' : '' }}">
                        {{ number_format($substance->current_stock, 2, ',', '.') }} {{ $substance->unit }}
                    </td>
                    <td>{{ number_format($substance->min_stock, 2, ',', '.') }} {{ $substance->unit }}</td>
                    <td>
                        @if($substance->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-secondary">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('controlled-substances.show', $substance) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $substance->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('controlled-substances.destroy', $substance) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma substância controlada cadastrada.</p>
        @endif
    </div>
</div>

<!-- ControlledSubstance Modal -->
<div class="modal fade" id="controlledSubstanceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="controlledSubstanceModalTitle">Nova Substância</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('controlled-substance-form', key('controlled-substance-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#controlledSubstanceModal').modal('hide'); });
        Livewire.on('controlled-substance-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('controlledSubstanceModalTitle').textContent = 'Nova Substância';
        $('#controlledSubstanceModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editControlledSubstance', { id: id });
        document.getElementById('controlledSubstanceModalTitle').textContent = 'Editar Substância';
        $('#controlledSubstanceModal').modal('show');
    }
</script>
@endpush
