@extends('layouts.adminlte', ['title' => 'Nova Internação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Internação</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalizations.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('hospitalizations.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_id">Veterinário Responsável *</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('vet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admission_date">Data de Admissão *</label>
                        <input type="date" name="admission_date" id="admission_date" class="form-control @error('admission_date') is-invalid @enderror" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                        @error('admission_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admission_time">Horário</label>
                        <input type="time" name="admission_time" id="admission_time" class="form-control @error('admission_time') is-invalid @enderror" value="{{ old('admission_time', date('H:i')) }}">
                        @error('admission_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department">Departamento *</label>
                        <select name="department" id="department" class="form-control @error('department') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="UTI" {{ old('department') == 'UTI' ? 'selected' : '' }}>UTI</option>
                            <option value="Enfermaria" {{ old('department') == 'Enfermaria' ? 'selected' : '' }}>Enfermaria</option>
                            <option value="Isolamento" {{ old('department') == 'Isolamento' ? 'selected' : '' }}>Isolamento</option>
                            <option value="Pré-cirúrgico" {{ old('department') == 'Pré-cirúrgico' ? 'selected' : '' }}>Pré-cirúrgico</option>
                            <option value="Pós-cirúrgico" {{ old('department') == 'Pós-cirúrgico' ? 'selected' : '' }}>Pós-cirúrgico</option>
                        </select>
                        @error('department')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bed">Leito</label>
                        <input type="text" name="bed" id="bed" class="form-control @error('bed') is-invalid @enderror" value="{{ old('bed') }}" placeholder="Ex: A-01">
                        @error('bed')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4 pt-3">
                            <input type="checkbox" name="is_emergency" id="is_emergency" class="custom-control-input" value="1" {{ old('is_emergency') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_emergency">
                                <i class="fas fa-exclamation-triangle text-danger"></i> Emergência
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="admission_reason">Motivo da Internação *</label>
                <textarea name="admission_reason" id="admission_reason" rows="3" class="wysiwyg form-control @error('admission_reason') is-invalid @enderror" required>{{ old('admission_reason') }}</textarea>
                @error('admission_reason')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="initial_diagnosis">Diagnóstico Inicial</label>
                <textarea name="initial_diagnosis" id="initial_diagnosis" rows="2" class="wysiwyg form-control @error('initial_diagnosis') is-invalid @enderror">{{ old('initial_diagnosis') }}</textarea>
                @error('initial_diagnosis')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Internar
            </button>
        </div>
    </form>
</div>
@endsection
