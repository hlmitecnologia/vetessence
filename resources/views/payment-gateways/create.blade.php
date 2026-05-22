@extends('layouts.adminlte', ['title' => 'Novo Gateway'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Gateway de Pagamento</h3>
        <div class="card-tools">
            <a href="{{ route('payment-gateways.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <form action="{{ route('payment-gateways.store') }}" method="POST">
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
                        <label for="provider">Provedor *</label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="mercadopago" {{ old('provider') == 'mercadopago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="pagseguro" {{ old('provider') == 'pagseguro' ? 'selected' : '' }}>PagSeguro</option>
                            <option value="stripe" {{ old('provider') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="pix" {{ old('provider') == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="other" {{ old('provider') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" name="is_active" class="custom-control-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativo (desativa outros)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_sandbox" class="custom-control-input" value="1" {{ old('is_sandbox', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_sandbox">Modo Sandbox</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="public_key">Chave Pública</label>
                <textarea name="public_key" rows="2" class="form-control">{{ old('public_key') }}</textarea>
            </div>
            <div class="form-group">
                <label for="secret_key">Chave Secreta</label>
                <textarea name="secret_key" rows="2" class="form-control">{{ old('secret_key') }}</textarea>
            </div>
            <div class="form-group">
                <label for="webhook_secret">Segredo do Webhook</label>
                <input type="text" name="webhook_secret" class="form-control" value="{{ old('webhook_secret') }}">
            </div>
            <div class="form-group">
                <label for="webhook_url">URL do Webhook</label>
                <input type="url" name="webhook_url" class="form-control" value="{{ old('webhook_url') }}" placeholder="{{ url('/api/v1/payment/webhook') }}">
            </div>
            <div class="form-group">
                <label for="branch_id">Unidade</label>
                <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                    <option value="">Todas as unidades</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="config">Configuração Adicional (JSON)</label>
                <textarea name="config" rows="3" class="form-control" placeholder='{"key": "value"}'></textarea>
            </div>
            <div class="form-group">
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
