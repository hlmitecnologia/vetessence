@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-exchange-alt"></i> Transferir Estoque</h4>
    <div class="card">
        <form action="{{ route('stock.transfer') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Produto</label>
                    <select name="product_id" class="form-control" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (estoque: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                    <label>Origem</label>
                    <select name="from_branch_id" class="form-control" required>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Destino</label>
                    <select name="to_branch_id" class="form-control" required>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Transferir</button>
                <a href="{{ route('stock.movements') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
