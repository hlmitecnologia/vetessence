@extends('layouts.adminlte', ['title' => 'Relatório Anual - Substâncias Controladas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Relatório Anual de Substâncias Controladas</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.reports.monthly') }}" class="btn btn-default btn-sm">
                <i class="fas fa-calendar-day"></i> Relatório Mensal
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('controlled-substances.reports.annual') }}" class="mb-3">
            <div class="row">
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

        @if($logs->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Total Movimentações</th>
                    <th>Substâncias Movimentadas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $monthKey => $monthLogs)
                @php $monthNum = (int)$monthKey; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::create()->month($monthNum)->format('F') }}</td>
                    <td>{{ $monthLogs->count() }}</td>
                    <td>{{ $monthLogs->pluck('substance.name')->unique()->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum dado encontrado para o ano selecionado.</p>
        @endif
    </div>
</div>
@endsection
