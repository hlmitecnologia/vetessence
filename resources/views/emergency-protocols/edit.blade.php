@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-edit"></i> Editar Protocolo de Emergencia</h4>
    <div class="card"><div class="card-body">
        <form action="{{ route('emergency-protocols.update', $emergencyProtocol) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group"><label>Titulo</label><input type="text" name="title" class="form-control" value="{{ $emergencyProtocol->title }}" required></div>
            <div class="row">
                <div class="col-md-4"><label>Especie</label><input type="text" name="species" class="form-control" value="{{ $emergencyProtocol->species }}"></div>
                <div class="col-md-4"><label>Gravidade</label>
                    <select name="severity" class="form-control" required>
                        <option value="critical" {{ $emergencyProtocol->severity == 'critical' ? 'selected' : '' }}>Critico</option>
                        <option value="urgent" {{ $emergencyProtocol->severity == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        <option value="stable" {{ $emergencyProtocol->severity == 'stable' ? 'selected' : '' }}>Estavel</option>
                    </select>
                </div>
                <div class="col-md-4"><label>Categoria</label><input type="text" name="category" class="form-control" value="{{ $emergencyProtocol->category }}"></div>
            </div>
            <div class="form-group"><label>Descricao</label><textarea name="description" class="form-control" rows="2">{{ $emergencyProtocol->description }}</textarea></div>
            <div class="form-group"><label>Procedimento (passo a passo)</label><textarea name="procedure_steps" class="form-control" rows="5" required>{{ $emergencyProtocol->procedure_steps }}</textarea></div>
            <div class="form-group"><label>Medicamentos</label><input type="text" name="medications" class="form-control" value="{{ $emergencyProtocol->medications }}"></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" {{ $emergencyProtocol->is_active ? 'checked' : '' }} class="form-check-input" id="active"><label class="form-check-label" for="active">Ativo</label></div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('emergency-protocols.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div></div>
</div>
@endsection
