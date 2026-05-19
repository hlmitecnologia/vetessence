@extends('layouts.adminlte', ['title' => 'Novo Termo de Consentimento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Termo de Consentimento</h3>
        <div class="card-tools">
            <a href="{{ route('consent-forms.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('consent-forms.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" id="pet_id" :value="old('pet_id')" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} - {{ $pet->tutors->first()->name ?? '' }}
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
                        <label for="tutor_id">Tutor *</label>
                        <x-tom-select name="tutor_id" id="tutor_id" :value="old('tutor_id')" required>
                            @foreach($tutors as $tutor)
                                <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('tutor_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="consent_template_id">Modelo de Termo</label>
                        <x-tom-select name="consent_template_id" id="consent_template_id" :value="old('consent_template_id')">
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('consent_template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ $template->category ?? 'Geral' }})
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('consent_template_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="veterinarian_id">Veterinário *</label>
                        <x-tom-select name="veterinarian_id" id="veterinarian_id" :value="old('veterinarian_id')" required>
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('veterinarian_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('veterinarian_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="client_name">Nome do Tutor (para assinatura)</label>
                        <input type="text" name="client_name" id="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name') }}">
                        @error('client_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="client_document">CPF/RG do Tutor</label>
                        <input type="text" name="client_document" id="client_document" class="form-control @error('client_document') is-invalid @enderror" value="{{ old('client_document') }}">
                        @error('client_document')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="witness_id">Testemunha</label>
                        <x-tom-select name="witness_id" id="witness_id" :value="old('witness_id')">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('witness_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </x-tom-select>
                        @error('witness_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="signed_content">Conteúdo Personalizado</label>
                <textarea name="signed_content" id="signed_content" rows="6" class="form-control @error('signed_content') is-invalid @enderror">{{ old('signed_content') }}</textarea>
                <small class="text-muted">Se um modelo for selecionado, ele será mesclado com este conteúdo.</small>
                @error('signed_content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>

            <hr>
            <h5>Assinatura Digital</h5>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> A assinatura digital será capturada após a criação do termo. Selecione "Rascunho" para criar sem assinar.
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="mark_signed" id="mark_signed" class="custom-control-input" value="1" {{ old('mark_signed') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="mark_signed">Marcar como assinado</label>
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
