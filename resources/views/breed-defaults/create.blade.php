@extends('layouts.adminlte', ['title' => 'Novo Padrão de Raça'])

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Novo Padrão de Raça</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('breed-defaults.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Espécie *</label>
                        <select name="species" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="canino">Canino</option>
                            <option value="felino">Felino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Raça *</label>
                        <input type="text" name="breed" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Porte</label>
                        <select name="size" class="form-control">
                            <option value="">Selecione</option>
                            <option value="pequeno">Pequeno</option>
                            <option value="medio">Médio</option>
                            <option value="grande">Grande</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Peso Mín (kg)</label>
                        <input type="number" step="0.01" name="avg_weight_min" class="form-control" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Peso Máx (kg)</label>
                        <input type="number" step="0.01" name="avg_weight_max" class="form-control" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vida Mín (anos)</label>
                        <input type="number" name="avg_lifespan_min" class="form-control" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vida Máx (anos)</label>
                        <input type="number" name="avg_lifespan_max" class="form-control" min="0">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Temperamento</label>
                <input type="text" name="temperament" class="form-control" maxlength="255">
            </div>
            <div class="form-group">
                <label>Predisposições</label>
                <textarea name="predispositions" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Ativo</label>
                <select name="is_active" class="form-control">
                    <option value="1">Sim</option>
                    <option value="0">Não</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection
