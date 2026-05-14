@extends('layouts.adminlte', ['title' => 'Editar Padrão de Raça'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Padrão de Raça</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('breed-defaults.update', $breedDefault) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Espécie *</label>
                        <select name="species" class="form-control" required>
                            <option value="canino" {{ $breedDefault->species == 'canino' ? 'selected' : '' }}>Canino</option>
                            <option value="felino" {{ $breedDefault->species == 'felino' ? 'selected' : '' }}>Felino</option>
                            <option value="outro" {{ $breedDefault->species == 'outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Raça *</label>
                        <input type="text" name="breed" class="form-control" value="{{ $breedDefault->breed }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Porte</label>
                        <select name="size" class="form-control">
                            <option value="">Selecione</option>
                            <option value="pequeno" {{ $breedDefault->size == 'pequeno' ? 'selected' : '' }}>Pequeno</option>
                            <option value="medio" {{ $breedDefault->size == 'medio' ? 'selected' : '' }}>Médio</option>
                            <option value="grande" {{ $breedDefault->size == 'grande' ? 'selected' : '' }}>Grande</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Peso Mín (kg)</label>
                        <input type="number" step="0.01" name="avg_weight_min" class="form-control" value="{{ $breedDefault->avg_weight_min }}" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Peso Máx (kg)</label>
                        <input type="number" step="0.01" name="avg_weight_max" class="form-control" value="{{ $breedDefault->avg_weight_max }}" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vida Mín (anos)</label>
                        <input type="number" name="avg_lifespan_min" class="form-control" value="{{ $breedDefault->avg_lifespan_min }}" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vida Máx (anos)</label>
                        <input type="number" name="avg_lifespan_max" class="form-control" value="{{ $breedDefault->avg_lifespan_max }}" min="0">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Temperamento</label>
                <input type="text" name="temperament" class="form-control" value="{{ $breedDefault->temperament }}" maxlength="255">
            </div>
            <div class="form-group">
                <label>Predisposições</label>
                <textarea name="predispositions" class="form-control" rows="3">{{ $breedDefault->predispositions }}</textarea>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3">{{ $breedDefault->notes }}</textarea>
            </div>
            <div class="form-group">
                <label>Ativo</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $breedDefault->is_active ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{ !$breedDefault->is_active ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>
</div>
@endsection
