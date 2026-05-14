@extends('layouts.adminlte', ['title' => 'Editar Registro de Óbito'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Registro de Óbito</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('pet-death-records.update', $petDeathRecord) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Pet</label>
                <select name="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}" {{ $petDeathRecord->pet_id == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Data do Óbito</label>
                <input type="date" name="death_date" class="form-control" value="{{ $petDeathRecord->death_date->format('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label>Causa</label>
                <input type="text" name="cause" class="form-control" value="{{ $petDeathRecord->cause }}">
            </div>
            <div class="form-group">
                <label>Veterinário</label>
                <input type="text" name="attending_vet" class="form-control" value="{{ $petDeathRecord->attending_vet }}">
            </div>
            <div class="form-group">
                <label>Destinação</label>
                <select name="disposition" class="form-control">
                    <option value="">Selecione</option>
                    <option value="cremation" {{ $petDeathRecord->disposition == 'cremation' ? 'selected' : '' }}>Cremação</option>
                    <option value="burial" {{ $petDeathRecord->disposition == 'burial' ? 'selected' : '' }}>Sepultamento</option>
                    <option value="collected_by_tutor" {{ $petDeathRecord->disposition == 'collected_by_tutor' ? 'selected' : '' }}>Recolhido pelo tutor</option>
                    <option value="other" {{ $petDeathRecord->disposition == 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3">{{ $petDeathRecord->notes }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
