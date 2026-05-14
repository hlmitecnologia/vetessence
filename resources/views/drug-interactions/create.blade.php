@extends('layouts.adminlte', ['title' => 'Nova Interação Medicamentosa'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Interação Medicamentosa</h3>
        <div class="card-tools">
            <a href="{{ route('drug-interactions.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('drug-interactions.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="drug_a">Medicamento A *</label>
                        <input type="text" name="drug_a" id="drug_a" class="form-control @error('drug_a') is-invalid @enderror" value="{{ old('drug_a') }}" required placeholder="Ex: Cetoprofeno">
                        @error('drug_a')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="drug_b">Medicamento B *</label>
                        <input type="text" name="drug_b" id="drug_b" class="form-control @error('drug_b') is-invalid @enderror" value="{{ old('drug_b') }}" required placeholder="Ex: Meloxicam">
                        @error('drug_b')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="severity">Severidade *</label>
                        <select name="severity" id="severity" class="form-control @error('severity') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="contraindicated" {{ old('severity') == 'contraindicated' ? 'selected' : '' }}>Contraindicada</option>
                            <option value="caution" {{ old('severity') == 'caution' ? 'selected' : '' }}>Precaução</option>
                            <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>Menor</option>
                        </select>
                        @error('severity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror">
                            <option value="">Selecione</option>
                            @foreach(['AINE', 'Antibiótico', 'Antifúngico', 'Antiparasitário', 'Anticonvulsivante', 'Cardiovascular', 'Hormonal', 'Anestésico', 'Sedativo', 'Quimioterápico', 'Outros'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="source">Fonte</label>
                        <input type="text" name="source" id="source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source') }}" placeholder="Ex: MSD, PubMed">
                        @error('source')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição da Interação *</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="mechanism">Mecanismo</label>
                <input type="text" name="mechanism" id="mechanism" class="form-control @error('mechanism') is-invalid @enderror" value="{{ old('mechanism') }}" placeholder="Ex: Inibição competitiva da COX">
                @error('mechanism')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="management">Conduta / Manejo</label>
                <textarea name="management" id="management" rows="2" class="form-control @error('management') is-invalid @enderror">{{ old('management') }}</textarea>
                @error('management')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Ativo</label>
                </div>
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
