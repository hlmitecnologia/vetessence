@extends('layouts.adminlte', ['title' => 'Modelos de Comunicação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modelos de Comunicação</h3>
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
                    <th>Tipo</th>
                    <th>Canal</th>
                    <th>Assunto</th>
                    <th>Ativo</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td><strong>{{ $template->name }}</strong></td>
                    <td>
                        @php
                            $typeLabels = ['reminder' => 'Lembrete', 'recall' => 'Rechamada', 'promotional' => 'Promocional', 'notification' => 'Notificação', 'other' => 'Outro'];
                        @endphp
                        <span class="badge badge-info">{{ $typeLabels[$template->type] ?? $template->type }}</span>
                    </td>
                    <td>
                        @php
                            $channelLabels = ['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'sms' => 'SMS', 'push' => 'Push'];
                            $channelIcons = ['whatsapp' => 'fab fa-whatsapp', 'email' => 'fas fa-envelope', 'sms' => 'fas fa-sms', 'push' => 'fas fa-bell'];
                        @endphp
                        @if($template->channel)
                            <i class="{{ $channelIcons[$template->channel] ?? 'fas fa-comment' }}"></i>
                            {{ $channelLabels[$template->channel] ?? $template->channel }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $template->subject ?? '-' }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge badge-success">Sim</span>
                        @else
                            <span class="badge badge-secondary">Não</span>
                        @endif
                    </td>
                    <td>
                        <button onclick="openEditModal({{ $template->id }})" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('communication-templates.destroy', $template) }}" method="POST" class="d-inline">
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
        <p class="text-center text-muted">Nenhum modelo de comunicação encontrado.</p>
        @endif
    </div>
</div>

<!-- CommunicationTemplate Modal -->
<div class="modal fade" id="communicationTemplateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="communicationTemplateModalTitle">Novo Modelo de Comunicação</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('communication-template-form', key('communication-template-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('close-modal', function() { $('#communicationTemplateModal').modal('hide'); });
        Livewire.on('communication-template-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('communicationTemplateModalTitle').textContent = 'Novo Modelo de Comunicação';
        $('#communicationTemplateModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editCommunicationTemplate', { id: id });
        document.getElementById('communicationTemplateModalTitle').textContent = 'Editar Modelo de Comunicação';
        $('#communicationTemplateModal').modal('show');
    }
</script>
@endpush
