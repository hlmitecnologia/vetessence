@extends('layouts.adminlte', ['title' => 'Plano de Tratamento - ' . $plan->plan_number])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Plano de Tratamento - {{ $plan->plan_number }}</h3>
                <div class="card-tools">
                    @if($plan->status === 'pending_approval' && Gate::allows('treatment-plans.approve'))
                        <form action="{{ route('treatment-plans.approve', $plan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Aprovar
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('treatment-plans.edit', $plan) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('treatment-plans.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Pet:</strong>
                        <p>{{ $plan->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Tutor:</strong>
                        <p>{{ $plan->tutor->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Veterinário:</strong>
                        <p>{{ $plan->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $planStatusLabels = ['draft' => 'Rascunho', 'pending_approval' => 'Aguardando Aprovação', 'approved' => 'Aprovado', 'in_progress' => 'Em Andamento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                                $planStatusColors = ['draft' => 'secondary', 'pending_approval' => 'warning', 'approved' => 'success', 'in_progress' => 'info', 'completed' => 'primary', 'cancelled' => 'danger'];
                            @endphp
                            <span class="badge badge-{{ $planStatusColors[$plan->status] ?? 'secondary' }}">
                                {{ $planStatusLabels[$plan->status] ?? $plan->status }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($plan->title)
                <div class="mt-3">
                    <strong>Título:</strong>
                    <p>{{ $plan->title }}</p>
                </div>
                @endif

                @if($plan->description)
                <div class="mt-3">
                    <strong>Descrição:</strong>
                    <p>{!! $plan->description !!}</p>
                </div>
                @endif

                @if($plan->client_approved_at)
                <div class="mt-3">
                    <strong>Aprovado pelo Cliente em:</strong>
                    <p>{{ $plan->client_approved_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif

                @if($plan->client_notes)
                <div class="mt-3 p-3 bg-warning-light rounded">
                    <strong>Observações do Cliente:</strong>
                    <p>{!! $plan->client_notes !!}</p>
                </div>
                @endif

                @if($plan->vet_notes)
                <div class="mt-3 p-3 bg-info-light rounded">
                    <strong>Observações do Veterinário:</strong>
                    <p>{!! $plan->vet_notes !!}</p>
                </div>
                @endif

                <hr>

                <h5>Itens do Plano</h5>
                @if($plan->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Quantidade</th>
                                <th>Valor Unitário</th>
                                <th>Total</th>
                                <th>Autorizado</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plan->items as $item)
                            <tr>
                                <td>{!! $item->description !!}</td>
                                <td>{{ $item->category ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                                <td>
                                    @if($item->is_authorized)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Autorizado</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-times"></i> Pendente</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $item->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td colspan="4" class="text-right">Total Estimado:</td>
                                <td>R$ {{ number_format($plan->total_estimated, 2, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td colspan="4" class="text-right">Total Autorizado:</td>
                                <td>R$ {{ number_format($plan->total_authorized, 2, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Nenhum item adicionado ao plano.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
