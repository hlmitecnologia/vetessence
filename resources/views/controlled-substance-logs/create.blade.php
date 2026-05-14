@extends('layouts.adminlte', ['title' => 'Nova Movimentação'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Movimentação de Substância Controlada</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substance-logs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('controlled-substance-logs.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="controlled_substance_id">Substância *</label>
                <select name="controlled_substance_id" id="controlled_substance_id" class="form-control @error('controlled_substance_id') is-invalid @enderror" required>
                    <option value="">Selecione</option>
                    @foreach($substances as $s)
                        <option value="{{ $s->id }}" {{ old('controlled_substance_id', $selectedSubstanceId ?? '') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} (Estoque: {{ number_format($s->current_stock, 2, ',', '.') }} {{ $s->unit }})
                        </option>
                    @endforeach
                </select>
                @error('controlled_substance_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Tipo de Movimentação *</label>
                <div class="d-flex">
                    <div class="custom-control custom-radio mr-4">
                        <input type="radio" name="type" id="type_in" value="in" class="custom-control-input" {{ old('type', 'in') == 'in' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_in">
                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Entrada</span>
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="type" id="type_out" value="out" class="custom-control-input" {{ old('type') == 'out' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_out">
                            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Saída</span>
                        </label>
                    </div>
                </div>
                @error('type')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="quantity">Quantidade *</label>
                        <input type="number" step="0.01" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                        @error('quantity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pet_id">Pet (para saídas)</label>
                        <select name="pet_id" id="pet_id" class="form-control">
                            <option value="">Selecione</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="witness_id">Testemunha *</label>
                        <select name="witness_id" id="witness_id" class="form-control @error('witness_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('witness_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('witness_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="reason">Motivo/Justificativa *</label>
                <textarea name="reason" id="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Registrar Movimentação
            </button>
        </div>
    </form>
</div>
@endsection
