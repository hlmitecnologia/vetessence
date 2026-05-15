@extends('layouts.adminlte', ['title' => 'Nova Substância Controlada'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Substância Controlada</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('controlled-substances.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome Comercial *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="active_ingredient">Princípio Ativo</label>
                        <input type="text" name="active_ingredient" id="active_ingredient" class="form-control @error('active_ingredient') is-invalid @enderror" value="{{ old('active_ingredient') }}">
                        @error('active_ingredient')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="schedule">Lista/Controle *</label>
                        <select name="schedule" id="schedule" class="form-control @error('schedule') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['A1', 'A2', 'A3', 'B1', 'B2', 'C1', 'D1', 'Outros'] as $sched)
                                <option value="{{ $sched }}" {{ old('schedule') == $sched ? 'selected' : '' }}>{{ $sched }}</option>
                            @endforeach
                        </select>
                        @error('schedule')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="anvisa_register">Registro ANVISA</label>
                        <input type="text" name="anvisa_register" id="anvisa_register" class="form-control" value="{{ old('anvisa_register') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="unit">Unidade *</label>
                        <select name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(['ml', 'mg', 'g', 'mcg', 'UI', 'comprimido', 'ampola', 'frasco'] as $u)
                                <option value="{{ $u }}" {{ old('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                        @error('unit')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="current_stock">Estoque Atual</label>
                        <input type="number" step="0.01" name="current_stock" id="current_stock" class="form-control" value="{{ old('current_stock', '0') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_stock">Estoque Mínimo</label>
                        <input type="number" step="0.01" name="min_stock" id="min_stock" class="form-control" value="{{ old('min_stock', '0') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4 pt-3">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativo</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
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
