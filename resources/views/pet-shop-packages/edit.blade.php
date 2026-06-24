@extends('layouts.adminlte', ['title' => 'Editar Pacote'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Pacote Petshop</h3>
        <div class="card-tools">
            <a href="{{ route('pet-shop-packages.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('pet-shop-packages.update', $petShopPackage) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $petShopPackage->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo *</label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="grooming" {{ old('type', $petShopPackage->type) == 'grooming' ? 'selected' : '' }}>Banho & Tosa</option>
                            <option value="boarding" {{ old('type', $petShopPackage->type) == 'boarding' ? 'selected' : '' }}>Hotel</option>
                            <option value="both" {{ old('type', $petShopPackage->type) == 'both' ? 'selected' : '' }}>Ambos</option>
                        </select>
                        @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unidade *</label>
                        <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            @foreach(\App\Models\Branch::where('is_active', true)->get() as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', $petShopPackage->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $petShopPackage->description) }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Preço Total *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                            <input type="number" name="total_price" class="form-control @error('total_price') is-invalid @enderror" step="0.01" min="0" value="{{ old('total_price', $petShopPackage->total_price) }}" required>
                        </div>
                        @error('total_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Preço Original *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                            <input type="number" name="original_price" class="form-control @error('original_price') is-invalid @enderror" step="0.01" min="0" value="{{ old('original_price', $petShopPackage->original_price) }}" required>
                        </div>
                        @error('original_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Validade (dias) *</label>
                        <input type="number" name="validity_days" class="form-control @error('validity_days') is-invalid @enderror" min="1" value="{{ old('validity_days', $petShopPackage->validity_days) }}" required>
                        @error('validity_days') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Máx. Usos *</label>
                        <input type="number" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror" min="1" value="{{ old('max_uses', $petShopPackage->max_uses) }}" required>
                        @error('max_uses') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" value="1" {{ old('is_active', $petShopPackage->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="isActive">Ativo</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection
