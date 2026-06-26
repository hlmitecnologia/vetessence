@extends('layouts.adminlte', ['title' => 'Usuários'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Usuários</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle font-weight-bold text-sm" style="width: 32px; height: 32px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white); color: var(--brand-primary, #455e36);">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-weight-bold ml-2">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name ?? '-' }}</td>
                    <td>{{ $user->branch->name ?? 'Todas' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <button onclick="openEditModal({{ $user->id }})" class="btn btn-action btn-primary" title="Editar">
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

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Novo Usuário</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('employee-form', ['context' => 'user'], key('user-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function refreshTomSelects() {
        var modal = document.getElementById('userModal');
        modal.querySelectorAll('.tom-select-wrapper[data-wire]').forEach(function(wrapper) {
            var wireModel = wrapper.dataset.wire;
            var componentEl = wrapper.closest('[wire\\:id]');
            if (!componentEl || !window.Livewire) return;
            var component = Livewire.find(componentEl.getAttribute('wire:id'));
            if (!component) return;
            var value = component.get(wireModel);
            wrapper.dataset.value = typeof value !== 'undefined' && value !== null ? String(value) : '';
        });
        destroyTomSelects(modal);
        initTomSelects(modal);
    }

    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#userModal').modal('hide'); });
        Livewire.on('user-saved', function() { location.reload(); });
        Livewire.on('employee-loaded', function() { refreshTomSelects(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('userModalTitle').textContent = 'Novo Usuário';
        $('#userModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editUser', { id: id });
        document.getElementById('userModalTitle').textContent = 'Editar Usuário';
        $('#userModal').modal('show');
    }
</script>
@endpush
