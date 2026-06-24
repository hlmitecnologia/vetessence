@extends('layouts.adminlte', ['title' => 'Novo Certificado Sanitário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Emitir Certificado Sanitário</h3>
        <div class="card-tools">
            <a href="{{ route('health-certificates.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('health-certificates.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="international" {{ old('type') == 'international' ? 'selected' : '' }}>Internacional</option>
                            <option value="domestic" {{ old('type') == 'domestic' ? 'selected' : '' }}>Nacional</option>
                            <option value="boarding" {{ old('type') == 'boarding' ? 'selected' : '' }}>Hospedagem</option>
                            <option value="exhibition" {{ old('type') == 'exhibition' ? 'selected' : '' }}>Exposição</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="destination">Destino</label>
                        <input type="text" name="destination" id="destination" class="form-control" value="{{ old('destination') }}" placeholder="País/Cidade">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="issue_date">Data Emissão *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="expiration_date">Validade</label>
                        <input type="date" name="expiration_date" id="expiration_date" class="form-control" value="{{ old('expiration_date') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="issuer_vet_id">Veterinário Emissor *</label>
                        <x-tom-select name="issuer_vet_id" id="issuer_vet_id" :value="old('issuer_vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('issuer_vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Emitido</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="custom-control custom-checkbox mt-4">
                        <input type="checkbox" name="is_export" id="is_export" class="custom-control-input" value="1" {{ old('is_export') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_export">Exportação/CITES</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="clinical_notes">Observações Clínicas</label>
                <textarea name="clinical_notes" id="clinical_notes" rows="3" class="wysiwyg form-control @error('clinical_notes') is-invalid @enderror" placeholder="Vacinas, exames, condições clínicas relevantes...">{{ old('clinical_notes') }}</textarea>
                            @error('clinical_notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="notes">Informações Adicionais</label>
                <textarea name="notes" id="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Emitir Certificado</button>
        </div>
    </form>
</div>
@endsection
