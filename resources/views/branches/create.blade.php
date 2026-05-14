@extends('layouts.adminlte', ['title' => 'Nova Unidade'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nova Unidade</h3>
        <div class="card-tools">
            <a href="{{ route('branches.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="address">Endereço</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="city">Cidade</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="state">UF</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state') }}" maxlength="2">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="zip_code">CEP</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" name="cnpj" class="form-control" value="{{ old('cnpj') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_main" class="custom-control-input" value="1" {{ old('is_main') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_main">Unidade Principal</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_active" class="custom-control-input" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Ativa</label>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection
