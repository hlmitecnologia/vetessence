@extends('layouts.adminlte', ['title' => $zoonoticDisease->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        @if($zoonoticDisease->is_notifiable)
        <div class="alert alert-danger d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-lg mr-3"></i>
            <div>
                <strong>Doença de Notificação Obrigatória</strong><br>
                <small>Esta doença deve ser notificada às autoridades sanitárias conforme legislação vigente.</small>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Categoria</small>
                        <p>
                            <span class="badge
                                @switch($zoonoticDisease->category)
                                    @case('viral') badge-danger @break
                                    @case('bacterial') badge-warning @break
                                    @case('parasitic') badge-info @break
                                    @case('fungal') badge-secondary @break
                                    @default badge-dark
                                @endswitch
                            ">{{ $zoonoticDisease->category_label }}</span>
                        </p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Agente Causador</small>
                        <p class="font-weight-bold">{{ $zoonoticDisease->causative_agent ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Período de Incubação</small>
                        <p>{{ $zoonoticDisease->incubation_period ?? '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Notificação Obrigatória</small>
                        <p>{{ $zoonoticDisease->is_notifiable ? 'Sim' : 'Não' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            @if($zoonoticDisease->species_affected)
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-paw mr-2"></i>Espécies Atingidas</h5>
                        <div>
                            @php
                                $speciesLabels = [
                                    'canine' => 'Cães', 'feline' => 'Gatos', 'equine' => 'Equinos',
                                    'bovine' => 'Bovinos', 'ovine' => 'Ovinos', 'caprine' => 'Caprinos',
                                    'swine' => 'Suínos', 'avian' => 'Aves', 'reptile' => 'Répteis',
                                    'rodents' => 'Roedores', 'wild_mammals' => 'Mamíferos Silvestres',
                                    'wild_canids' => 'Canídeos Silvestres', 'wild_felids' => 'Felídeos Silvestres',
                                    'non_human_primates' => 'Primatas Não Humanos',
                                    'wild_birds' => 'Aves Silvestres', 'wild_ungulates' => 'Ungulados Silvestres',
                                    'ferrets' => 'Furões', 'fish' => 'Peixes',
                                    'asinine' => 'Asininos', 'mule' => 'Muares',
                                    'human' => 'Humanos', 'psittacidae' => 'Psitacídeos',
                                    'birds' => 'Aves',
                                ];
                            @endphp
                            @foreach($zoonoticDisease->species_affected as $species)
                            <span class="badge badge-primary mr-1 mb-1">{{ $speciesLabels[$species] ?? $species }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-arrow-right mr-2"></i>Transmissão</h5>
                        <p>{{ $zoonoticDisease->transmission ?? 'Informação não disponível.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-dog mr-2"></i>Sintomas em Animais</h5>
                        <p>{!! $zoonoticDisease->animal_symptoms ?? 'Informação não disponível.' !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user mr-2"></i>Sintomas em Humanos</h5>
                        <p>{!! $zoonoticDisease->human_symptoms ?? 'Informação não disponível.' !!}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-shield-alt mr-2"></i>Prevenção</h5>
                        <p>{!! $zoonoticDisease->prevention ?? 'Informação não disponível.' !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-ambulance mr-2"></i>Tratamento</h5>
                        <p>{!! $zoonoticDisease->treatment ?? 'Informação não disponível.' !!}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($zoonoticDisease->notes)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-sticky-note mr-2"></i>Observações</h5>
                <p>{!! $zoonoticDisease->notes !!}</p>
            </div>
        </div>
        @endif

        @if($zoonoticDisease->medicalRecords->count() > 0)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-medical mr-2"></i>Registros em Prontuários ({{ $zoonoticDisease->medicalRecords->count() }})</h5>
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Pet</th>
                            <th>Confirmado / Suspeito</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zoonoticDisease->medicalRecords as $record)
                        <tr>
                            <td data-order="{{ $record->date->format('Y-m-d') }}">{{ $record->date->format('d/m/Y') }}</td>
                            <td><a href="{{ route('medical-records.show', $record) }}">{{ $record->pet->name ?? '-' }}</a></td>
                            <td>
                                @if($record->pivot->is_suspected)
                                    <span class="badge badge-warning">Suspeito</span>
                                @else
                                    <span class="badge badge-danger">Confirmado</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('zoonotic-diseases.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
            <a href="{{ route('zoonotic-diseases.edit', $zoonoticDisease) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i> Editar</a>
        </div>
    </div>
</div>
@endsection
