@extends('layouts.adminlte', ['title' => 'Log de Auditoria'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log de Auditoria</h3>
        <div class="card-tools">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Usuário:</dt>
                    <dd class="col-sm-8">{{ $auditLog->user->name ?? 'Sistema' }}</dd>

                    <dt class="col-sm-4">Ação:</dt>
                    <dd class="col-sm-8">
                        @php
                            $actionColors = ['create' => 'badge-success', 'update' => 'badge-info', 'delete' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $actionColors[$auditLog->action] ?? 'badge-secondary' }}">
                            {{ $auditLog->action }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Modelo:</dt>
                    <dd class="col-sm-8">{{ $auditLog->model_type }}</dd>

                    <dt class="col-sm-4">ID do Registro:</dt>
                    <dd class="col-sm-8">{{ $auditLog->model_id }}</dd>

                    <dt class="col-sm-4">IP:</dt>
                    <dd class="col-sm-8">{{ $auditLog->ip_address ?? '-' }}</dd>

                    <dt class="col-sm-4">Data:</dt>
                    <dd class="col-sm-8">{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Valores Antigos</h5>
                    </div>
                    <div class="card-body">
                        @if($auditLog->old_values)
                            <pre class="mb-0" style="max-height: 300px; overflow-y: auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted mb-0">Nenhum dado anterior.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Novos Valores</h5>
                    </div>
                    <div class="card-body">
                        @if($auditLog->new_values)
                            <pre class="mb-0" style="max-height: 300px; overflow-y: auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted mb-0">Nenhum dado novo.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
