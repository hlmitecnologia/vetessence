@php
    $title = 'Configuração NFS-e';
@endphp
@extends('layouts.adminlte')

@push('styles')
<style>
.provider-fields { display:none; }
.provider-fields[data-provider="{{ old('provider', $config->provider ?? 'webmania') }}"] { display:block; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configuração NFS-e</h3>
            </div>
            <form action="{{ route('nfse.config.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p class="text-muted">Configure os dados fiscais para emissão de Nota Fiscal de Serviços Eletrônica.</p>

                    <div class="form-group">
                        <label>Unidade *</label>
                        <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                            <option value="">Selecione a unidade</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    data-cnpj="{{ $branch->cnpj }}"
                                    {{ old('branch_id', $branchId) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <hr>
                    <h5>Dados Fiscais</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CNPJ *</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj', $config->cnpj ?? '') }}" required>
                                @error('cnpj') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Código IBGE do Município *</label>
                                <input type="text" name="municipio_ibge" class="form-control @error('municipio_ibge') is-invalid @enderror" value="{{ old('municipio_ibge', $config->municipio_ibge ?? '') }}" required>
                                @error('municipio_ibge') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Regime Tributário *</label>
                                <select name="regime_tributario" class="form-control @error('regime_tributario') is-invalid @enderror" required>
                                    <option value="mei" @selected(old('regime_tributario', $config->regime_tributario ?? '') === 'mei')>MEI</option>
                                    <option value="simples_nacional" @selected(old('regime_tributario', $config->regime_tributario ?? '') === 'simples_nacional')>Simples Nacional</option>
                                    <option value="lucro_presumido" @selected(old('regime_tributario', $config->regime_tributario ?? '') === 'lucro_presumido')>Lucro Presumido</option>
                                </select>
                                @error('regime_tributario') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Série *</label>
                                <input type="text" name="serie" class="form-control @error('serie') is-invalid @enderror" value="{{ old('serie', $config->serie ?? '1') }}" required>
                                @error('serie') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
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

                    <hr>
                    <h5>Provedor NFS-e</h5>

                    <div class="form-group">
                        <label>Provedor *</label>
                        <select name="provider" class="form-control provider-select @error('provider') is-invalid @enderror" data-group="nfse" required>
                            <option value="webmania" {{ old('provider', $config->provider ?? 'webmania') == 'webmania' ? 'selected' : '' }}>Webmania®</option>
                            <option value="focusnfe" {{ old('provider', $config->provider ?? '') == 'focusnfe' ? 'selected' : '' }}>FocusNFe</option>
                            <option value="ginfes" {{ old('provider', $config->provider ?? '') == 'ginfes' ? 'selected' : '' }}>GinFes</option>
                        </select>
                        @error('provider') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- WEBMANIA --}}
                    <div class="provider-fields" data-provider="webmania" data-group="nfse">
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

                    {{-- FOCUS NFE --}}
                    <div class="provider-fields" data-provider="focusnfe" data-group="nfse">
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

                    {{-- GINFES --}}
                    <div class="provider-fields" data-provider="ginfes" data-group="nfse">
                        <h6 class="text-primary mt-3"><i class="fas fa-building mr-1"></i>Credenciais GinFes</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usuário (e-mail) *</label>
                                    <input type="email" name="ginfes_username" class="form-control @error('ginfes_username') is-invalid @enderror" value="{{ old('ginfes_username', $config->ginfes_username ?? '') }}" placeholder="email@exemplo.com">
                                    @error('ginfes_username') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Senha *</label>
                                    <input type="password" name="ginfes_password" class="form-control @error('ginfes_password') is-invalid @enderror" value="{{ old('ginfes_password', $config->ginfes_password ?? '') }}" placeholder="********">
                                    @error('ginfes_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Configuração</button>
                    <a href="{{ route('nfse.index') }}" class="btn btn-default float-right">Ver NFSe Emitidas</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
function toggleNfseProviderFields() {
    var select = document.querySelector('.provider-select[data-group="nfse"]');
    if (!select) return;
    var selected = select.value;
    var fields = document.querySelectorAll('.provider-fields[data-group="nfse"]');
    for (var i = 0; i < fields.length; i++) {
        fields[i].style.display = fields[i].dataset.provider === selected ? 'block' : 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleNfseProviderFields();
    var select = document.querySelector('.provider-select[data-group="nfse"]');
    if (select) {
        select.addEventListener('change', toggleNfseProviderFields);
    }
    var branchSelect = document.getElementById('branch_id');
    if (branchSelect) {
        branchSelect.addEventListener('change', function() {
            var cnpj = this.options[this.selectedIndex]?.dataset.cnpj || '';
            document.getElementById('cnpj').value = cnpj;
            var url = new URL(window.location);
            url.searchParams.set('branch_id', this.value);
            window.location.href = url.toString();
        });
    }
});
@endpush
