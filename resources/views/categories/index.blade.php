@extends('layouts.adminlte', ['title' => 'Categorias'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Categorias</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($categories->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Pai</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td><strong>{{ $cat->name }}</strong></td>
                    <td>
                        @php $typeLabels = ['product' => 'Produto', 'service' => 'Serviço', 'vaccine' => 'Vacina']; @endphp
                        <span class="badge badge-secondary">{{ $typeLabels[$cat->type] ?? $cat->type }}</span>
                    </td>
                    <td>{{ $cat->parent->name ?? '-' }}</td>
                    <td>
                        <button onclick="openEditModal({{ $cat->id }})" class="btn btn-action btn-primary" title="Editar">
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
@endsection

@push('modals')
<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Nova Categoria</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('category-form', key('category-form'))
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() {
            $('#categoryModal').modal('hide');
        });

        Livewire.on('category-saved', function() {
            location.reload();
        });
    });

    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('categoryModalTitle').textContent = 'Nova Categoria';
        $('#categoryModal').modal('show');
    }

    function openEditModal(id) {
        Livewire.dispatch('editCategory', { id: id });
        document.getElementById('categoryModalTitle').textContent = 'Editar Categoria';
        $('#categoryModal').modal('show');
    }
</script>
@endpush
