@extends('layouts.adminlte', ['title' => 'Editar Substância Controlada'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar - {{ $controlledSubstance->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('controlled-substances.update', $controlledSubstance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome Comercial *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $controlledSubstance->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="active_ingredient">Princípio Ativo</label>
                        <input type="text" name="active_ingredient" id="active_ingredient" class="form-control" value="{{ old('active_ingredient', $controlledSubstance->active_ingredient) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="schedule">Lista/Controle *</label>
                        <select name="schedule" id="schedule" class="form-control" required>
                            @foreach(['A1', 'A2', 'A3', 'B1', 'B2', 'C1', 'D1', 'Outros'] as $sched)
                                <option value="{{ $sched }}" {{ old('schedule', $controlledSubstance->schedule) == $sched ? 'selected' : '' }}>{{ $sched }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="anvisa_register">Registro ANVISA</label>
                        <input type="text" name="anvisa_register" id="anvisa_register" class="form-control" value="{{ old('anvisa_register', $controlledSubstance->anvisa_register) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit">Unidade *</label>
                        <select name="unit" id="unit" class="form-control" required>
                            @foreach(['ml', 'mg', 'g', 'mcg', 'UI', 'comprimido', 'ampola', 'frasco'] as $u)
                                <option value="{{ $u }}" {{ old('unit', $controlledSubstance->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="current_stock">Estoque Atual</label>
                        <input type="number" step="0.01" name="current_stock" id="current_stock" class="form-control" value="{{ old('current_stock', $controlledSubstance->current_stock) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_stock">Estoque Mínimo</label>
                        <input type="number" step="0.01" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', $controlledSubstance->min_stock) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4 pt-3">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $controlledSubstance->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $controlledSubstance->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
