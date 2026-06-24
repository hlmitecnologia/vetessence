@extends('layouts.adminlte', ['title' => 'Editar Sessão de Terapia'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Sessão de Terapia</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('therapy-sessions.update', $therapySession) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Pet *</label>
                <x-tom-select name="pet_id" :value="old('pet_id', $therapySession->pet_id)" required>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ $therapySession->pet_id == $pet->id ? 'selected' : '' }}>{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div class="form-group">
                <label>Tipo *</label>
                <select name="type" class="form-control" required>
                    <option value="physiotherapy" {{ $therapySession->type == 'physiotherapy' ? 'selected' : '' }}>Fisioterapia</option>
                    <option value="hydrotherapy" {{ $therapySession->type == 'hydrotherapy' ? 'selected' : '' }}>Hidroterapia</option>
                    <option value="acupuncture" {{ $therapySession->type == 'acupuncture' ? 'selected' : '' }}>Acupuntura</option>
                    <option value="laser" {{ $therapySession->type == 'laser' ? 'selected' : '' }}>Laserterapia</option>
                    <option value="massage" {{ $therapySession->type == 'massage' ? 'selected' : '' }}>Massagem</option>
                    <option value="other" {{ $therapySession->type == 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Data/Hora *</label>
                <input type="datetime-local" name="session_date" class="form-control" value="{{ $therapySession->session_date->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="form-group">
                <label>Terapeuta</label>
                <x-tom-select name="therapist_id" :value="old('therapist_id', $therapySession->therapist_id)">
                    @foreach($therapists as $t)
                    <option value="{{ $t->id }}" {{ $therapySession->therapist_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Duração (min)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="{{ $therapySession->duration_minutes }}" min="1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="scheduled" {{ $therapySession->status == 'scheduled' ? 'selected' : '' }}>Agendada</option>
                            <option value="completed" {{ $therapySession->status == 'completed' ? 'selected' : '' }}>Concluída</option>
                            <option value="cancelled" {{ $therapySession->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Anotações</label>
                <textarea name="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="3">{!! $therapySession->notes !!}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observations" class="wysiwyg form-control @error('observations') is-invalid @enderror" rows="3">{!! $therapySession->observations !!}</textarea>
                @error('observations')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
