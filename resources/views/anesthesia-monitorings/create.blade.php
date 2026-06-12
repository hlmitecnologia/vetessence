@extends('layouts.adminlte', ['title' => 'Novo Monitoramento Anestésico'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Novo Monitoramento Anestésico</h3>
        <div class="card-tools">
            <a href="{{ route('anesthesia-monitorings.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('anesthesia-monitorings.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="surgery_id">Cirurgia *</label>
                        <x-tom-select name="surgery_id" id="surgery_id" :value="old('surgery_id')" required>
                            @foreach($surgeries as $surgery)
                                <option value="{{ $surgery->id }}" {{ old('surgery_id') == $surgery->id ? 'selected' : '' }}>
                                    {{ $surgery->pet->name ?? 'Pet' }} - {{ $surgery->surgery_type }} ({{ $surgery->scheduled_date->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </x-tom-select>
                        @error('surgery_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="vet_id">Veterinário Responsável</label>
                        <x-tom-select name="vet_id" id="vet_id" :value="old('vet_id')">
                            @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ old('vet_id') == $vet->id ? 'selected' : '' }}>{{ $vet->name }}</option>
                            @endforeach
                        </x-tom-select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="anesthetist">Anestesista</label>
                        <input type="text" name="anesthetist" id="anesthetist" class="form-control" value="{{ old('anesthetist') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="monitoring_start">Início do Monitoramento</label>
                        <input type="datetime-local" name="monitoring_start" id="monitoring_start" class="form-control" value="{{ old('monitoring_start', date('Y-m-d\TH:i')) }}">
                    </div>
                </div>
            </div>

            <hr>
            <h5>Protocolo Anestésico</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="anesthetic_protocol">Protocolo</label>
                        <textarea name="anesthetic_protocol" id="anesthetic_protocol" rows="2" class="wysiwyg form-control">{{ old('anesthetic_protocol') }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="premedication">Pré-medicação</label>
                        <textarea name="premedication" id="premedication" rows="2" class="wysiwyg form-control">{{ old('premedication') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="induction_agent">Agente Indutor</label>
                        <input type="text" name="induction_agent" id="induction_agent" class="form-control" value="{{ old('induction_agent') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maintenance_agent">Agente de Manutenção</label>
                        <input type="text" name="maintenance_agent" id="maintenance_agent" class="form-control" value="{{ old('maintenance_agent') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="iv_access">Acesso IV</label>
                        <select name="iv_access" id="iv_access" class="form-control">
                            <option value="">Selecione</option>
                            @php $ivAccessOptions = ['Cateter 20G', 'Cateter 22G', 'Cateter 24G', 'Cateter 18G', 'Agulha scalp', 'JE']; @endphp
                            @foreach($ivAccessOptions as $access)
                                <option value="{{ $access }}" {{ old('iv_access') == $access ? 'selected' : '' }}>{{ $access }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="intubation_type">Tipo de Intubação</label>
                        <select name="intubation_type" id="intubation_type" class="form-control">
                            <option value="">Selecione</option>
                            @php $intubationOptions = ['TOT', 'Máscara', 'Traqueostomia', 'Não intubado']; @endphp
                            @foreach($intubationOptions as $int)
                                <option value="{{ $int }}" {{ old('intubation_type') == $int ? 'selected' : '' }}>{{ $int }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fluid_type">Tipo de Fluido</label>
                        <select name="fluid_type" id="fluid_type" class="form-control">
                            <option value="">Selecione</option>
                            @php $fluidOptions = ['Ringer Lactato', 'Ringer Simples', 'SF 0,9%', 'Glicose 5%', 'Hetastarch']; @endphp
                            @foreach($fluidOptions as $fl)
                                <option value="{{ $fl }}" {{ old('fluid_type') == $fl ? 'selected' : '' }}>{{ $fl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fluid_rate">Taxa de Fluido (ml/kg/h)</label>
                        <input type="number" step="0.1" name="fluid_rate" id="fluid_rate" class="form-control" value="{{ old('fluid_rate') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="observations">Observações</label>
                        <textarea name="observations" id="observations" rows="2" class="wysiwyg form-control">{{ old('observations') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Iniciar Monitoramento
            </button>
        </div>
    </form>
</div>
@endsection
