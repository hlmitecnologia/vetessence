@extends('layouts.adminlte', ['title' => 'Ajustar Estoque'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-pen"></i> Ajustar Estoque</h3>
            <div class="card-tools">
                <a href="{{ route('stock.movements') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <form action="{{ route('stock.adjust.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="type">Tipo *</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">Selecione</option>
                        <option value="entry" {{ old('type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                        <option value="exit" {{ old('type') == 'exit' ? 'selected' : '' }}>Saída</option>
                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                        <option value="loss" {{ old('type') == 'loss' ? 'selected' : '' }}>Perda</option>
                        <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>Devolução</option>
                    </select>
                    @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="product_id">Produto *</label>
                    <x-tom-select name="product_id" :value="old('product_id')" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (estoque: {{ $p->stock }})</option>
                        @endforeach
                    </x-tom-select>
                    @error('product_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantidade *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('quantity') }}" required>
                    @error('quantity')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="branch_id">Unidade *</label>
                    <x-tom-select name="branch_id" :value="old('branch_id')" required>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </x-tom-select>
                    @error('branch_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="batch_number">Lote</label>
                            <input type="text" name="batch_number" id="batch_number" class="form-control @error('batch_number') is-invalid @enderror" value="{{ old('batch_number') }}">
                            @error('batch_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="expiry_date">Validade</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Observações</label>
                    <textarea name="notes" id="notes" class="wysiwyg form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
