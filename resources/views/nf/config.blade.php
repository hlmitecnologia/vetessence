@php
    $title = 'Configuração NF';
    $providerNames = [
        'webmania' => 'Webmania®',
        'focusnfe' => 'FocusNFe',
        'spedy' => 'Spedy',
        'nfeio' => 'NFE.io',
    ];
@endphp
@extends('layouts.adminlte')

@push('styles')
<style>
.provider-fields { display:none; }
.provider-fields[data-group="nfse"][data-provider="{{ old('provider', $nfseConfig->provider ?? 'webmania') }}"] { display:block; }
.provider-fields[data-group="nfe"][data-provider="{{ old('provider', $nfeConfig->provider ?? 'focusnfe') }}"] { display:block; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-1"></i>
            Configure o provedor de emissão de notas fiscais. Os dados fiscais (CNPJ, IE, CRT, código IBGE) são obtidos automaticamente do cadastro da unidade.
        </div>
    </div>

    {{-- NFS-e --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice"></i> NFS-e (Serviços)</h3>
                @php $nfseProvider = $providerNames[$nfseConfig->provider ?? 'webmania'] ?? $nfseConfig->provider ?? 'Webmania®'; @endphp
                <span class="badge badge-success float-right mt-1">Ativo: {{ $nfseProvider }}</span>
            </div>
            <form action="{{ route('nfse.config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provedor *</label>
                                <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="nfse" required>
                                    <option value="webmania" {{ old('provider', $nfseConfig->provider ?? 'webmania') == 'webmania' ? 'selected' : '' }}>Webmania®</option>
                                    <option value="focusnfe" {{ old('provider', $nfseConfig->provider ?? '') == 'focusnfe' ? 'selected' : '' }}>FocusNFe</option>
                                    <option value="spedy" {{ old('provider', $nfseConfig->provider ?? '') == 'spedy' ? 'selected' : '' }}>Spedy</option>
                                    <option value="nfeio" {{ old('provider', $nfseConfig->provider ?? '') == 'nfeio' ? 'selected' : '' }}>NFE.io</option>
                                </select>
                                @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ambiente *</label>
                                <select name="ambiente" class="form-control @error('ambiente') is-invalid @enderror" required>
                                    <option value="homologacao" @selected(old('ambiente', $nfseConfig->ambiente ?? 'homologacao') === 'homologacao')>Homologação</option>
                                    <option value="producao" @selected(old('ambiente', $nfseConfig->ambiente ?? '') === 'producao')>Produção</option>
                                </select>
                                @error('ambiente') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- WEBMANIA --}}
                    <div class="provider-fields" data-provider="webmania" data-group="nfse">
                        <h6 class="text-primary mt-3"><i class="fas fa-globe mr-1"></i>Credenciais Webmania®</h6>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            A API NFS-e v2.0 utiliza apenas o Access Token (Bearer token) obtido no painel Webmania.
                        </p>
                        <div class="form-group">
                            <label>Access Token *</label>
                            <input type="text" name="webmania_access_token" class="form-control @error('webmania_access_token') is-invalid @enderror" value="{{ old('webmania_access_token', $nfseConfig->webmania_access_token ?? '') }}" placeholder="Bearer token da API v2.0">
                            @error('webmania_access_token') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- FOCUS NFE --}}
                    <div class="provider-fields" data-provider="focusnfe" data-group="nfse">
                        <h6 class="text-primary mt-3"><i class="fas fa-bolt mr-1"></i>Credenciais FocusNFe</h6>
                        <div class="form-group">
                            <label>API Token *</label>
                            <input type="text" name="focusnfe_token" class="form-control @error('focusnfe_token') is-invalid @enderror" value="{{ old('focusnfe_token', $nfseConfig->focusnfe_token ?? '') }}" placeholder="Token de API">
                            @error('focusnfe_token') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            O token é enviado via HTTP Basic Auth (usuário: token, senha: vazio).
                        </p>
                    </div>

                    {{-- SPEDY --}}
                    <div class="provider-fields" data-provider="spedy" data-group="nfse">
                        <h6 class="text-primary mt-3"><i class="fas fa-rocket mr-1"></i>Credenciais Spedy</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="text" name="spedy_api_key" class="form-control @error('spedy_api_key') is-invalid @enderror" value="{{ old('spedy_api_key', $nfseConfig->spedy_api_key ?? '') }}" placeholder="Chave da API">
                                    @error('spedy_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>API Secret *</label>
                                    <input type="password" name="spedy_api_secret" class="form-control @error('spedy_api_secret') is-invalid @enderror" value="{{ old('spedy_api_secret', $nfseConfig->spedy_api_secret ?? '') }}" placeholder="******">
                                    @error('spedy_api_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NFE.IO --}}
                    <div class="provider-fields" data-provider="nfeio" data-group="nfse">
                        <h6 class="text-primary mt-3"><i class="fas fa-file-invoice mr-1"></i>Credenciais NFE.io</h6>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            A API NFE.io utiliza o header <code>Authorization: {api_key}</code> (diretamente, sem prefixo Basic). O Company ID é o identificador da empresa cadastrada na plataforma NFE.io.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="text" name="nfeio_api_key" class="form-control @error('nfeio_api_key') is-invalid @enderror" value="{{ old('nfeio_api_key', $nfseConfig->nfeio_api_key ?? '') }}" placeholder="Chave da API">
                                    @error('nfeio_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company ID *</label>
                                    <input type="text" name="nfeio_company_id" class="form-control @error('nfeio_company_id') is-invalid @enderror" value="{{ old('nfeio_company_id', $nfseConfig->nfeio_company_id ?? '') }}" placeholder="ID da empresa na NFE.io">
                                    @error('nfeio_company_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar NFS-e</button>
                    <a href="{{ route('nfse.index') }}" class="btn btn-default float-right">Ver NFS-e Emitidas</a>
                </div>
            </form>
        </div>
    </div>

    {{-- NF-e / NFC-e --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box"></i> NF-e / NFC-e (Produtos)</h3>
                @php $nfeProvider = $providerNames[$nfeConfig->provider ?? 'focusnfe'] ?? $nfeConfig->provider ?? 'FocusNFe'; @endphp
                <span class="badge badge-success float-right mt-1">Ativo: {{ $nfeProvider }}</span>
            </div>
            <form action="{{ route('nfe.config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provedor *</label>
                                <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="nfe" required>
                                    <option value="focusnfe" {{ old('provider', $nfeConfig->provider ?? 'focusnfe') == 'focusnfe' ? 'selected' : '' }}>FocusNFe</option>
                                    <option value="nfeio" {{ old('provider', $nfeConfig->provider ?? '') == 'nfeio' ? 'selected' : '' }}>NFE.io</option>
                                    <option value="webmania" {{ old('provider', $nfeConfig->provider ?? '') == 'webmania' ? 'selected' : '' }}>Webmania®</option>
                                </select>
                                @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ambiente *</label>
                                <select name="ambiente" class="form-control @error('ambiente') is-invalid @enderror" required>
                                    <option value="homologacao" @selected(old('ambiente', $nfeConfig->ambiente ?? 'homologacao') === 'homologacao')>Homologação</option>
                                    <option value="producao" @selected(old('ambiente', $nfeConfig->ambiente ?? '') === 'producao')>Produção</option>
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
                            <input type="text" name="focusnfe_token" class="form-control @error('focusnfe_token') is-invalid @enderror" value="{{ old('focusnfe_token', $nfeConfig->focusnfe_token ?? '') }}" placeholder="Token de API">
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
                        <p class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            A API NFE.io utiliza o header <code>Authorization: {api_key}</code> (diretamente, sem prefixo Basic). O Company ID é o identificador da empresa cadastrada na plataforma NFE.io.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="text" name="nfeio_api_key" class="form-control @error('nfeio_api_key') is-invalid @enderror" value="{{ old('nfeio_api_key', $nfeConfig->nfeio_api_key ?? '') }}" placeholder="Chave da API">
                                    @error('nfeio_api_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company ID *</label>
                                    <input type="text" name="nfeio_company_id" class="form-control @error('nfeio_company_id') is-invalid @enderror" value="{{ old('nfeio_company_id', $nfeConfig->nfeio_company_id ?? '') }}" placeholder="ID da empresa na NFE.io">
                                    @error('nfeio_company_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WEBMANIA --}}
                    <div class="provider-fields" data-provider="webmania" data-group="nfe">
                        <h6 class="text-primary mt-3"><i class="fas fa-globe mr-1"></i>Credenciais Webmania®</h6>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            As credenciais são obtidas no painel Webmania → Minha Conta → API.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Key *</label>
                                    <input type="text" name="webmania_consumer_key" class="form-control @error('webmania_consumer_key') is-invalid @enderror" value="{{ old('webmania_consumer_key', $nfeConfig->webmania_consumer_key ?? '') }}">
                                    @error('webmania_consumer_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Consumer Secret *</label>
                                    <input type="password" name="webmania_consumer_secret" class="form-control @error('webmania_consumer_secret') is-invalid @enderror" value="{{ old('webmania_consumer_secret', $nfeConfig->webmania_consumer_secret ?? '') }}">
                                    @error('webmania_consumer_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Access Token *</label>
                                    <input type="text" name="webmania_access_token" class="form-control @error('webmania_access_token') is-invalid @enderror" value="{{ old('webmania_access_token', $nfeConfig->webmania_access_token ?? '') }}">
                                    @error('webmania_access_token') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Access Token Secret *</label>
                                    <input type="password" name="webmania_access_token_secret" class="form-control @error('webmania_access_token_secret') is-invalid @enderror" value="{{ old('webmania_access_token_secret', $nfeConfig->webmania_access_token_secret ?? '') }}">
                                    @error('webmania_access_token_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar NF-e</button>
                    <a href="{{ route('nfe.index') }}" class="btn btn-default float-right">Ver NF-e Emitidas</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleNfseProviderFields() {
    var select = document.querySelector('.provider-select[data-group="nfse"]');
    if (!select) return;
    var selected = select.value;
    var fields = document.querySelectorAll('.provider-fields[data-group="nfse"]');
    for (var i = 0; i < fields.length; i++) {
        fields[i].style.display = fields[i].dataset.provider === selected ? 'block' : 'none';
    }
}
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
    toggleNfseProviderFields();
    toggleNfeProviderFields();
    var nfseSelect = document.querySelector('.provider-select[data-group="nfse"]');
    if (nfseSelect) nfseSelect.addEventListener('change', toggleNfseProviderFields);
    var nfeSelect = document.querySelector('.provider-select[data-group="nfe"]');
    if (nfeSelect) nfeSelect.addEventListener('change', toggleNfeProviderFields);
});
</script>
@endpush
