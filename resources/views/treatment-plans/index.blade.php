@extends('layouts.adminlte', ['title' => 'Planos de Tratamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Planos de Tratamento</h3>
        <div class="card-tools">
            <a href="{{ route('treatment-plans.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Plano
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($plans->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº do Plano</th>
                    <th>Pet</th>
                    <th>Título</th>
                    <th>Total Estimado</th>
                    <th>Total Autorizado</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                <tr>
                    <td><strong>{{ $plan->plan_number }}</strong></td>
                    <td>{{ $plan->pet->name ?? '-' }}</td>
                    <td>{{ $plan->title ?? '-' }}</td>
                    <td>R$ {{ number_format($plan->total_estimated, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($plan->total_authorized, 2, ',', '.') }}</td>
                    <td>
                        @php
                            $planStatusLabels = ['draft' => 'Rascunho', 'pending_approval' => 'Aguardando Aprovação', 'approved' => 'Aprovado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                            $planStatusColors = ['draft' => 'secondary', 'pending_approval' => 'warning', 'approved' => 'success', 'in_progress' => 'info', 'completed' => 'primary', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $planStatusColors[$plan->status] ?? 'secondary' }}">
                            {{ $planStatusLabels[$plan->status] ?? $plan->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('treatment-plans.show', $plan) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('treatment-plans.edit', $plan) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum plano de tratamento encontrado.</p>
        @endif
    </div>
</div>
@endsection
