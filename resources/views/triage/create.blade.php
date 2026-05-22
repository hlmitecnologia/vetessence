@extends('layouts.adminlte', ['title' => 'Nova Triagem'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('triage.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pet *</label>
                        <div class="input-group">
                            <x-tom-select name="pet_id" :value="old('pet_id')" required>
                                @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} @if($pet->tutors->first()) - {{ $pet->tutors->first()->name }} @endif
                                </option>
                                @endforeach
                            </x-tom-select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" onclick="openNewPetModal()" title="Novo Pet">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Severidade *</label>
                        <select name="severity" required class="form-control">
                            <option value="">Selecione...</option>
                            <option value="green" {{ old('severity') == 'green' ? 'selected' : '' }}>Verde - Não urgente</option>
                            <option value="yellow" {{ old('severity') == 'yellow' ? 'selected' : '' }}>Amarelo - Prioritário</option>
                            <option value="orange" {{ old('severity') == 'orange' ? 'selected' : '' }}>Laranja - Urgência</option>
                            <option value="red" {{ old('severity') == 'red' ? 'selected' : '' }}>Vermelho - Emergência</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Queixa Principal *</label>
                        <textarea name="chief_complaint" rows="3" class="form-control" required>{{ old('chief_complaint') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Veterinário Responsável</label>
                        <x-tom-select name="assigned_vet_id" :value="old('assigned_vet_id')">
                            <option value="">Selecione...</option>
                            @foreach($veterinarians ?? [] as $vet)
                            <option value="{{ $vet->id }}" {{ old('assigned_vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sinais Vitais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Temperatura (ºC)</label>
                                <input type="text" name="vs_temperature" value="{{ old('vs_temperature') }}" class="form-control" placeholder="38.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Frequência Cardíaca (bpm)</label>
                                <input type="text" name="vs_heart_rate" value="{{ old('vs_heart_rate') }}" class="form-control" placeholder="120">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Frequência Respiratória (mrm)</label>
                                <input type="text" name="vs_respiratory_rate" value="{{ old('vs_respiratory_rate') }}" class="form-control" placeholder="30">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="text" name="vs_weight" value="{{ old('vs_weight') }}" class="form-control" placeholder="10.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Mucosas</label>
                                <input type="text" name="vs_mucosa" value="{{ old('vs_mucosa') }}" class="form-control" placeholder="Normocoradas">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hidratação</label>
                                <input type="text" name="vs_hydration" value="{{ old('vs_hydration') }}" class="form-control" placeholder="Normal">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Linfonodos</label>
                                <input type="text" name="vs_lymph_nodes" value="{{ old('vs_lymph_nodes') }}" class="form-control" placeholder="Normais">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('triage.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Registrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="petModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Pet</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @livewire('pet-form', key('pet-form-triage'))
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
function openNewPetModal() {
    $('#petModal').modal('show');
}
@endpush
