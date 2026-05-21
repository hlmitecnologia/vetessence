@extends('layouts.adminlte', ['title' => 'Templates de Banho/Tosa'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Templates de Banho/Tosa</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo</button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Preço</th>
                    <th>Duração</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->species ?? '-' }}</td>
                    <td>{{ $t->breed ?? '-' }}</td>
                    <td>{{ $t->size ?? '-' }}</td>
                    <td>R$ {{ number_format($t->price, 2, ',', '.') }}</td>
                    <td>{{ $t->estimated_minutes }} min</td>
                    <td>{!! $t->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('grooming-templates.show', $t) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <button onclick="openEditModal({{ $t->id }})" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('grooming-templates.destroy', $t) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Nenhum template encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $templates->links() }}</div>
</div>

<!-- GroomingTemplate Modal -->
<div class="modal fade" id="groomingTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groomingTemplateModalTitle">Novo Template de Banho/Tosa</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('grooming-template-form', key('grooming-template-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#groomingTemplateModal').modal('hide'); });
        Livewire.on('grooming-template-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('groomingTemplateModalTitle').textContent = 'Novo Template de Banho/Tosa';
        $('#groomingTemplateModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editGroomingTemplate', { id: id });
        document.getElementById('groomingTemplateModalTitle').textContent = 'Editar Template de Banho/Tosa';
        $('#groomingTemplateModal').modal('show');
    }
@endpush
