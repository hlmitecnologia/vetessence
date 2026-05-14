@extends('layouts.adminlte', ['title' => 'Relatório Mensal - Substâncias Controladas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatório Mensal de Substâncias Controladas</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.reports.annual') }}" class="btn btn-default btn-sm">
                <i class="fas fa-calendar-alt"></i> Relatório Anual
            </a>
            <a href="{{ route('controlled-substances.reports.export-csv') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('controlled-substances.reports.monthly') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="month">Mês</label>
                        <select name="month" id="month" class="form-control form-control-sm">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (request('month', date('m'))) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="year">Ano</label>
                        <select name="year" id="year" class="form-control form-control-sm">
                            @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                <option value="{{ $y }}" {{ (request('year', date('Y'))) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm form-control-sm">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @if($reportData->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Substância</th>
                    <th>Total Entradas</th>
                    <th>Total Saídas</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr>
                    <td>{{ $row->substance ?? $row->name }}</td>
                    <td>{{ $row->total_in }}</td>
                    <td>{{ $row->total_out }}</td>
                    <td>
                        <span class="badge {{ ($row->balance ?? ($row->total_in - $row->total_out)) >= 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $row->balance ?? ($row->total_in - $row->total_out) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum dado encontrado para o período selecionado.</p>
        @endif
    </div>
</div>
@endsection
