@extends('layouts.adminlte', ['title' => 'Nova Sessão de Terapia'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Nova Sessão de Terapia</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('therapy-sessions.store') }}">
            @csrf
            <div class="form-group">
                <label>Pet *</label>
                <x-tom-select name="pet_id" :value="old('pet_id')" required>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->tutors->first()->name ?? 'Sem tutor' }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div class="form-group">
                <label>Tipo *</label>
                <select name="type" class="form-control" required>
                    <option value="">Selecione</option>
                    <option value="physiotherapy">Fisioterapia</option>
                    <option value="hydrotherapy">Hidroterapia</option>
                    <option value="acupuncture">Acupuntura</option>
                    <option value="laser">Laserterapia</option>
                    <option value="massage">Massagem</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Data/Hora *</label>
                <input type="datetime-local" name="session_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Terapeuta</label>
                <x-tom-select name="therapist_id" :value="old('therapist_id')">
                    @foreach($therapists as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </x-tom-select>
            </div>
            <div class="form-group">
                <label>Duração (minutos)</label>
                <input type="number" name="duration_minutes" class="form-control" min="1">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="scheduled">Agendada</option>
                    <option value="completed">Concluída</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="3"></textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection
