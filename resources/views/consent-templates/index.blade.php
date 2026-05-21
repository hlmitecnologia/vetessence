@extends('layouts.adminlte', ['title' => 'Modelos de Termos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modelos de Termos de Consentimento</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Modelo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($templates->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Ativo</th>
                    <th>Usado em</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>{{ $template->category ?? '-' }}</td>
                    <td class="text-truncate" style="max-width: 250px;">{{ $template->description ?? '-' }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>{{ $template->consentForms->count() }}</td>
                    <td>
                        <button onclick="openEditModal({{ $template->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('consent-templates.destroy', $template) }}" method="POST" class="d-inline">
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
        <p class="text-center text-muted">Nenhum modelo de termo encontrado.</p>
        @endif
    </div>
</div>

<!-- ConsentTemplate Modal -->
<div class="modal fade" id="consentTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consentTemplateModalTitle">Novo Modelo de Termo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('consent-template-form', key('consent-template-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#consentTemplateModal').modal('hide'); });
        Livewire.on('consent-template-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('consentTemplateModalTitle').textContent = 'Novo Modelo de Termo';
        $('#consentTemplateModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editConsentTemplate', { id: id });
        document.getElementById('consentTemplateModalTitle').textContent = 'Editar Modelo de Termo';
        $('#consentTemplateModal').modal('show');
    }
@endpush
