@extends('layouts.adminlte', ['title' => 'Prontuários'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Prontuário Médico</h3>
        <div class="card-tools">
            <a href="{{ route('medical-records.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($records->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pet</th>
                    <th>Veterinário</th>
                    <th>Tipo</th>
                    <th>Diagnóstico</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td data-order="{{ $record->date->format('Y-m-d') }}">{{ $record->date->format('d/m/Y') }}</td>
                    <td><strong>{{ $record->pet->name ?? '-' }}</strong></td>
                    <td>{{ $record->vet->name ?? '-' }}</td>
                    <td>
                        @php $typeLabels = ['consulta' => 'Consulta', 'cirurgia' => 'Cirurgia', 'emergencia' => 'Emergência', 'vacina' => 'Vacina', 'retorno' => 'Retorno', 'exame' => 'Exame']; @endphp
                        <span class="badge badge-primary">{{ $typeLabels[$record->type] ?? $record->type }}</span>
                    </td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $record->diagnosis ?? '-' }}</td>
                    <td>
                        <a href="{{ route('medical-records.show', $record) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('medical-records.edit', $record) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum registro encontrado.</p>
        @endif
    </div>
</div>
@endsection