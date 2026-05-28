@extends('layouts.adminlte', ['title' => 'Raças'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Raças</h3>
        <div class="card-tools">
            <button onclick="openCreateModal()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Raça</button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        @php
            $speciesLabels = config('species');
        @endphp
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Espécie</th>
                    <th>Raça</th>
                    <th>Porte</th>
                    <th>Peso Médio</th>
                    <th>Expectativa de Vida</th>
                    <th>Temperamento</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($defaults as $d)
                <tr>
                    <td>{{ $speciesLabels[$d->species] ?? $d->species }}</td>
                    <td>{{ $d->breed }}</td>
                    <td>{{ $d->size ?? '-' }}</td>
                    <td>{{ $d->avg_weight_min && $d->avg_weight_max ? $d->avg_weight_min . ' - ' . $d->avg_weight_max . ' kg' : '-' }}</td>
                    <td>{{ $d->avg_lifespan_min && $d->avg_lifespan_max ? $d->avg_lifespan_min . ' - ' . $d->avg_lifespan_max . ' anos' : '-' }}</td>
                    <td>{{ $d->temperament ?? '-' }}</td>
                    <td>{!! $d->is_active ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('breed-defaults.show', $d) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                        <button onclick="openEditModal({{ $d->id }})" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('breed-defaults.destroy', $d) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Confirmar?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Nenhum registro encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="breedDefaultModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breedDefaultModalTitle">Nova Raça</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('breed-default-form', key('breed-default-form'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('close-modal', function() { $('#breedDefaultModal').modal('hide'); });
        Livewire.on('breed-default-saved', function() { location.reload(); });
    });
    function openCreateModal() {
        Livewire.dispatch('resetForm');
        document.getElementById('breedDefaultModalTitle').textContent = 'Nova Raça';
        $('#breedDefaultModal').modal('show');
    }
    function openEditModal(id) {
        Livewire.dispatch('editBreedDefault', { id: id });
        document.getElementById('breedDefaultModalTitle').textContent = 'Editar Raça';
        $('#breedDefaultModal').modal('show');
    }
</script>
@endpush
