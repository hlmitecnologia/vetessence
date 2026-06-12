@extends('layouts.adminlte', ['title' => 'Monitoramento Anestésico - ' . ($monitoring->pet->name ?? '')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Monitoramento Anestésico - {{ $monitoring->pet->name ?? '' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('anesthesia-monitorings.edit', $monitoring) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('anesthesia-monitorings.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Pet:</strong>
                        <p>{{ $monitoring->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Cirurgia:</strong>
                        <p>{{ $monitoring->surgery->surgery_type ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Anestesista:</strong>
                        <p>{{ $monitoring->anesthetist ?? $monitoring->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Duração:</strong>
                        <p>
                            @if($monitoring->monitoring_start && $monitoring->monitoring_end)
                                {{ $monitoring->monitoring_start->diffInMinutes($monitoring->monitoring_end) }} min
                            @else
                                Em andamento
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <strong>Protocolo Anestésico:</strong>
                        <p>{{ $monitoring->anesthetic_protocol ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Pré-medicação:</strong>
                        <p>{{ $monitoring->premedication ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Indução:</strong>
                        <p>{{ $monitoring->induction_agent ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <strong>Manutenção:</strong>
                        <p>{{ $monitoring->maintenance_agent ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Acesso IV:</strong>
                        <p>{{ $monitoring->iv_access ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Intubação:</strong>
                        <p>{{ $monitoring->intubation_type ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4">
                        <strong>Fluidoterapia:</strong>
                        <p>{{ $monitoring->fluid_type ?? '-' }} @if($monitoring->fluid_rate) - {{ $monitoring->fluid_rate }} ml/kg/h @endif</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Início:</strong>
                        <p>{{ $monitoring->monitoring_start ? $monitoring->monitoring_start->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <strong>Término:</strong>
                        <p>{{ $monitoring->monitoring_end ? $monitoring->monitoring_end->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>

                @if($monitoring->observations)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Observações:</strong>
                    <p class="mt-1">{!! $monitoring->observations !!}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat text-danger"></i> Sinais Vitais
                    <small class="text-muted ml-2">Registros em tempo real</small>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addVitalSignModal">
                        <i class="fas fa-plus"></i> Adicionar Registro
                    </button>
                </div>
            </div>
            <div class="card-body">
                @include('anesthesia-monitorings._vital_signs_table', ['vitalSigns' => $monitoring->vitalSigns])
            </div>
        </div>
    </div>
</div>

@can('create', App\Models\AnesthesiaVitalSign::class)
<div class="modal fade" id="addVitalSignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Novo Registro de Sinais Vitais</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="recorded_at">Horário *</label>
                                <input type="datetime-local" name="recorded_at" id="recorded_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="heart_rate">FC (bpm)</label>
                                <input type="number" name="heart_rate" id="heart_rate" class="form-control" placeholder="Ex: 120">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="respiratory_rate">FR (mpm)</label>
                                <input type="number" name="respiratory_rate" id="respiratory_rate" class="form-control" placeholder="Ex: 20">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="spo2">SpO₂ (%)</label>
                                <input type="number" name="spo2" id="spo2" class="form-control" placeholder="Ex: 98">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="etco2">ETCO₂ (mmHg)</label>
                                <input type="number" name="etco2" id="etco2" class="form-control" placeholder="Ex: 35">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="temperature">Temperatura (°C)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" class="form-control" placeholder="Ex: 38.0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="blood_pressure_systolic">PAS (mmHg)</label>
                                <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" class="form-control" placeholder="Ex: 120">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="blood_pressure_diastolic">PAD (mmHg)</label>
                                <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" class="form-control" placeholder="Ex: 80">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="blood_pressure_mean">PAM (mmHg)</label>
                                <input type="number" name="blood_pressure_mean" id="blood_pressure_mean" class="form-control" placeholder="Ex: 95">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="anesthetic_depth">Plano Anestésico</label>
                                <select name="anesthetic_depth" id="anesthetic_depth" class="form-control">
                                    <option value="">Selecione</option>
                                    @foreach(['superficial' => 'Superficial', 'moderado' => 'Moderado', 'profundo' => 'Profundo', 'muito_profundo' => 'Muito Profundo'] as $v => $l)
                                        <option value="{{ $v }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vaporizer_setting">Vaporizador (%)</label>
                                <input type="number" step="0.1" name="vaporizer_setting" id="vaporizer_setting" class="form-control" placeholder="Ex: 2.0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="observations">Observações</label>
                                <input type="text" name="observations" id="observations" class="form-control" placeholder="Intercorrências">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
