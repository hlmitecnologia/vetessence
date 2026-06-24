@extends('layouts.adminlte', ['title' => 'Editar Certificado'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Certificado {{ $healthCertificate->certificate_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('health-certificates.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('health-certificates.update', $healthCertificate) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id', $healthCertificate->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $healthCertificate->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="international" {{ old('type', $healthCertificate->type) == 'international' ? 'selected' : '' }}>Internacional</option>
                            <option value="domestic" {{ old('type', $healthCertificate->type) == 'domestic' ? 'selected' : '' }}>Nacional</option>
                            <option value="boarding" {{ old('type', $healthCertificate->type) == 'boarding' ? 'selected' : '' }}>Hospedagem</option>
                            <option value="exhibition" {{ old('type', $healthCertificate->type) == 'exhibition' ? 'selected' : '' }}>Exposição</option>
                            <option value="other" {{ old('type', $healthCertificate->type) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="destination">Destino</label>
                        <input type="text" name="destination" id="destination" class="form-control" value="{{ old('destination', $healthCertificate->destination) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="issue_date">Data Emissão *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ old('issue_date', $healthCertificate->issue_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="expiration_date">Validade</label>
                        <input type="date" name="expiration_date" id="expiration_date" class="form-control" value="{{ old('expiration_date', $healthCertificate->expiration_date ? $healthCertificate->expiration_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="issuer_vet_id">Veterinário Emissor *</label>
                        <x-tom-select name="issuer_vet_id" id="issuer_vet_id" :value="old('issuer_vet_id', $healthCertificate->issuer_vet_id)" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('issuer_vet_id', $healthCertificate->issuer_vet_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" {{ old('status', $healthCertificate->status) == 'draft' ? 'selected' : '' }}>Rascunho</option>
                            <option value="issued" {{ old('status', $healthCertificate->status) == 'issued' ? 'selected' : '' }}>Emitido</option>
                            <option value="expired" {{ old('status', $healthCertificate->status) == 'expired' ? 'selected' : '' }}>Vencido</option>
                            <option value="cancelled" {{ old('status', $healthCertificate->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="custom-control custom-checkbox mt-4">
                        <input type="checkbox" name="is_export" id="is_export" class="custom-control-input" value="1" {{ old('is_export', $healthCertificate->is_export) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_export">Exportação/CITES</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="clinical_notes">Observações Clínicas</label>
                <textarea name="clinical_notes" id="clinical_notes" rows="3" class="wysiwyg form-control @error('clinical_notes') is-invalid @enderror">{{ old('clinical_notes', $healthCertificate->clinical_notes) }}</textarea>
                            @error('clinical_notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="notes">Informações Adicionais</label>
                <textarea name="notes" id="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes', $healthCertificate->notes) }}</textarea>
                            @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection
