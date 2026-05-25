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
                        <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="gateway" required>
                            <option value="">Selecione</option>
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

            {{-- MERCADO PAGO --}}
            <div class="provider-fields" data-provider="mercadopago" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-credit-card mr-1"></i>Mercado Pago</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Client ID (Chave Pública)</label>
                            <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="APP_USR-...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Access Token (Chave Secreta)</label>
                            <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="APP_USR-...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- PAGSEGURO --}}
            <div class="provider-fields" data-provider="pagseguro" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-shield-alt mr-1"></i>PagSeguro</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>E-mail da conta</label>
                            <input type="email" name="public_key" class="form-control" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="email@exemplo.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Token</label>
                            <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="Token PagSeguro">
                        </div>
                    </div>
                </div>
            </div>

            {{-- STRIPE --}}
            <div class="provider-fields" data-provider="stripe" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fab fa-stripe mr-1"></i>Stripe</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Publishable Key</label>
                            <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="pk_live_...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Secret Key</label>
                            <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="sk_live_...">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Webhook Secret</label>
                    <input type="text" name="webhook_secret" class="form-control" value="{{ old('webhook_secret', $paymentGateway->webhook_secret) }}" placeholder="whsec_...">
                </div>
            </div>

            {{-- PIX --}}
            <div class="provider-fields" data-provider="pix" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-qrcode mr-1"></i>PIX</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chave PIX</label>
                            <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="CPF, CNPJ, e-mail, telefone ou EVP">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome do Recebedor</label>
                            <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="NOME DA EMPRESA">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" name="config[city]" class="form-control" value="{{ old('config.city', $paymentGateway->config['city'] ?? '') }}" placeholder="SAO PAULO">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>GUI (Identificador)</label>
                            <input type="text" name="config[gi]" class="form-control" value="{{ old('config.gi', $paymentGateway->config['gi'] ?? 'br.gov.bcb.pix') }}" placeholder="br.gov.bcb.pix">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>URL (opcional)</label>
                            <input type="url" name="config[url]" class="form-control" value="{{ old('config.url', $paymentGateway->config['url'] ?? '') }}" placeholder="https://">
                        </div>
                    </div>
                </div>
            </div>

            {{-- OUTRO (genérico) --}}
            <div class="provider-fields" data-provider="other" data-group="gateway" style="display:none;">
                <h6 class="text-muted mt-3"><i class="fas fa-plug mr-1"></i>Outro Provedor</h6>
                <div class="form-group">
                    <label>Chave Pública</label>
                    <textarea name="public_key" rows="2" class="form-control">{{ old('public_key', $paymentGateway->public_key) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Chave Secreta</label>
                    <textarea name="secret_key" rows="2" class="form-control">{{ old('secret_key', $paymentGateway->secret_key) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Segredo do Webhook</label>
                    <input type="text" name="webhook_secret" class="form-control" value="{{ old('webhook_secret', $paymentGateway->webhook_secret) }}">
                </div>
                <div class="form-group">
                    <label>URL do Webhook</label>
                    <input type="url" name="webhook_url" class="form-control" value="{{ old('webhook_url', $paymentGateway->webhook_url) }}" placeholder="{{ url('/api/v1/payment/webhook') }}">
                </div>
                <div class="form-group">
                    <label>Configuração Adicional (JSON)</label>
                    <textarea name="config" rows="3" class="form-control" placeholder='{"key": "value"}'>{{ old('config', is_array($paymentGateway->config) ? json_encode($paymentGateway->config, JSON_PRETTY_PRINT) : $paymentGateway->config) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $paymentGateway->notes) }}</textarea>
            </div>
            <div class="form-group">
                <label for="branch_id">Unidade</label>
                <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                    <option value="">Todas as unidades</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $paymentGateway->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
        </div>
    </form>
</div>
@endsection

<script>
(function() {
    function toggleProviderFields() {
        var select = document.querySelector('.provider-select[data-group="gateway"]');
        if (!select) return;
        var selected = select.value;
        var fields = document.querySelectorAll('.provider-fields[data-group="gateway"]');
        for (var i = 0; i < fields.length; i++) {
            fields[i].style.display = fields[i].dataset.provider === selected ? 'block' : 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        toggleProviderFields();
        var select = document.querySelector('.provider-select[data-group="gateway"]');
        if (select) {
            select.addEventListener('change', toggleProviderFields);
        }
    });
})();
</script>
