@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-plus-circle"></i> Novo Protocolo de Emergencia</h4>
    <div class="card"><div class="card-body">
        <form action="{{ route('emergency-protocols.store') }}" method="POST">
            @csrf
            <div class="form-group"><label>Titulo</label><input type="text" name="title" class="form-control" required></div>
            <div class="row">
                <div class="col-md-4"><label>Especie</label><input type="text" name="species" class="form-control"></div>
                <div class="col-md-4"><label>Gravidade</label>
                    <select name="severity" class="form-control" required>
                        <option value="critical">Critico</option>
                        <option value="urgent" selected>Urgente</option>
                        <option value="stable">Estavel</option>
                    </select>
                </div>
                <div class="col-md-4"><label>Categoria</label><input type="text" name="category" class="form-control"></div>
            </div>
            <div class="form-group"><label>Descricao</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="form-group"><label>Procedimento (passo a passo)</label><textarea name="procedure_steps" class="form-control" rows="5" required></textarea></div>
            <div class="form-group"><label>Medicamentos</label><input type="text" name="medications" class="form-control" placeholder="Lista de medicamentos"></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="active"><label class="form-check-label" for="active">Ativo</label></div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('emergency-protocols.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div></div>
</div>
@endsection
