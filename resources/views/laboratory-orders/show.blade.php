@extends('layouts.adminlte', ['title' => 'Pedido de Laboratório - ' . $order->order_number])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pedido de Laboratório - {{ $order->order_number }}</h3>
                <div class="card-tools">
                    <a href="{{ route('laboratory-orders.edit', $order) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('laboratory-orders.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Pet:</strong>
                        <p>{{ $order->pet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Veterinário:</strong>
                        <p>{{ $order->vet->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Laboratório:</strong>
                        <p>{{ $order->lab_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusLabels = ['requested' => 'Solicitado', 'collected' => 'Coletado', 'in_analysis' => 'Em Análise', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                                $statusColors = ['requested' => 'primary', 'collected' => 'info', 'in_analysis' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong>Data do Pedido:</strong> <p>{{ $order->order_date->format('d/m/Y') }}</p></div>
                    <div class="col-md-4"><strong>Data do Resultado:</strong> <p>{{ $order->result_date ? $order->result_date->format('d/m/Y') : '-' }}</p></div>
                </div>

                @if($order->notes)
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Observações:</strong>
                    <p class="mt-1">{!! $order->notes !!}</p>
                </div>
                @endif

                <hr>
                <h5>Exames / Testes</h5>
                @if($order->tests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Exame</th>
                                <th>Código</th>
                                <th>Resultado</th>
                                <th>Valor de Referência</th>
                                <th>Unidade</th>
                                <th>Status</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->tests as $test)
                            <tr class="{{ $test->is_abnormal ? 'table-danger' : '' }}">
                                <td><strong>{{ $test->test_name }}</strong></td>
                                <td>{{ $test->test_code ?? '-' }}</td>
                                <td class="font-weight-bold {{ $test->is_abnormal ? 'text-danger' : '' }}">
                                    {{ $test->result ?? '-' }}
                                    @if($test->is_abnormal)
                                        <i class="fas fa-exclamation-triangle text-danger ml-1" title="Anormal"></i>
                                    @endif
                                </td>
                                <td>{{ $test->reference_range ?? '-' }}</td>
                                <td>{{ $test->unit ?? '-' }}</td>
                                <td>
                                    @if($test->is_abnormal)
                                        <span class="badge badge-danger">Anormal</span>
                                    @elseif($test->result)
                                        <span class="badge badge-success">Normal</span>
                                    @else
                                        <span class="badge badge-secondary">Pendente</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 150px;">{{ $test->observations ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">Nenhum teste cadastrado neste pedido.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
