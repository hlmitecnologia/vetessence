@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-plus-circle"></i> Novo Farmaco</h4>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('drug-formulary.store') }}" method="POST">
                @csrf
                <div class="form-group"><label>Farmaco</label><input type="text" name="drug" class="form-control" required></div>
                <div class="form-group"><label>Especie</label><input type="text" name="species" class="form-control" required></div>
                <div class="form-group"><label>Dosagem (mg/kg)</label><input type="number" name="dosage_mg_kg" class="form-control" step="0.01" required></div>
                <div class="form-group"><label>Dose Maxima (mg)</label><input type="number" name="max_dose" class="form-control" step="0.01"></div>
                <div class="form-group"><label>Via</label><input type="text" name="route" class="form-control"></div>
                <div class="form-group"><label>Observacoes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="active"><label class="form-check-label" for="active">Ativo</label></div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('drug-formulary.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
