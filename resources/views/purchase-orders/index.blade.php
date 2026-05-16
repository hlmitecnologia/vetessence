@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-shopping-cart"></i> Pedidos de Compra</h4>
        @can('purchase-orders.create')
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Pedido</a>
        @endcan
    </div>
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Nº Pedido</th><th>Fornecedor</th><th>Solicitante</th><th>Status</th><th>Total</th><th>Data</th><th></th></tr></thead>
                <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td>{{ $o->order_number }}</td>
                            <td>{{ $o->supplier->name ?? '-' }}</td>
                            <td>{{ $o->requester->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $o->status === 'draft' ? 'secondary' : ($o->status === 'ordered' ? 'info' : 'success') }}">
                                    {{ strtoupper($o->status) }}
                                </span>
                            </td>
                            <td>R$ {{ number_format($o->total, 2, ',', '.') }}</td>
                            <td>{{ $o->created_at->format('d/m/Y') }}</td>
                            <td><a href="{{ route('purchase-orders.show', $o) }}" class="btn btn-sm btn-outline-info">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Nenhum pedido encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $orders->links() }}
</div>
@endsection
