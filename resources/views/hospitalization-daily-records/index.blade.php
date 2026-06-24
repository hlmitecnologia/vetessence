@extends('layouts.adminlte', ['title' => 'Registros Diários'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registros Diários de Internação</h3>
        <div class="card-tools">
            <a href="{{ route('hospitalization-daily-records.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4">
                <select name="hospitalization_id" class="form-control">
                    <option value="">Todas as internações</option>
                    @foreach(\App\Models\Hospitalization::with('pet')->get() as $hosp)
                        <option value="{{ $hosp->id }}" {{ request('hospitalization_id') == $hosp->id ? 'selected' : '' }}>
                            {{ $hosp->pet->name ?? '#' . $hosp->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-default btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </form>

        @if($records->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Turno</th>
                    <th>Pet</th>
                    <th>Profissional</th>
                    <th>Temperatura</th>
                    <th>FC</th>
                    <th>FR</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td data-order="{{ $record->record_date->format('Y-m-d') }}">{{ $record->record_date->format('d/m/Y') }}</td>
                    <td>
                        @php $shiftLabels = ['morning' => 'Manhã', 'afternoon' => 'Tarde', 'night' => 'Noite']; @endphp
                        {{ $shiftLabels[$record->shift] ?? $record->shift }}
                    </td>
                    <td><strong>{{ $record->hospitalization->pet->name ?? '-' }}</strong></td>
                    <td>{{ $record->user->name ?? '-' }}</td>
                    <td>{{ $record->temperature ? $record->temperature . '°C' : '-' }}</td>
                    <td>{{ $record->heart_rate ?? '-' }}</td>
                    <td>{{ $record->respiratory_rate ?? '-' }}</td>
                    <td>
                        <a href="{{ route('hospitalization-daily-records.show', $record) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('hospitalization-daily-records.edit', $record) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $records->links() }}
        </div>
        @else
        <p class="text-center text-muted">Nenhum registro diário encontrado.</p>
        @endif
    </div>
</div>
@endsection
