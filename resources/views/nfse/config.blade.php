@php
    $title = 'Configuração NFS-e';
@endphp
@extends('layouts.adminlte')
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
                    <p class="text-muted">Configure os dados fiscais para emissão de Nota Fiscal de Serviços Eletrônica via Webmania®.</p>

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
                    <h5>Credenciais Webmania®</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>App ID *</label>
                                <input type="text" name="webmania_app_id" class="form-control @error('webmania_app_id') is-invalid @enderror" value="{{ old('webmania_app_id', $config->webmania_app_id ?? '') }}" required>
                                @error('webmania_app_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>App Secret *</label>
                                <input type="password" name="webmania_app_secret" class="form-control @error('webmania_app_secret') is-invalid @enderror" value="{{ old('webmania_app_secret', $config->webmania_app_secret ?? '') }}" required>
                                @error('webmania_app_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Consumer Key *</label>
                                <input type="text" name="webmania_consumer_key" class="form-control @error('webmania_consumer_key') is-invalid @enderror" value="{{ old('webmania_consumer_key', $config->webmania_consumer_key ?? '') }}" required>
                                @error('webmania_consumer_key') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Consumer Secret *</label>
                                <input type="password" name="webmania_consumer_secret" class="form-control @error('webmania_consumer_secret') is-invalid @enderror" value="{{ old('webmania_consumer_secret', $config->webmania_consumer_secret ?? '') }}" required>
                                @error('webmania_consumer_secret') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
<script>
document.getElementById('branch_id').addEventListener('change', function () {
    const cnpj = this.options[this.selectedIndex]?.dataset.cnpj || '';
    document.getElementById('cnpj').value = cnpj;

    const url = new URL(window.location);
    url.searchParams.set('branch_id', this.value);
    window.location.href = url.toString();
});
</script>
@endpush
