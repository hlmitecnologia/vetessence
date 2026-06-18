@extends('layouts.adminlte', ['title' => 'Editar Teleconsulta'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Teleconsulta - {{ $teleconsultation->room_name }}</h3>
        <div class="card-tools">
            <a href="{{ route('teleconsultations.show', $teleconsultation) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('teleconsultations.update', $teleconsultation) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="room_name">Nome da Sala *</label>
                        <input type="text" name="room_name" class="form-control @error('room_name') is-invalid @enderror" value="{{ old('room_name', $teleconsultation->room_name) }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="provider">Provedor</label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="jitsi" {{ old('provider', $teleconsultation->provider) == 'jitsi' ? 'selected' : '' }}>Jitsi Meet</option>
                            <option value="zoom" {{ old('provider', $teleconsultation->provider) == 'zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="google_meet" {{ old('provider', $teleconsultation->provider) == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                            <option value="other" {{ old('provider', $teleconsultation->provider) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="scheduled_at">Agendado para</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at', optional($teleconsultation->scheduled_at)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <x-tom-select name="pet_id" :value="old('pet_id', $teleconsultation->pet_id)" required>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id', $teleconsultation->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vet_id">Veterinário *</label>
                        <x-tom-select name="vet_id" :value="old('vet_id', $teleconsultation->vet_id)" required>
                            @foreach($vets as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id', $teleconsultation->vet_id) == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tutor_id">Tutor</label>
                        <x-tom-select name="tutor_id" :value="old('tutor_id', $teleconsultation->tutor_id)">
                            @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ old('tutor_id', $teleconsultation->tutor_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes', $teleconsultation->notes) }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection
