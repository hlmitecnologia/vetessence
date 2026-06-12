@extends('layouts.adminlte', ['title' => 'Novo Encaminhamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Encaminhamento</h3>
        <div class="card-tools">
            <a href="{{ route('referrals.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('referrals.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('pet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="referring_vet_id">Veterinário de Origem</label>
                        <x-tom-select name="referring_vet_id" id="referring_vet_id" :value="old('referring_vet_id')">
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('referring_vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="referring_clinic">Clínica de Origem</label>
                        <input type="text" name="referring_clinic" id="referring_clinic" class="form-control" value="{{ old('referring_clinic', 'VetEssence') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiving_vet_id">Veterinário de Destino *</label>
                        <x-tom-select name="receiving_vet_id" id="receiving_vet_id" :value="old('receiving_vet_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('receiving_vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('receiving_vet_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="receiving_clinic">Clínica de Destino *</label>
                        <input type="text" name="receiving_clinic" id="receiving_clinic" class="form-control @error('receiving_clinic') is-invalid @enderror" value="{{ old('receiving_clinic') }}" required>
                        @error('receiving_clinic')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(['sent' => 'Enviado', 'received' => 'Recebido'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="reason">Motivo do Encaminhamento *</label>
                <textarea name="reason" id="reason" rows="3" class="wysiwyg form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="clinical_history">Histórico Clínico</label>
                <textarea name="clinical_history" id="clinical_history" rows="4" class="wysiwyg form-control">{{ old('clinical_history') }}</textarea>
            </div>
            <div class="form-group">
                <label for="requested_procedures">Procedimentos Solicitados</label>
                <textarea name="requested_procedures" id="requested_procedures" rows="3" class="wysiwyg form-control">{{ old('requested_procedures') }}</textarea>
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
