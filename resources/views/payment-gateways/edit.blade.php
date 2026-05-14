@extends('layouts.adminlte', ['title' => 'Editar Gateway'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar - {{ $paymentGateway->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('payment-gateways.show', $paymentGateway) }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('payment-gateways.update', $paymentGateway) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $paymentGateway->name) }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="provider">Provedor *</label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="mercadopago" {{ old('provider', $paymentGateway->provider) == 'mercadopago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="pagseguro" {{ old('provider', $paymentGateway->provider) == 'pagseguro' ? 'selected' : '' }}>PagSeguro</option>
                            <option value="stripe" {{ old('provider', $paymentGateway->provider) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="pix" {{ old('provider', $paymentGateway->provider) == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="other" {{ old('provider', $paymentGateway->provider) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_active" class="custom-control-input" value="1" {{ old('is_active', $paymentGateway->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Ativo</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_sandbox" class="custom-control-input" value="1" {{ old('is_sandbox', $paymentGateway->is_sandbox) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_sandbox">Sandbox</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="public_key">Chave Pública</label>
                <textarea name="public_key" rows="2" class="form-control">{{ old('public_key', $paymentGateway->public_key) }}</textarea>
            </div>
            <div class="form-group">
                <label for="secret_key">Chave Secreta</label>
                <textarea name="secret_key" rows="2" class="form-control">{{ old('secret_key', $paymentGateway->secret_key) }}</textarea>
            </div>
            <div class="form-group">
                <label for="webhook_secret">Segredo Webhook</label>
                <input type="text" name="webhook_secret" class="form-control" value="{{ old('webhook_secret', $paymentGateway->webhook_secret) }}">
            </div>
            <div class="form-group">
                <label for="webhook_url">URL Webhook</label>
                <input type="url" name="webhook_url" class="form-control" value="{{ old('webhook_url', $paymentGateway->webhook_url) }}">
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $paymentGateway->notes) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection
