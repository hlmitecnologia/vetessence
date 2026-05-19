@extends('layouts.adminlte', ['title' => 'Modelos de Laudo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modelos de Laudo Clínico</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Modelo
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="species" class="form-control form-control-sm">
                    <option value="">Todas as espécies</option>
                    <option value="canine" {{ request('species') == 'canine' ? 'selected' : '' }}>Canina</option>
                    <option value="feline" {{ request('species') == 'feline' ? 'selected' : '' }}>Felina</option>
                    <option value="equine" {{ request('species') == 'equine' ? 'selected' : '' }}>Equina</option>
                    <option value="bovine" {{ request('species') == 'bovine' ? 'selected' : '' }}>Bovina</option>
                    <option value="other" {{ request('species') == 'other' ? 'selected' : '' }}>Outras</option>
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
                <a href="{{ route('clinical-report-templates.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>

        @if($templates->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Espécie</th>
                    <th>Especialidade</th>
                    <th>Categoria</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>{{ $template->species ?? 'Todas' }}</td>
                    <td>{{ $template->specialty ?? '-' }}</td>
                    <td>{{ $template->category ?? '-' }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('clinical-report-templates.show', $template) }}" class="btn btn-action btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openEditModal({{ $template->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('clinical-report-templates.destroy', $template) }}" method="POST" class="d-inline">
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
        <div class="mt-3">
            {{ $templates->appends(request()->query())->links() }}
        </div>
        @else
        <p class="text-center text-muted my-4">Nenhum modelo de laudo encontrado.</p>
        @endif
    </div>
</div>

<!-- ClinicalReportTemplate Modal -->
<div class="modal fade" id="clinicalReportTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clinicalReportTemplateModalTitle">Novo Modelo de Laudo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('clinical-report-template-form', key('clinical-report-template-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#clinicalReportTemplateModal').modal('hide'); });
        Livewire.on('clinical-report-template-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('clinicalReportTemplateModalTitle').textContent = 'Novo Modelo de Laudo';
        $('#clinicalReportTemplateModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editClinicalReportTemplate', { id: id });
        document.getElementById('clinicalReportTemplateModalTitle').textContent = 'Editar Modelo de Laudo';
        $('#clinicalReportTemplateModal').modal('show');
    }
</script>
@endpush
