@extends('layouts.adminlte', ['title' => 'Internação - ' . ($hospitalization->pet->name ?? '')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Internação - {{ $hospitalization->pet->name ?? '' }}
                    @if($hospitalization->is_emergency)
                        <span class="badge badge-danger ml-2"><i class="fas fa-exclamation-triangle"></i> Emergência</span>
                    @endif
                </h3>
                <div class="card-tools">
                    <a href="{{ route('hospitalizations.edit', $hospitalization) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('hospitalizations.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Pet:</strong>
                        <p>{{ $hospitalization->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Tutor:</strong>
                        <p>{{ $hospitalization->tutor->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Veterinário:</strong>
                        <p>{{ $hospitalization->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusLabels = ['admitted' => 'Internado', 'discharged' => 'Alta', 'transferred' => 'Transferido'];
                                $statusColors = ['admitted' => 'primary', 'discharged' => 'success', 'transferred' => 'warning'];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$hospitalization->status] ?? 'secondary' }}">
                                {{ $statusLabels[$hospitalization->status] ?? $hospitalization->status }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <strong>Admissão:</strong>
                        <p>{{ $hospitalization->admission_date->format('d/m/Y') }} {{ $hospitalization->admission_time ?? '' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Departamento:</strong>
                        <p>{{ $hospitalization->department ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Leito:</strong>
                        <p>{{ $hospitalization->bed ?? '-' }}</p>
                    </div>
                    @if($hospitalization->discharged_at)
                    <div class="col-md-3">
                        <strong>Data da Alta:</strong>
                        <p>{{ $hospitalization->discharged_at->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>

                @if($hospitalization->admission_reason)
                <div class="mt-3">
                    <strong>Motivo da Internação:</strong>
                    <p>{!! $hospitalization->admission_reason !!}</p>
                </div>
                @endif

                @if($hospitalization->initial_diagnosis)
                <div class="mt-3">
                    <strong>Diagnóstico Inicial:</strong>
                    <p>{!! $hospitalization->initial_diagnosis !!}</p>
                </div>
                @endif

                @if($hospitalization->discharge_summary)
                <div class="mt-3 p-3 bg-success-light rounded">
                    <strong>Resumo de Alta:</strong>
                    <p>{!! $hospitalization->discharge_summary !!}</p>
                </div>
                @endif

                @if($hospitalization->discharge_instructions)
                <div class="mt-3 p-3 bg-info-light rounded">
                    <strong>Instruções de Alta:</strong>
                    <p>{!! $hospitalization->discharge_instructions !!}</p>
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
                <ul class="nav nav-tabs card-header-tabs" id="hospitalizationTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="daily-records-tab" data-toggle="tab" href="#daily-records" role="tab">
                            <i class="fas fa-clipboard-list"></i> Registros Diários
                            @if($hospitalization->dailyRecords->count() > 0)
                                <span class="badge badge-primary ml-1">{{ $hospitalization->dailyRecords->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fluid-therapy-tab" data-toggle="tab" href="#fluid-therapy" role="tab">
                            <i class="fas fa-syringe"></i> Fluidoterapia
                            @if($hospitalization->fluidTherapies->count() > 0)
                                <span class="badge badge-primary ml-1">{{ $hospitalization->fluidTherapies->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="prescriptions-tab" data-toggle="tab" href="#prescriptions" role="tab">
                            <i class="fas fa-prescription"></i> Prescrições
                            @if($hospitalization->prescriptions->count() > 0)
                                <span class="badge badge-primary ml-1">{{ $hospitalization->prescriptions->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="execution-tab" data-toggle="tab" href="#execution" role="tab">
                            <i class="fas fa-clipboard-check"></i> Execução
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="hospitalizationTabsContent">
                    <div class="tab-pane fade show active" id="daily-records" role="tabpanel">
                        @if($hospitalization->dailyRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Turno</th>
                                        <th>Profissional</th>
                                        <th>Subjetivo</th>
                                        <th>Temperatura</th>
                                        <th>FC</th>
                                        <th>FR</th>
                                        <th style="width: 80px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hospitalization->dailyRecords as $record)
                                    <tr>
                                        <td data-order="{{ $record->record_date->format('Y-m-d') }}">{{ $record->record_date->format('d/m/Y') }}</td>
                                        <td>
                                            @php $shiftLabels = ['morning' => 'Manhã', 'afternoon' => 'Tarde', 'night' => 'Noite']; @endphp
                                            {{ $shiftLabels[$record->shift] ?? $record->shift }}
                                        </td>
                                        <td>{{ $record->user->name ?? '-' }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">{{ $record->subjective ?? '-' }}</td>
                                        <td>{{ $record->temperature ? $record->temperature . '°C' : '-' }}</td>
                                        <td>{{ $record->heart_rate ?? '-' }}</td>
                                        <td>{{ $record->respiratory_rate ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-action btn-info" data-toggle="modal" data-target="#dailyRecordModal{{ $record->id }}" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-muted">Nenhum registro diário encontrado.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="fluid-therapy" role="tabpanel">
                        @if($hospitalization->fluidTherapies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fluido</th>
                                        <th>Taxa</th>
                                        <th>Volume</th>
                                        <th>Via</th>
                                        <th>Início</th>
                                        <th>Término</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hospitalization->fluidTherapies as $ft)
                                    <tr>
                                        <td>{{ $ft->fluid_type }}</td>
                                        <td>{{ $ft->rate ? $ft->rate . ' ml/kg/h' : '-' }}</td>
                                        <td>{{ $ft->volume ? $ft->volume . ' ml' : '-' }}</td>
                                        <td>{{ $ft->route ?? '-' }}</td>
                                        <td data-order="{{ $ft->start_time?->timestamp ?? 0 }}">{{ $ft->start_time ? $ft->start_time->format('d/m/Y H:i') : '-' }}</td>
                                        <td data-order="{{ $ft->end_time?->timestamp ?? 0 }}">{{ $ft->end_time ? $ft->end_time->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-muted">Nenhuma fluidoterapia registrada.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="prescriptions" role="tabpanel">
                        @if($hospitalization->prescriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Dosagem</th>
                                        <th>Frequência</th>
                                        <th>Via</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hospitalization->prescriptions as $rx)
                                    <tr>
                                        <td>{{ $rx->medication }}</td>
                                        <td>{{ $rx->dosage }} {{ $rx->unit }}</td>
                                        <td>{{ $rx->frequency }}</td>
                                        <td>{{ $rx->route ?? '-' }}</td>
                                        <td data-order="{{ $rx->start_date ? $rx->start_date->format('Y-m-d') : '' }}">{{ $rx->start_date ? $rx->start_date->format('d/m/Y') : '-' }}</td>
                                        <td data-order="{{ $rx->end_date ? $rx->end_date->format('Y-m-d') : '' }}">{{ $rx->end_date ? $rx->end_date->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @php
                                                $rxStatusLabels = ['active' => 'Ativa', 'discontinued' => 'Descontinuada', 'completed' => 'Concluída'];
                                                $rxStatusColors = ['active' => 'success', 'discontinued' => 'danger', 'completed' => 'info'];
                                            @endphp
                                            <span class="badge badge-{{ $rxStatusColors[$rx->status] ?? 'secondary' }}">
                                                {{ $rxStatusLabels[$rx->status] ?? $rx->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-muted">Nenhuma prescrição registrada.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="execution" role="tabpanel">
                        @livewire('execution-board', ['hospitalization' => $hospitalization])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($hospitalization->dailyRecords as $record)
<div class="modal fade" id="dailyRecordModal{{ $record->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registro Diário - {{ $record->record_date->format('d/m/Y') }} - {{ $shiftLabels[$record->shift] ?? $record->shift }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if($record->subjective)
                <div class="mb-3">
                    <strong>Subjetivo:</strong>
                    <p>{!! $record->subjective !!}</p>
                </div>
                @endif
                @if($record->objective)
                <div class="mb-3">
                    <strong>Objetivo:</strong>
                    <p>{!! $record->objective !!}</p>
                </div>
                @endif
                @if($record->assessment)
                <div class="mb-3">
                    <strong>Avaliação:</strong>
                    <p>{!! $record->assessment !!}</p>
                </div>
                @endif
                @if($record->plan)
                <div class="mb-3">
                    <strong>Plano:</strong>
                    <p>{!! $record->plan !!}</p>
                </div>
                @endif
                <div class="row">
                    @if($record->temperature)
                    <div class="col-md-4"><strong>Temperatura:</strong> {{ $record->temperature }}°C</div>
                    @endif
                    @if($record->heart_rate)
                    <div class="col-md-4"><strong>FC:</strong> {{ $record->heart_rate }} bpm</div>
                    @endif
                    @if($record->respiratory_rate)
                    <div class="col-md-4"><strong>FR:</strong> {{ $record->respiratory_rate }} mpm</div>
                    @endif
                </div>
                <div class="row mt-2">
                    @if($record->appetite)
                    <div class="col-md-3"><strong>Apetite:</strong> {{ ucfirst($record->appetite) }}</div>
                    @endif
                    @if($record->hydration)
                    <div class="col-md-3"><strong>Hidratação:</strong> {{ ucfirst($record->hydration) }}</div>
                    @endif
                    @if($record->urination)
                    <div class="col-md-3"><strong>Urição:</strong> {{ ucfirst($record->urination) }}</div>
                    @endif
                    @if($record->defecation)
                    <div class="col-md-3"><strong>Defecação:</strong> {{ ucfirst($record->defecation) }}</div>
                    @endif
                </div>
                @if($record->medications_given)
                <div class="mt-3">
                    <strong>Medicações Administradas:</strong>
                    <p>{!! $record->medications_given !!}</p>
                </div>
                @endif
                @if($record->observations)
                <div class="mt-3">
                    <strong>Observações:</strong>
                    <p>{!! $record->observations !!}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
