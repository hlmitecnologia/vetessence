@extends('layouts.adminlte', ['title' => 'Interações Medicamentosas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Interações Medicamentosas</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova Interação
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar medicamento..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="severity" class="form-control form-control-sm">
                    <option value="">Todas as severidades</option>
                    <option value="contraindicated" {{ request('severity') == 'contraindicated' ? 'selected' : '' }}>Contraindicada</option>
                    <option value="caution" {{ request('severity') == 'caution' ? 'selected' : '' }}>Precaução</option>
                    <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Menor</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-control form-control-sm">
                    <option value="">Ativo/Inativo</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('drug-interactions.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($interactions->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicamento A</th>
                    <th>Medicamento B</th>
                    <th>Severidade</th>
                    <th>Categoria</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interactions as $interaction)
                <tr>
                    <td><strong>{{ $interaction->drug_a }}</strong></td>
                    <td><strong>{{ $interaction->drug_b }}</strong></td>
                    <td>
                        @if($interaction->severity == 'contraindicated')
                            <span class="badge badge-danger">Contraindicada</span>
                        @elseif($interaction->severity == 'caution')
                            <span class="badge badge-warning">Precaução</span>
                        @else
                            <span class="badge badge-info">Menor</span>
                        @endif
                    </td>
                    <td>{{ $interaction->category ?? '-' }}</td>
                    <td>
                        @if($interaction->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('drug-interactions.show', $interaction) }}" class="btn btn-action btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $interaction->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('drug-interactions.destroy', $interaction) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-confirm="Tem certeza?" class="btn btn-action btn-danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $interactions->appends(request()->query())->links() }}
        </div>
        @else
        <p class="text-center text-muted my-4">Nenhuma interação medicamentosa cadastrada.</p>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- DrugInteraction Modal -->
<div class="modal fade" id="drugInteractionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="drugInteractionModalTitle">Nova Interação Medicamentosa</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('drug-interaction-form', key('drug-interaction-form'))
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#drugInteractionModal').modal('hide'); });
        Livewire.on('drug-interaction-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('drugInteractionModalTitle').textContent = 'Nova Interação Medicamentosa';
        $('#drugInteractionModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editDrugInteraction', { id: id });
        document.getElementById('drugInteractionModalTitle').textContent = 'Editar Interação Medicamentosa';
        $('#drugInteractionModal').modal('show');
    }
</script>
@endpush
