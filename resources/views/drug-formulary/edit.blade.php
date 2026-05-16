@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-edit"></i> Editar Farmaco</h4>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('drug-formulary.update', $drugFormulary) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group"><label>Farmaco</label><input type="text" name="drug" class="form-control" value="{{ $drugFormulary->drug }}" required></div>
                <div class="form-group"><label>Especie</label><input type="text" name="species" class="form-control" value="{{ $drugFormulary->species }}" required></div>
                <div class="form-group"><label>Dosagem (mg/kg)</label><input type="number" name="dosage_mg_kg" class="form-control" step="0.01" value="{{ $drugFormulary->dosage_mg_kg }}" required></div>
                <div class="form-group"><label>Dose Maxima (mg)</label><input type="number" name="max_dose" class="form-control" step="0.01" value="{{ $drugFormulary->max_dose }}"></div>
                <div class="form-group"><label>Via</label><input type="text" name="route" class="form-control" value="{{ $drugFormulary->route }}"></div>
                <div class="form-group"><label>Observacoes</label><textarea name="notes" class="form-control" rows="2">{{ $drugFormulary->notes }}</textarea></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" {{ $drugFormulary->is_active ? 'checked' : '' }} class="form-check-input" id="active"><label class="form-check-label" for="active">Ativo</label></div>
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="{{ route('drug-formulary.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
