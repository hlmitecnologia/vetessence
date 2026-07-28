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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $paymentGateway->name) }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="provider">Provedor *</label>
                        {{-- Demais provedores ocultados temporariamente (reativar quando voltar) --}}
                        <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="gateway" required>
                            <option value="">Selecione</option>
                            <option value="mercadopago" {{ old('provider', $paymentGateway->provider) == 'mercadopago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="multicard" {{ old('provider', $paymentGateway->provider) == 'multicard' ? 'selected' : '' }}>MultiplusCard (PinPDV)</option>
                            <option value="payer" style="display:none;" {{ old('provider', $paymentGateway->provider) == 'payer' ? 'selected' : '' }}>Payer API Gateway</option>
                            <option value="pix" {{ old('provider', $paymentGateway->provider) == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="pagseguro" style="display:none;" {{ old('provider', $paymentGateway->provider) == 'pagseguro' ? 'selected' : '' }}>PagSeguro</option>
                            <option value="stone" style="display:none;" {{ old('provider', $paymentGateway->provider) == 'stone' ? 'selected' : '' }}>Stone</option>
                            <option value="stripe" style="display:none;" {{ old('provider', $paymentGateway->provider) == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="other" style="display:none;" {{ old('provider', $paymentGateway->provider) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="channel">Canal de uso *</label>
                        <select name="channel" class="form-control @error('channel') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="portal" {{ old('channel', $paymentGateway->channel) == 'portal' ? 'selected' : '' }}>Portal (pagamento online pelo tutor)</option>
                            <option value="pdv" {{ old('channel', $paymentGateway->channel) == 'pdv' ? 'selected' : '' }}>PDV (pagamento presencial na maquininha)</option>
                            <option value="both" {{ old('channel', $paymentGateway->channel) == 'both' ? 'selected' : '' }}>Ambos (Portal + PDV)</option>
                        </select>
                        @error('channel')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $paymentGateway->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Ativo (desativa outros do mesmo canal)</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_sandbox" value="0">
                        <input type="checkbox" name="is_sandbox" id="is_sandbox" class="custom-control-input" value="1" {{ old('is_sandbox', $paymentGateway->is_sandbox) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_sandbox">Sandbox (homologação)</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Webhook (URL a configurar no provedor)</label>
                        <div class="input-group">
                            <input type="url" readonly class="form-control"
                                value="{{ url('/api/payments/webhook/' . $paymentGateway->id) }}"
                                id="webhook-url-preview">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" onclick="copyWebhookUrl()" title="Copiar"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MERCADO PAGO --}}
            <div class="provider-fields" data-provider="mercadopago" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fab fa-mercadopago mr-1"></i>Mercado Pago</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    O Access Token é obtido no painel Mercado Pago em <strong>Desenvolvedores &gt; Credenciais</strong>.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Public Key</label>
                            <input type="text" name="public_key" class="form-control @error('public_key') is-invalid @enderror" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="APP_USR-xxxx-xxxx">
                            @error('public_key')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Access Token (secret_key)</label>
                            <input type="password" name="secret_key" class="form-control @error('secret_key') is-invalid @enderror" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="APP_USR-xxxx-xxxx">
                            @error('secret_key')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Terminal ID (Point Smart) *</label>
                    <div id="mp-devices-wrapper">
                        <div class="input-group">
                            <input type="text" name="config[terminal_id]" id="terminal_id_input"
                                class="form-control @error('config.terminal_id') is-invalid @enderror"
                                value="{{ old('config.terminal_id', $paymentGateway->config['terminal_id'] ?? '') }}"
                                placeholder="Selecione um device abaixo ou digite manualmente">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="mp-refresh-devices" title="Recarregar devices">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div id="mp-devices-status" class="mt-1"></div>
                        <div id="mp-devices-select-wrapper" class="mt-1" style="display:none;">
                            <select id="mp-devices-select" class="form-control form-control-sm">
                                <option value="">Selecione um device...</option>
                            </select>
                        </div>
                    </div>
                    @error('config.terminal_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    <small class="text-muted">Devices obtidos automaticamente via <code>GET /point/integration-api/devices</code>.</small>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-link mr-1"></i><strong>Webhook (configurar no painel Mercado Pago)</strong><br>
                    Configure a URL abaixo em <strong>Your Integrations &gt; Webhooks</strong> para o tópico <strong>Order (Mercado Pago)</strong>:
                    <div class="input-group mt-2">
                        <input type="text" readonly class="form-control"
                            value="{{ url('/api/payments/webhook/' . $paymentGateway->id) }}"
                            id="mp-webhook-preview">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default" onclick="copyMpWebhook()" title="Copiar"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PIX --}}
            <div class="provider-fields" data-provider="pix" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-qrcode mr-1"></i>PIX</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    O PIX estático não utiliza webhook. O pagamento é confirmado manualmente.
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle mr-1"></i>
                    O nome do recebedor e a cidade serão obtidos automaticamente da unidade selecionada abaixo.
                </p>
                <div class="form-group">
                    <label>Chave PIX</label>
                    <input type="text" name="public_key" class="form-control @error('public_key') is-invalid @enderror" value="{{ old('public_key', $paymentGateway->public_key) }}" placeholder="CPF, CNPJ, e-mail, telefone ou EVP">
                    @error('public_key')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>URL (opcional — para PIX dinâmico)</label>
                    <input type="url" name="config[url]" class="form-control @error('config.url') is-invalid @enderror" value="{{ old('config.url', $paymentGateway->config['url'] ?? '') }}" placeholder="https://">
                    @error('config.url')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- PAYER --}}
            <div class="provider-fields" data-provider="payer" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-cloud mr-1"></i>Payer API Gateway</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Integração com Payer API Gateway. Autenticação JWT via endpoint OAuth.
                    Credenciais fornecidas pela Payer.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company ID *</label>
                            <input type="text" name="config[company_id]" class="form-control @error('config.company_id') is-invalid @enderror" value="{{ old('config.company_id', $paymentGateway->config['company_id'] ?? '') }}" placeholder="ID da empresa na Payer">
                            @error('config.company_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Store ID *</label>
                            <input type="text" name="config[store_id]" class="form-control @error('config.store_id') is-invalid @enderror" value="{{ old('config.store_id', $paymentGateway->config['store_id'] ?? '') }}" placeholder="ID da loja na Payer">
                            @error('config.store_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Terminal ID *</label>
                            <input type="text" name="config[terminal_id]" class="form-control @error('config.terminal_id') is-invalid @enderror" value="{{ old('config.terminal_id', $paymentGateway->config['terminal_id'] ?? '') }}" placeholder="ID do terminal Payer">
                            @error('config.terminal_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Automation Name *</label>
                            <input type="text" name="config[automation_name]" class="form-control @error('config.automation_name') is-invalid @enderror" value="{{ old('config.automation_name', $paymentGateway->config['automation_name'] ?? '') }}" placeholder="Nome da automação">
                            @error('config.automation_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Client ID *</label>
                            <input type="text" name="config[client_id]" class="form-control @error('config.client_id') is-invalid @enderror" value="{{ old('config.client_id', $paymentGateway->config['client_id'] ?? '') }}" placeholder="clientId da API Payer">
                            @error('config.client_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" name="config[username]" class="form-control @error('config.username') is-invalid @enderror" value="{{ old('config.username', $paymentGateway->config['username'] ?? '') }}" placeholder="userName (e-mail)">
                            @error('config.username')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>User Alias *</label>
                            <input type="text" name="config[user_alias]" class="form-control @error('config.user_alias') is-invalid @enderror" value="{{ old('config.user_alias', $paymentGateway->config['user_alias'] ?? '') }}" placeholder="userAlias">
                            @error('config.user_alias')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-link mr-1"></i><strong>Webhook (configurar no painel Payer)</strong><br>
                    Configure a URL abaixo no painel da Payer para receber notificações de pagamento:
                    <div class="input-group mt-2">
                        <input type="text" readonly class="form-control"
                            value="{{ url('/api/payments/webhook/' . $paymentGateway->id) }}"
                            id="payer-webhook-preview">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default" onclick="copyPayerWebhook()" title="Copiar"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MULTIPLUSCARD --}}
            <div class="provider-fields" data-provider="multicard" data-group="gateway" style="display:none;">
                <h6 class="text-primary mt-3"><i class="fas fa-credit-card mr-1"></i>MultiplusCard (PinPDV)</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Integração com SmartPOS MultiplusCard. O token de acesso é obtido no portal PinPDV em
                    <strong>Configurações &gt; Tokens de Acesso &gt; Cadastrar</strong>.
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Token de Acesso *</label>
                            <input type="password" name="secret_key" class="form-control @error('secret_key') is-invalid @enderror" value="{{ old('secret_key', $paymentGateway->secret_key) }}" placeholder="Bearer token de longa duração">
                            @error('secret_key')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ambiente *</label>
                            <select name="config[ambiente]" class="form-control @error('config.ambiente') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                <option value="homologacao" {{ old('config.ambiente', $paymentGateway->config['ambiente'] ?? '') == 'homologacao' ? 'selected' : '' }}>Homologação</option>
                                <option value="producao" {{ old('config.ambiente', $paymentGateway->config['ambiente'] ?? '') == 'producao' ? 'selected' : '' }}>Produção</option>
                            </select>
                            @error('config.ambiente')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>ID do Dispositivo (PinPDV) *</label>
                    <input type="number" name="config[pinpdv_id]" class="form-control @error('config.pinpdv_id') is-invalid @enderror" value="{{ old('config.pinpdv_id', $paymentGateway->config['pinpdv_id'] ?? '') }}" placeholder="ID numérico do SmartPOS">
                    @error('config.pinpdv_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    <small class="text-muted">Para listar os dispositivos disponíveis, acesse o portal PinPDV ou use a API: <code>GET /pinpdv</code></small>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-link mr-1"></i><strong>Webhook (configurar no painel PinPDV)</strong><br>
                    Para receber notificações automáticas de pagamento, configure a URL abaixo no painel do MultiplusCard:
                    <div class="input-group mt-2">
                        <input type="text" readonly class="form-control"
                            value="{{ url('/api/payments/webhook/' . $paymentGateway->id) }}"
                            id="multicard-webhook-preview">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default" onclick="copyMulticardWebhook()" title="Copiar"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OUTRO (genérico) --}}
            {{-- Suspenso temporariamente @if(false) ... @endif --}}

            <div class="form-group">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes', $paymentGateway->notes) }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
            var visible = fields[i].dataset.provider === selected;
            fields[i].style.display = visible ? 'block' : 'none';
            fields[i].querySelectorAll('input, textarea, select').forEach(function(el) {
                el.disabled = !visible;
            });
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        toggleProviderFields();
        var select = document.querySelector('.provider-select[data-group="gateway"]');
        if (select) {
            select.addEventListener('change', toggleProviderFields);
        }
    });

    function copyPayerWebhook() {
        var el = document.getElementById('payer-webhook-preview');
        if (el) {
            navigator.clipboard.writeText(el.value).then(function() {
                alert('URL do webhook copiada!');
            });
        }
    }

    function copyMulticardWebhook() {
        var el = document.getElementById('multicard-webhook-preview');
        if (el) {
            navigator.clipboard.writeText(el.value).then(function() {
                alert('URL do webhook copiada!');
            });
        }
    }

    function copyMpWebhook() {
        var el = document.getElementById('mp-webhook-preview');
        if (el) {
            navigator.clipboard.writeText(el.value).then(function() {
                alert('URL do webhook copiada!');
            });
        }
    }

    function loadMpDevices() {
        var status = document.getElementById('mp-devices-status');
        var selectWrapper = document.getElementById('mp-devices-select-wrapper');
        var select = document.getElementById('mp-devices-select');
        var input = document.getElementById('terminal_id_input');
        var currentVal = input.value;

        status.innerHTML = '<small class="text-info"><i class="fas fa-spinner fa-spin"></i> Buscando devices no Mercado Pago...</small>';
        selectWrapper.style.display = 'none';

        fetch('{{ route("payment-gateways.mp-devices", $paymentGateway) }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && data.devices && data.devices.length > 0) {
                status.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> ' + data.devices.length + ' device(s) encontrado(s). Selecione abaixo:</small>';
                select.innerHTML = '<option value="">Selecione um device...</option>';
                data.devices.forEach(function(d) {
                    var opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name + ' — ' + d.id + ' [' + d.status + ']';
                    if (d.id === currentVal) opt.selected = true;
                    select.appendChild(opt);
                });
                selectWrapper.style.display = 'block';
            } else {
                status.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Nenhum device encontrado. Verifique o Access Token.') + '</small>';
            }
        })
        .catch(function(e) {
            status.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Erro ao buscar devices: ' + e.message + '</small>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var refreshBtn = document.getElementById('mp-refresh-devices');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', loadMpDevices);
        }
        var devicesSelect = document.getElementById('mp-devices-select');
        if (devicesSelect) {
            devicesSelect.addEventListener('change', function() {
                document.getElementById('terminal_id_input').value = this.value;
            });
        }

        if (document.querySelector('.provider-select[data-group="gateway"]').value === 'mercadopago') {
            loadMpDevices();
        }
    });
})();
</script>
