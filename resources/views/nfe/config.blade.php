@php
    $title = 'Configuração NF-e';
@endphp
@extends('layouts.adminlte')

@push('styles')
<style>
.provider-fields { display:none; }
.provider-fields[data-provider="{{ old('provider', $config->provider ?? 'focusnfe') }}"] { display:block; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configuração NF-e</h3>
            </div>
            <form action="{{ route('nfe.config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Configure o provedor de emissão de NF-e. Os dados fiscais (CNPJ, IE, CRT, código IBGE) são obtidos automaticamente do cadastro da unidade.
                    </p>

                    <hr>
                    <h5>Provedor NF-e</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provedor *</label>
                                <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="nfe" required>
                                    <option value="focusnfe" {{ old('provider', $config->provider ?? 'focusnfe') == 'focusnfe' ? 'selected' : '' }}>FocusNFe</option>
                                    <option value="nfeio" {{ old('provider', $config->provider ?? '') == 'nfeio' ? 'selected' : '' }}>NFE.io</option>
                                    <option value="webmania" {{ old('provider', $config->provider ?? '') == 'webmania' ? 'selected' : '' }}>Webmania®</option>
                                </select>
                                @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ambiente *</label>
                                <select name="ambiente" class="form-control @error('ambiente') is-invalid @enderror" required>
                                    <option value="homologacao" @selected(old('ambiente', $config->ambiente ?? 'homologacao') === 'homologacao')>Homologação</option>
                                    <option value="producao" @selected(old('ambiente', $config->ambiente ?? '') === 'producao')>Produção</option>
                                </select>
                                @error('ambiente') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- FOCUS NFE --}}
                    <div class="provider-fields" data-provider="focusnfe" data-group="nfe">
                        <h6 class="text-primary mt-3"><i class="fas fa-bolt mr-1"></i>Credenciais FocusNFe</h6>
                        <div class="form-group">
                            <label>API Token *</label>
                            <input type="text" name="focusnfe_token" class="form-control @error('focusnfe_token') is-invalid @enderror" value="{{ old('focusnfe_token', $config->focusnfe_token ?? '') }}" placeholder="Token de API">
                            @error('focusnfe_token') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            O token é enviado via HTTP Basic Auth (usuário: token, senha: vazio).
                        </p>
                    </div>

                    {{-- NFE.IO --}}
                    <div class="provider-fields" data-provider="nfeio" data-group="nfe">
                        <h6 class="text-primary mt-3"><i class="fas fa-file-invoice mr-1"></i>Credenciais NFE.io</h6>
                        <div class="form-group">
                            <label>API Key *</label>
                            <input type="text" name="nfeio_api_key" class="form-control @error('nfeio_api_key') is-invalid @enderror" value="{{ old('nfeio_api_key', $config->nfeio_api_key ?? '') }}" placeholder="Chave da API">
                            @error('nfeio_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- WEBMANIA --}}
                    <div class="provider-fields" data-provider="webmania" data-group="nfe">
                        <h6 class="text-primary mt-3"><i class="fas fa-globe mr-1"></i>Credenciais Webmania®</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>App ID *</label>
                                    <input type="text" name="webmania_app_id" class="form-control @error('webmania_app_id') is-invalid @enderror" value="{{ old('webmania_app_id', $config->webmania_app_id ?? '') }}">
                                    @error('webmania_app_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>App Secret *</label>
                                    <input type="password" name="webmania_app_secret" class="form-control @error('webmania_app_secret') is-invalid @enderror" value="{{ old('webmania_app_secret', $config->webmania_app_secret ?? '') }}">
                                    @error('webmania_app_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Key *</label>
                                    <input type="text" name="webmania_consumer_key" class="form-control @error('webmania_consumer_key') is-invalid @enderror" value="{{ old('webmania_consumer_key', $config->webmania_consumer_key ?? '') }}">
                                    @error('webmania_consumer_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Secret *</label>
                                    <input type="password" name="webmania_consumer_secret" class="form-control @error('webmania_consumer_secret') is-invalid @enderror" value="{{ old('webmania_consumer_secret', $config->webmania_consumer_secret ?? '') }}">
                                    @error('webmania_consumer_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Configuração</button>
                    <a href="{{ route('nfe.index') }}" class="btn btn-default float-right">Ver NF-e Emitidas</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleNfeProviderFields() {
    var select = document.querySelector('.provider-select[data-group="nfe"]');
    if (!select) return;
    var selected = select.value;
    var fields = document.querySelectorAll('.provider-fields[data-group="nfe"]');
    for (var i = 0; i < fields.length; i++) {
        fields[i].style.display = fields[i].dataset.provider === selected ? 'block' : 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleNfeProviderFields();
    var select = document.querySelector('.provider-select[data-group="nfe"]');
    if (select) {
        select.addEventListener('change', toggleNfeProviderFields);
    }
});
</script>
@endpush
