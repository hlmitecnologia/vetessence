@extends('layouts.adminlte', ['title' => 'Odontogramas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Odontogramas</h3>
        <div class="card-tools">
            <a href="{{ route('dental-charts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Odontograma
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($charts->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Data do Exame</th>
                    <th>Tipo de Procedimento</th>
                    <th>Veterinário</th>
                    <th>Índice Tártaro</th>
                    <th>Condições</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($charts as $chart)
                <tr>
                    <td><strong>{{ $chart->pet->name ?? '-' }}</strong></td>
                    <td>{{ $chart->examination_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $procLabels = ['cleaning' => 'Limpeza', 'extraction' => 'Extração', 'surgery' => 'Cirurgia', 'exam' => 'Exame', 'other' => 'Outro'];
                        @endphp
                        {{ $procLabels[$chart->procedure_type] ?? $chart->procedure_type }}
                    </td>
                    <td>{{ $chart->vet->name ?? '-' }}</td>
                    <td>{{ $chart->tartar_index ?? '-' }}</td>
                    <td>{{ $chart->conditions->count() }} registro(s)</td>
                    <td>
                        <a href="{{ route('dental-charts.show', $chart) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('dental-charts.edit', $chart) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum odontograma encontrado.</p>
        @endif
    </div>
</div>
@endsection
