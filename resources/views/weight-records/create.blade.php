@extends('layouts.adminlte', ['title' => 'Novo Registro de Peso'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Registro de Peso</h3>
        <div class="card-tools">
            <a href="{{ route('weight-records.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('weight-records.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="pet_id">Pet *</label>
                <select name="pet_id" id="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                    <option value="">Selecione um pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id', $selectedPetId ?? '') == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}
                        </option>
                    @endforeach
                </select>
                @error('pet_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="weight">Peso (kg) *</label>
                        <input type="number" step="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}" placeholder="Ex: 25.50" required>
                        @error('weight')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bcs">ECC (Escore de Condição Corporal)</label>
                        <select name="bcs" id="bcs" class="form-control @error('bcs') is-invalid @enderror">
                            <option value="">Selecione</option>
                            @for($i = 1; $i <= 9; $i++)
                                <option value="{{ $i }}" {{ old('bcs') == $i ? 'selected' : '' }}>{{ $i }}/9</option>
                            @endfor
                        </select>
                        @error('bcs')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="measurement_date">Data da Medição *</label>
                        <input type="date" name="measurement_date" id="measurement_date" class="form-control @error('measurement_date') is-invalid @enderror" value="{{ old('measurement_date', date('Y-m-d')) }}" required>
                        @error('measurement_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
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
