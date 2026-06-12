@extends('layouts.adminlte', ['title' => 'Editar Monitoramento Anestésico'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Monitoramento Anestésico</h3>
        <div class="card-tools">
            <a href="{{ route('anesthesia-monitorings.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <form action="{{ route('anesthesia-monitorings.update', $monitoring) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Pet</label>
                        <input type="text" class="form-control" value="{{ $monitoring->pet->name ?? '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Cirurgia</label>
                        <input type="text" class="form-control" value="{{ $monitoring->surgery->surgery_type ?? '-' }}" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="anesthetist">Anestesista</label>
                        <input type="text" name="anesthetist" id="anesthetist" class="form-control" value="{{ old('anesthetist', $monitoring->anesthetist) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="monitoring_start">Início</label>
                        <input type="datetime-local" name="monitoring_start" id="monitoring_start" class="form-control"
                               value="{{ old('monitoring_start', $monitoring->monitoring_start ? $monitoring->monitoring_start->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="monitoring_end">Término</label>
                        <input type="datetime-local" name="monitoring_end" id="monitoring_end" class="form-control"
                               value="{{ old('monitoring_end', $monitoring->monitoring_end ? $monitoring->monitoring_end->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
            </div>

            <hr>
            <h5>Protocolo Anestésico</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="anesthetic_protocol">Protocolo</label>
                        <textarea name="anesthetic_protocol" id="anesthetic_protocol" rows="2" class="wysiwyg form-control">{{ old('anesthetic_protocol', $monitoring->anesthetic_protocol) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="premedication">Pré-medicação</label>
                        <textarea name="premedication" id="premedication" rows="2" class="wysiwyg form-control">{{ old('premedication', $monitoring->premedication) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="induction_agent">Agente Indutor</label>
                        <input type="text" name="induction_agent" id="induction_agent" class="form-control" value="{{ old('induction_agent', $monitoring->induction_agent) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maintenance_agent">Agente de Manutenção</label>
                        <input type="text" name="maintenance_agent" id="maintenance_agent" class="form-control" value="{{ old('maintenance_agent', $monitoring->maintenance_agent) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="iv_access">Acesso IV</label>
                        <input type="text" name="iv_access" id="iv_access" class="form-control" value="{{ old('iv_access', $monitoring->iv_access) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="intubation_type">Intubação</label>
                        <input type="text" name="intubation_type" id="intubation_type" class="form-control" value="{{ old('intubation_type', $monitoring->intubation_type) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fluid_type">Tipo de Fluido</label>
                        <input type="text" name="fluid_type" id="fluid_type" class="form-control" value="{{ old('fluid_type', $monitoring->fluid_type) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fluid_rate">Taxa de Fluido (ml/kg/h)</label>
                        <input type="number" step="0.1" name="fluid_rate" id="fluid_rate" class="form-control" value="{{ old('fluid_rate', $monitoring->fluid_rate) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="observations">Observações</label>
                        <textarea name="observations" id="observations" rows="2" class="wysiwyg form-control">{{ old('observations', $monitoring->observations) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </form>
</div>
@endsection
