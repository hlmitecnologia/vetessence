@extends('layouts.adminlte', ['title' => 'Nova Prescrição'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Prescrição</h3>
        <div class="card-tools">
            <a href="{{ route('prescriptions.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('prescriptions.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="medical_record_id">Prontuário *</label>
                <select name="medical_record_id" id="medical_record_id" class="form-control @error('medical_record_id') is-invalid @enderror" required>
                    <option value="">Selecione um prontuário</option>
                    @foreach($medicalRecords as $record)
                        <option value="{{ $record->id }}" {{ old('medical_record_id') == $record->id ? 'selected' : '' }}>
                            {{ $record->pet->name ?? 'Pet' }} - {{ $record->date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
                @error('medical_record_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="medication">Medicamento *</label>
                <input type="text" name="medication" id="medication" class="form-control @error('medication') is-invalid @enderror" value="{{ old('medication') }}" required>
                @error('medication')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dosage">Dosagem *</label>
                        <input type="text" name="dosage" id="dosage" class="form-control @error('dosage') is-invalid @enderror" value="{{ old('dosage') }}" placeholder="Ex: 10mg" required>
                        @error('dosage')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="frequency">Frequência *</label>
                        <input type="text" name="frequency" id="frequency" class="form-control @error('frequency') is-invalid @enderror" value="{{ old('frequency') }}" placeholder="Ex: 2x ao dia" required>
                        @error('frequency')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="duration">Duração *</label>
                        <input type="text" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" placeholder="Ex: 7 dias" required>
                        @error('duration')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="instructions">Instruções</label>
                <textarea name="instructions" id="instructions" class="form-control" rows="3">{{ old('instructions') }}</textarea>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection