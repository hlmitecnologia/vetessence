@extends('layouts.adminlte', ['title' => 'Nova Teleconsulta'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Teleconsulta</h3>
        <div class="card-tools">
            <a href="{{ route('teleconsultations.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('teleconsultations.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="room_name">Nome da Sala *</label>
                        <input type="text" name="room_name" class="form-control @error('room_name') is-invalid @enderror" value="{{ old('room_name') }}" required>
                        @error('room_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="provider">Provedor *</label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="jitsi" {{ old('provider', 'jitsi') == 'jitsi' ? 'selected' : '' }}>Jitsi Meet</option>
                            <option value="zoom" {{ old('provider') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                            <option value="google_meet" {{ old('provider') == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                            <option value="other" {{ old('provider') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="scheduled_at">Agendado para *</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}" required>
                        @error('scheduled_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet *</label>
                        <select name="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                        @error('pet_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vet_id">Veterinário *</label>
                        <select name="vet_id" class="form-control @error('vet_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($vets as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </select>
                        @error('vet_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tutor_id">Tutor (usuário do sistema)</label>
                        <select name="tutor_id" class="form-control">
                            <option value="">Selecione</option>
                            @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}" {{ old('tutor_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Agendar</button>
        </div>
    </form>
</div>
@endsection
