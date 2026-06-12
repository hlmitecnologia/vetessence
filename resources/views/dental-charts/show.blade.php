@extends('layouts.adminlte', ['title' => 'Odontograma - ' . ($chart->pet->name ?? '')])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Odontograma - {{ $chart->pet->name ?? '' }} ({{ $chart->examination_date->format('d/m/Y') }})</h3>
                <div class="card-tools">
                    <a href="{{ route('dental-charts.edit', $chart) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('dental-charts.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Paciente:</strong>
                        <p>{{ $chart->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Veterinário:</strong>
                        <p>{{ $chart->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Data:</strong>
                        <p>{{ $chart->examination_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Procedimento:</strong>
                        <p>{{ $chart->procedure_type ?? '-' }}</p>
                    </div>
                </div>

                <hr>
                <h5>Grade Dentária</h5>

                @php
                    $quadrants = [
                        '1' => ['name' => 'Quadrante 1 (Superior Direito)', 'teeth' => [101, 102, 103, 104, 105, 106, 107, 108, 109, 110]],
                        '2' => ['name' => 'Quadrante 2 (Superior Esquerdo)', 'teeth' => [201, 202, 203, 204, 205, 206, 207, 208, 209, 210]],
                        '3' => ['name' => 'Quadrante 3 (Inferior Esquerdo)', 'teeth' => [301, 302, 303, 304, 305, 306, 307, 308, 309, 310]],
                        '4' => ['name' => 'Quadrante 4 (Inferior Direito)', 'teeth' => [401, 402, 403, 404, 405, 406, 407, 408, 409, 410]],
                    ];

                    $conditionsByTooth = $chart->conditions->groupBy(function($c) {
                        return $c->quadrant . '-' . $c->tooth_number;
                    });
                @endphp

                @foreach($quadrants as $qNum => $quadrant)
                <div class="mb-4">
                    <h6 class="text-muted">{{ $quadrant['name'] }}</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($quadrant['teeth'] as $tooth)
                            @php
                                $key = $qNum . '-' . $tooth;
                                $condition = $conditionsByTooth->get($key);
                                $hasCondition = $condition && $condition->count() > 0;
                                $severityColor = 'bg-light border';
                                if ($hasCondition) {
                                    $maxSeverity = $condition->max('severity');
                                    $severityColor = $maxSeverity >= 3 ? 'bg-danger text-white' : ($maxSeverity == 2 ? 'bg-warning' : 'bg-info');
                                }
                            @endphp
                            <div class="text-center p-2 rounded {{ $severityColor }}" style="width: 65px; cursor: pointer;"
                                 title="{{ $hasCondition ? $condition->first()->condition . ' (Severidade: ' . $condition->first()->severity . ')' : 'Hígido' }}"
                                 data-toggle="tooltip">
                                <strong>{{ $tooth }}</strong>
                                @if($hasCondition)
                                    <br><small>{{ $condition->first()->condition }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <hr>
                <h5>Condições Identificadas</h5>
                @if($chart->conditions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Dente</th>
                                <th>Quadrante</th>
                                <th>Condição</th>
                                <th>Severidade</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chart->conditions as $condition)
                            <tr>
                                <td><strong>{{ $condition->tooth_number }}</strong></td>
                                <td>{{ $condition->quadrant }}</td>
                                <td>{{ $condition->condition }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-circle {{ $i <= $condition->severity ? 'text-danger' : 'text-muted' }}" style="font-size: 10px;"></i>
                                    @endfor
                                    ({{ $condition->severity }}/5)
                                </td>
                                <td>{{ $condition->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Nenhuma condição registrada.</p>
                @endif

                @if($chart->tartar_index)
                <div class="row mt-4">
                    <div class="col-md-4"><strong>Índice de Tártaro:</strong> {{ $chart->tartar_index }}</div>
                    <div class="col-md-4"><strong>Índice de Gengivite:</strong> {{ $chart->gingivitis_index ?? '-' }}</div>
                    <div class="col-md-4"><strong>Halitose:</strong> {{ $chart->halitosis ? 'Sim' : 'Não' }}</div>
                </div>
                @endif

                @if($chart->general_notes)
                <div class="mt-4 p-3 bg-light rounded">
                    <strong>Observações Gerais:</strong>
                    <p class="mt-1">{!! $chart->general_notes !!}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
