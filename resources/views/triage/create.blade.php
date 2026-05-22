@extends('layouts.adminlte', ['title' => 'Nova Triagem'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <form action="{{ route('triage.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pet *</label>
                        <x-tom-select name="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} @if($pet->tutors->first()) - {{ $pet->tutors->first()->name }} @endif
                            </option>
                            @endforeach
                        </x-tom-select>
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
                    <div class="form-group">
                        <label>Sinais Vitais (JSON opcional)</label>
                        <textarea name="vital_signs" rows="2" class="form-control font-monospace small" placeholder='{"temperatura": "38.5", "freq_cardiaca": "120", "freq_respiratoria": "30" }'>{{ old('vital_signs') }}</textarea>
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
