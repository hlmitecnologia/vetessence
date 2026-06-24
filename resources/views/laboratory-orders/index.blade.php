@extends('layouts.adminlte', ['title' => 'Pedidos de Laboratório'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pedidos de Laboratório</h3>
        <div class="card-tools">
            <a href="{{ route('laboratory-orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Pedido
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº do Pedido</th>
                    <th>Pet</th>
                    <th>Laboratório</th>
                    <th>Data do Pedido</th>
                    <th>Resultado</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{{ $order->pet->name ?? '-' }}</td>
                    <td>{{ $order->lab_name ?? '-' }}</td>
                    <td data-order="{{ $order->order_date->format('Y-m-d') }}">{{ $order->order_date->format('d/m/Y') }}</td>
                    <td data-order="{{ $order->result_date ? $order->result_date->format('Y-m-d') : '' }}">{{ $order->result_date ? $order->result_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        @php
                            $statusLabels = ['requested' => 'Solicitado', 'collected' => 'Coletado', 'in_analysis' => 'Em Análise', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                            $statusColors = ['requested' => 'primary', 'collected' => 'info', 'in_analysis' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('laboratory-orders.show', $order) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('laboratory-orders.edit', $order) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum pedido de laboratório encontrado.</p>
        @endif
    </div>
</div>
@endsection
