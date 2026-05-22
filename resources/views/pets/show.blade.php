@extends('layouts.adminlte', ['title' => $pet->name])

@section('content')
<div class="mb-3">
    <a href="{{ route('pets.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar para lista
    </a>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($pet->photo_url)
                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle mx-auto mb-3" style="width: 128px; height: 128px; object-fit: cover;">
                @else
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 96px; height: 96px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white);">
                    <i class="fas fa-paw" style="font-size: 2.5rem; color: var(--brand-primary, #455e36);"></i>
                </div>
                @endif
                <h5 class="font-weight-bold">{{ $pet->name }}</h5>
                <p class="text-muted">
                    @php
                        $speciesLabels = ['canine' => 'Canino', 'feline' => 'Felino', 'avian' => 'Ave', 'exotic' => 'Exótico'];
                    @endphp
                    {{ $speciesLabels[$pet->species] ?? $pet->species }} - {{ $pet->breed ?? 'SRD' }}
                </p>

                <hr>

                <div class="text-left small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Gênero:</span>
                        <span>{{ $pet->gender === 'male' ? 'Macho' : 'Fêmea' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Idade:</span>
                        <span>{{ $pet->age ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Peso:</span>
                        <span>{{ $pet->weight ? $pet->weight . ' kg' : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Porte:</span>
                        <span>{{ ucfirst($pet->size ?? '-') }}</span>
                    </div>
                    @if($pet->microchip)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Microchip:</span>
                        <span>{{ $pet->microchip }}</span>
                    </div>
                    @endif
                    @if($pet->microchip_date)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Data Microchip:</span>
                        <span>{{ $pet->microchip_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($pet->rg_number)
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">RG Animal:</span>
                        <span>{{ $pet->rg_number }} @if($pet->rg_issuer)({{ $pet->rg_issuer }})@endif</span>
                    </div>
                    @endif
                </div>

                <hr>

                <button onclick="openEditModal({{ $pet->id }})" class="btn btn-primary btn-block">
                    <i class="fas fa-edit mr-1"></i> Editar
                </button>
                <a href="{{ route('pets.timeline', $pet) }}" class="btn btn-secondary-custom btn-block mt-2">
                    <i class="fas fa-history mr-1"></i> Timeline
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Tutores</h5>
            </div>
            <div class="card-body p-0">
                @forelse($pet->tutors as $tutor)
                <a href="{{ route('tutors.show', $tutor) }}" class="text-decoration-none">
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mr-3" style="width: 40px; height: 40px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white); color: var(--brand-primary, #455e36); font-weight: bold;">
                            {{ substr($tutor->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark">{{ $tutor->name }}</div>
                            <small class="text-muted">{{ $tutor->pivot->is_primary ? 'Titular' : 'Secundário' }}</small>
                        </div>
                    </div>
                </a>
                @empty
                <p class="text-muted text-center mb-0 py-3">Nenhum tutor vinculado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-medical mr-2"></i>Últimos Atendimentos</h5>
                <a href="{{ route('medical-records.create') }}?pet_id={{ $pet->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Novo
                </a>
            </div>
            <div class="card-body p-0">
                @if($pet->medicalRecords->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($pet->medicalRecords->take(5) as $record)
                    <a href="{{ route('medical-records.show', $record) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mr-3" style="width: 40px; height: 40px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white); color: var(--brand-primary, #455e36);">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="flex-fill">
                            <div class="font-weight-bold">{{ $record->date->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ Str::limit($record->diagnosis, 50) ?: 'Sem diagnóstico' }}</small>
                        </div>
                        <small class="text-muted">{{ $record->vet->name ?? '-' }}</small>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0 py-4">Nenhum atendimento registrado.</p>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-syringe mr-2"></i>Vacinas</h5>
                <a href="{{ route('vaccinations.create') }}?pet_id={{ $pet->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Nova
                </a>
            </div>
            <div class="card-body p-0">
                @if($pet->vaccinations->count() > 0)
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Vacina</th>
                            <th>Data</th>
                            <th>Próxima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pet->vaccinations as $vaccination)
                        <tr>
                            <td>{{ $vaccination->vaccine }}</td>
                            <td>{{ $vaccination->date->format('d/m/Y') }}</td>
                            <td>{{ $vaccination->next_date ? $vaccination->next_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center mb-0 py-4">Nenhuma vacina registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="petModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="petModalTitle">Editar Pet</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('pet-form', key('pet-form-show'))
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
document.addEventListener('livewire:initialized', function() {
    Livewire.on('close-modal', function() { $('#petModal').modal('hide'); });
    Livewire.on('pet-saved', function() { location.reload(); });
});
function openEditModal(id) {
    Livewire.dispatch('editPet', { id: id });
    document.getElementById('petModalTitle').textContent = 'Editar Pet';
    $('#petModal').modal('show');
}
@endpush
