@extends('layouts.adminlte', ['title' => 'Novo Registro de Óbito'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Novo Registro de Óbito</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('pet-death-records.store') }}">
            @csrf
            <div class="form-group">
                <label>Pet</label>
                <select name="pet_id" class="form-control @error('pet_id') is-invalid @enderror" required>
                    <option value="">Selecione</option>
                    @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }} - {{ $pet->species }} ({{ $pet->tutors->pluck('name')->join(', ') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Data do Óbito</label>
                <input type="date" name="death_date" class="form-control @error('death_date') is-invalid @enderror" required>
            </div>
            <div class="form-group">
                <label>Causa</label>
                <input type="text" name="cause" class="form-control" maxlength="255">
            </div>
            <div class="form-group">
                <label>Veterinário Responsável</label>
                <input type="text" name="attending_vet" class="form-control" maxlength="255">
            </div>
            <div class="form-group">
                <label>Destinação</label>
                <select name="disposition" class="form-control">
                    <option value="">Selecione</option>
                    <option value="cremation">Cremação</option>
                    <option value="burial">Sepultamento</option>
                    <option value="collected_by_tutor">Recolhido pelo tutor</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection
