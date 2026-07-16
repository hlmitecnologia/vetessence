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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="provider">Provedor *</label>
                        {{-- Demais provedores ocultados temporariamente (reativar quando voltar) --}}
                        <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="gateway" required>
                            <option value="">Selecione</option>
                            <option value="pix" {{ old('provider') == 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="mercadopago" style="display:none;" {{ old('provider') == 'mercadopago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="pagseguro" style="display:none;" {{ old('provider') == 'pagseguro' ? 'selected' : '' }}>PagSeguro</option>
                            <option value="stone" style="display:none;" {{ old('provider') == 'stone' ? 'selected' : '' }}>Stone</option>
                            <option value="stripe" style="display:none;" {{ old('provider') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="other" style="display:none;" {{ old('provider') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('provider')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="channel">Canal de uso *</label>
                        <select name="channel" class="form-control @error('channel') is-invalid @enderror" required>
                            <option value="">Selecione</option>
                            <option value="portal" {{ old('channel') == 'portal' ? 'selected' : '' }}>Portal (pagamento online pelo tutor)</option>
                            <option value="pdv" {{ old('channel') == 'pdv' ? 'selected' : '' }}>PDV (maquininha de cartão)</option>
                            <option value="both" {{ old('channel', 'both') == 'both' ? 'selected' : '' }}>Ambos</option>
                        </select>
                        <small class="text-muted">Nem todos provedores suportam ambos os canais.</small>
                        @error('channel')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Ativo (desativa outros do mesmo canal)</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="hidden" name="is_sandbox" value="0">
                            <input type="checkbox" name="is_sandbox" id="is_sandbox" class="custom-control-input" value="1" {{ old('is_sandbox') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_sandbox">Modo Sandbox (homologação)</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Webhook (URL a configurar no provedor)</label>
                        <div class="input-group">
                            <input type="url" readonly class="form-control" value="Salve o gateway primeiro para gerar a URL" id="webhook-url-preview">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" onclick="copyWebhookUrl()" title="Copiar"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                        <small class="text-muted">Após salvar, a URL será: <code>{{ url('/api/payments/webhook/ID') }}</code></small>
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
                    <input type="text" name="public_key" class="form-control @error('public_key') is-invalid @enderror" value="{{ old('public_key') }}" placeholder="CPF, CNPJ, e-mail, telefone ou EVP">
                    @error('public_key')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>URL (opcional — para PIX dinâmico)</label>
                    <input type="url" name="config[url]" class="form-control @error('config.url') is-invalid @enderror" value="{{ old('config.url') }}" placeholder="https://">
                    @error('config.url')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- OUTRO (genérico) --}}
            {{-- Suspenso temporariamente @if(false) ... @endif --}}

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
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="wysiwyg form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
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
})();
</script>
