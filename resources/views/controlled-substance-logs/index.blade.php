@extends('layouts.adminlte', ['title' => 'Movimentações - Substâncias Controladas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Movimentações de Substâncias Controladas</h3>
        <div class="card-tools">
            <a href="{{ route('controlled-substances.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Substâncias
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="substance_filter">Substância</label>
                        <select name="substance_id" id="substance_filter" class="form-control" onchange="this.form.submit()">
                            <option value="">Todas as substâncias</option>
                            @foreach($substances as $s)
                                <option value="{{ $s->id }}" {{ request('substance_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="type_filter">Tipo</label>
                        <select name="type" id="type_filter" class="form-control" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Saída</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('controlled-substance-logs.index', ['substance' => request('substance_id') ?: 0]) }}" class="btn btn-default">Limpar Filtros</a>
                </div>
            </div>
        </form>

        @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Substância</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Saldo</th>
                        <th>Responsável</th>
                        <th>Testemunha</th>
                        <th>Motivo</th>
                        <th>Pet</th>
                        <th style="width: 80px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $log->substance->name ?? '-' }}</strong></td>
                        <td>
                            @if($log->type === 'in')
                                <span class="badge badge-success">Entrada</span>
                            @else
                                <span class="badge badge-danger">Saída</span>
                            @endif
                        </td>
                        <td class="font-weight-bold">{{ number_format($log->quantity, 2, ',', '.') }}</td>
                        <td>{{ number_format($log->balance_after, 2, ',', '.') }}</td>
                        <td>{{ $log->user->name ?? '-' }}</td>
                        <td>{{ $log->witness->name ?? '-' }}</td>
                        <td class="text-truncate" style="max-width: 150px;">{{ $log->reason ?? '-' }}</td>
                        <td>{{ $log->pet->name ?? '-' }}</td>
                        <td>
                            <form action="{{ route('controlled-substance-logs.destroy', $log) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Tem certeza?')" class="btn btn-action btn-danger" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
        @else
        <p class="text-center text-muted">Nenhuma movimentação encontrada.</p>
        @endif
    </div>
</div>
@endsection
