@extends('layouts.adminlte', ['title' => 'Doenças Zoonóticas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-biohazard"></i> Doenças Zoonóticas</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nova
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou agente causal..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-control">
                        <option value="">Todas as categorias</option>
                        <option value="viral" {{ request('category') == 'viral' ? 'selected' : '' }}>Viral</option>
                        <option value="bacterial" {{ request('category') == 'bacterial' ? 'selected' : '' }}>Bacteriana</option>
                        <option value="parasitic" {{ request('category') == 'parasitic' ? 'selected' : '' }}>Parasitária</option>
                        <option value="fungal" {{ request('category') == 'fungal' ? 'selected' : '' }}>Fúngica</option>
                        <option value="prion" {{ request('category') == 'prion' ? 'selected' : '' }}>Prion</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info"><i class="fas fa-search"></i> Filtrar</button>
                </div>
            </div>
        </form>

        @if($diseases->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Agente Causador</th>
                    <th>Categoria</th>
                    <th>Notificável</th>
                    <th>Ativo</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($diseases as $disease)
                <tr>
                    <td><strong>{{ $disease->name }}</strong></td>
                    <td class="text-truncate" style="max-width: 250px;">{{ $disease->causative_agent ?? '-' }}</td>
                    <td>
                        @php $catColors = ['viral' => 'danger', 'bacterial' => 'warning', 'parasitic' => 'info', 'fungal' => 'secondary', 'prion' => 'dark']; @endphp
                        <span class="badge badge-{{ $catColors[$disease->category] ?? 'secondary' }}">{{ $disease->category_label }}</span>
                    </td>
                    <td>
                        @if($disease->is_notifiable)
                            <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        @if($disease->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-danger">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('zoonotic-diseases.show', $disease) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $disease->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $diseases->appends(request()->query())->links() }}</div>
        @else
        <p class="text-center text-muted">Nenhuma doença encontrada.</p>
        @endif
    </div>
</div>

<!-- ZoonoticDisease Modal -->
<div class="modal fade" id="zoonoticDiseaseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="zoonoticDiseaseModalTitle">Nova Doença Zoonótica</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('zoonotic-disease-form', key('zoonotic-disease-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#zoonoticDiseaseModal').modal('hide'); });
        Livewire.on('zoonotic-disease-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('zoonoticDiseaseModalTitle').textContent = 'Nova Doença Zoonótica';
        $('#zoonoticDiseaseModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editZoonoticDisease', { id: id });
        document.getElementById('zoonoticDiseaseModalTitle').textContent = 'Editar Doença Zoonótica';
        $('#zoonoticDiseaseModal').modal('show');
    }
@endpush
