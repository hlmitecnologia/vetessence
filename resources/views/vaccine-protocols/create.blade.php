@extends('layouts.adminlte', ['title' => 'Novo Protocolo'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Protocolo de Vacinação</h3>
        <div class="card-tools">
            <a href="{{ route('vaccine-protocols.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('vaccine-protocols.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="species">Espécie *</label>
                        <select name="species" id="species" class="form-control @error('species') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="canine" {{ old('species') == 'canine' ? 'selected' : '' }}>Canina</option>
                            <option value="feline" {{ old('species') == 'feline' ? 'selected' : '' }}>Felina</option>
                            <option value="equine" {{ old('species') == 'equine' ? 'selected' : '' }}>Equina</option>
                            <option value="bovine" {{ old('species') == 'bovine' ? 'selected' : '' }}>Bovina</option>
                        </select>
                        @error('species')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="vaccine_name">Nome da Vacina *</label>
                        <input type="text" name="vaccine_name" id="vaccine_name" class="form-control @error('vaccine_name') is-invalid @enderror" value="{{ old('vaccine_name') }}" placeholder="Ex: V10, Antirrábica, Giárdia" required>
                        @error('vaccine_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="age_start_weeks">Idade Início (semanas)</label>
                        <input type="number" name="age_start_weeks" id="age_start_weeks" class="form-control" value="{{ old('age_start_weeks') }}" placeholder="Ex: 6">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="age_end_weeks">Idade Fim (semanas)</label>
                        <input type="number" name="age_end_weeks" id="age_end_weeks" class="form-control" value="{{ old('age_end_weeks') }}" placeholder="Ex: 8">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="booster_interval_months">Intervalo Reforço (meses)</label>
                        <input type="number" name="booster_interval_months" id="booster_interval_months" class="form-control" value="{{ old('booster_interval_months') }}" placeholder="Ex: 12">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_initial" id="is_initial" class="custom-control-input" value="1" {{ old('is_initial') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_initial">Série inicial (filhote)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dose_number">Nº da Dose (série inicial)</label>
                        <input type="number" name="dose_number" id="dose_number" class="form-control" value="{{ old('dose_number') }}" placeholder="Ex: 1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_core" id="is_core" class="custom-control-input" value="1" {{ old('is_core', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_core">Vacina essencial</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection
