@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-shopping-cart"></i> Pedido {{ $purchaseOrder->order_number }}</h4>
        <div>
            @if($purchaseOrder->status === 'draft' && auth()->user()->can('purchase-orders.edit'))
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir pedido?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="fas fa-trash"></i> Excluir</button>
                </form>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Informações</h5></div>
                <div class="card-body">
                    <p><strong>Fornecedor:</strong> {{ $purchaseOrder->supplier->name ?? '-' }}</p>
                    <p><strong>Solicitante:</strong> {{ $purchaseOrder->requester->name ?? '-' }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge badge-{{ $purchaseOrder->status === 'draft' ? 'secondary' : ($purchaseOrder->status === 'ordered' ? 'info' : 'success') }}">
                            {{ strtoupper($purchaseOrder->status) }}
                        </span>
                    </p>
                    <p><strong>Total:</strong> R$ {{ number_format($purchaseOrder->total, 2, ',', '.') }}</p>
                    @if($purchaseOrder->notes)<p><strong>Obs:</strong> {{ $purchaseOrder->notes }}</p>@endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Ações</h5></div>
                <div class="card-body">
                    @if($purchaseOrder->status === 'draft' && auth()->user()->can('purchase-orders.approve'))
                        <form action="{{ route('purchase-orders.order', $purchaseOrder) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-info" onclick="return confirm('Confirmar envio ao fornecedor?')">
                                <i class="fas fa-paper-plane"></i> Enviar Pedido
                            </button>
                        </form>
                    @endif
                    @if($purchaseOrder->status === 'ordered' && auth()->user()->can('purchase-orders.receive'))
                        <button class="btn btn-success" data-toggle="modal" data-target="#receiveModal">
                            <i class="fas fa-boxes"></i> Receber Pedido
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5>Itens</h5></div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead><tr><th>Produto</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th><th>Qtd Recebida</th></tr></thead>
                <tbody>
                    @foreach($purchaseOrder->items as $i)
                        <tr>
                            <td>{{ $i->product->name ?? '-' }}</td>
                            <td>{{ $i->quantity }}</td>
                            <td>R$ {{ number_format($i->unit_price, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($i->quantity * $i->unit_price, 2, ',', '.') }}</td>
                            <td>{{ $i->received_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($purchaseOrder->status === 'ordered')
        <div class="modal fade" id="receiveModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5>Receber Pedido</h5></div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead><tr><th>Produto</th><th>Qtd Pedida</th><th>Qtd Recebida</th></tr></thead>
                                <tbody>
                                    @foreach($purchaseOrder->items as $i)
                                        <tr>
                                            <td>{{ $i->product->name ?? '-' }}</td>
                                            <td>{{ $i->quantity }}</td>
                                            <td>
                                                <input type="number" name="items[{{ $loop->index }}][id]" value="{{ $i->id }}" hidden>
                                                <input type="number" name="items[{{ $loop->index }}][received_quantity]" class="form-control" value="{{ $i->quantity }}" step="0.01" min="0">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Confirmar Recebimento</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
