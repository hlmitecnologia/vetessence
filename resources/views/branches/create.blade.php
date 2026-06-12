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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Endereço</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="number">Número</label>
                        <input type="text" name="number" class="form-control" value="{{ old('number') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="neighborhood">Bairro</label>
                        <input type="text" name="neighborhood" class="form-control" value="{{ old('neighborhood') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="complement">Complemento</label>
                <input type="text" name="complement" class="form-control" value="{{ old('complement') }}">
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="zip_code">CEP</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" maxlength="10" placeholder="00000-000">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="state_id">Estado</label>
                        <select name="state_id" id="branch_state_id" class="form-control @error('state_id') is-invalid @enderror">
                            <option value="">Selecione...</option>
                            @foreach(\App\Models\State::orderBy('name')->get() as $state)
                                <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="city_id">Cidade</label>
                        <select name="city_id" id="branch_city_id" class="form-control @error('city_id') is-invalid @enderror">
                            <option value="">Selecione o estado primeiro...</option>
                        </select>
                        @error('city_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-2">
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
            <hr>
            <h5>Dados Fiscais NFS-e</h5>
            <p class="text-muted small">Utilizados na emissão de Nota Fiscal de Serviços Eletrônica.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="municipio_ibge">Código IBGE do Município</label>
                        <input type="text" name="municipio_ibge" class="form-control" value="{{ old('municipio_ibge') }}" placeholder="Ex.: 3550308" maxlength="7">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="regime_tributario">Regime Tributário</label>
                        <select name="regime_tributario" class="form-control">
                            <option value="">-- Selecione --</option>
                            <option value="mei" @selected(old('regime_tributario') === 'mei')>MEI</option>
                            <option value="simples_nacional" @selected(old('regime_tributario') === 'simples_nacional')>Simples Nacional</option>
                            <option value="lucro_presumido" @selected(old('regime_tributario') === 'lucro_presumido')>Lucro Presumido</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="serie">Série</label>
                        <input type="text" name="serie" class="form-control" value="{{ old('serie', '1') }}" maxlength="3">
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="notes">Observações</label>
                <textarea name="notes" rows="2" class="wysiwyg form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#branch_state_id').on('change', function() {
        var stateId = $(this).val();
        var $citySelect = $('#branch_city_id');
        $citySelect.html('<option value="">Carregando...</option>');
        if (stateId) {
            $.get('/api/cities/' + stateId, function(data) {
                $citySelect.html('<option value="">Selecione...</option>');
                $.each(data, function(id, name) {
                    $citySelect.append('<option value="' + id + '">' + name + '</option>');
                });
            });
        } else {
            $citySelect.html('<option value="">Selecione o estado primeiro...</option>');
        }
    });

    $('#zip_code').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length !== 8) return;

        $.get('/api/cep/' + cep, function(data) {
            if (data.error) return;
            if (data.street) $('input[name="address"]').val(data.street);
            if (data.neighborhood) $('input[name="neighborhood"]').val(data.neighborhood);

            if (data.state) {
                var $stateSelect = $('#branch_state_id');
                $stateSelect.val($stateSelect.find('option').filter(function() {
                    return $(this).text().toUpperCase().indexOf(data.state.toUpperCase()) > -1;
                }).first().val());
                $stateSelect.trigger('change');

                if (data.city) {
                    setTimeout(function() {
                        var $citySelect = $('#branch_city_id');
                        $citySelect.find('option').each(function() {
                            if ($(this).text().toUpperCase() === data.city.toUpperCase()) {
                                $citySelect.val($(this).val());
                                return false;
                            }
                        });
                    }, 500);
                }
            }
        });
    });

    @if(old('state_id'))
        $('#branch_state_id').trigger('change');
    @endif
});
</script>
@endpush
