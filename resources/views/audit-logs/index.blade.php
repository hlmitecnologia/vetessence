@extends('layouts.adminlte', ['title' => 'Logs de Auditoria'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Logs de Auditoria</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="action">Ação</label>
                        <select name="action" id="action" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Criar</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Atualizar</option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Excluir</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="model_type">Modelo</label>
                        <input type="text" name="model_type" id="model_type" class="form-control form-control-sm" placeholder="Ex: App\Models\Pet" value="{{ request('model_type') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">Data Início</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">Data Fim</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="{{ route('audit-logs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-times"></i> Limpar
            </a>
        </form>

        @if($logs->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Modelo</th>
                    <th>ID</th>
                    <th>Data</th>
                    <th style="width: 80px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    <td>
                        @php
                            $actionColors = ['create' => 'badge-success', 'update' => 'badge-info', 'delete' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $actionColors[$log->action] ?? 'badge-secondary' }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td>{{ class_basename($log->model_type) }}</td>
                    <td>{{ $log->model_id }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i> Ver
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
