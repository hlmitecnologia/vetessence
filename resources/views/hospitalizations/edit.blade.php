@extends('layouts.adminlte', ['title' => 'Editar Internação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Internação - {{ $hospitalization->pet->name ?? '' }}</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalizations.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('hospitalizations.update', $hospitalization) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id', $hospitalization->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $hospitalization->pet_id) == $pet->id ? 'selected' : '' }}>
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
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id', $hospitalization->vet_id)" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id', $hospitalization->vet_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
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
                        <input type="date" name="admission_date" id="admission_date" class="form-control @error('admission_date') is-invalid @enderror" value="{{ old('admission_date', $hospitalization->admission_date->format('Y-m-d')) }}" required>
                        @error('admission_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admission_time">Horário</label>
                        <input type="time" name="admission_time" id="admission_time" class="form-control @error('admission_time') is-invalid @enderror" value="{{ old('admission_time', $hospitalization->admission_time) }}">
                        @error('admission_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="department">Departamento *</label>
                        <select name="department" id="department" class="form-control @error('department') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['UTI', 'Enfermaria', 'Isolamento', 'Pré-cirúrgico', 'Pós-cirúrgico'] as $dept)
                                <option value="{{ $dept }}" {{ old('department', $hospitalization->department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                        @error('department')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bed">Leito</label>
                        <input type="text" name="bed" id="bed" class="form-control @error('bed') is-invalid @enderror" value="{{ old('bed', $hospitalization->bed) }}">
                        @error('bed')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach(['admitted' => 'Internado', 'discharged' => 'Alta', 'transferred' => 'Transferido'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $hospitalization->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" name="is_emergency" id="is_emergency" class="custom-control-input" value="1" {{ old('is_emergency', $hospitalization->is_emergency) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_emergency">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Emergência
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="admission_reason">Motivo da Internação *</label>
                <textarea name="admission_reason" id="admission_reason" rows="3" class="wysiwyg form-control @error('admission_reason') is-invalid @enderror" required>{{ old('admission_reason', $hospitalization->admission_reason) }}</textarea>
                @error('admission_reason')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="initial_diagnosis">Diagnóstico Inicial</label>
                <textarea name="initial_diagnosis" id="initial_diagnosis" rows="2" class="wysiwyg form-control @error('initial_diagnosis') is-invalid @enderror">{{ old('initial_diagnosis', $hospitalization->initial_diagnosis) }}</textarea>
                @error('initial_diagnosis')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            @if($hospitalization->status === 'discharged')
            <hr>
            <h5>Informações de Alta</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="discharged_at">Data da Alta</label>
                        <input type="date" name="discharged_at" id="discharged_at" class="form-control" value="{{ old('discharged_at', $hospitalization->discharged_at ? $hospitalization->discharged_at->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="discharge_summary">Resumo de Alta</label>
                <textarea name="discharge_summary" id="discharge_summary" rows="3" class="wysiwyg form-control">{{ old('discharge_summary', $hospitalization->discharge_summary) }}</textarea>
            </div>
            <div class="form-group">
                <label for="discharge_instructions">Instruções de Alta</label>
                <textarea name="discharge_instructions" id="discharge_instructions" rows="3" class="wysiwyg form-control">{{ old('discharge_instructions', $hospitalization->discharge_instructions) }}</textarea>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
