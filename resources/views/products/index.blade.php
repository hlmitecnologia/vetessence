@extends('layouts.adminlte', ['title' => 'Produtos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Produtos</h3>
        <div class="card-tools">
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Produto</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $prod)
                <tr>
                    <td><code>{{ $prod->sku }}</code></td>
                    <td>
                        <strong>{{ $prod->name }}</strong>
                        @if($prod->isLowStock)
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Baixo</span>
                        @endif
                    </td>
                    <td class="{{ $prod->isLowStock ? 'text-danger font-weight-bold' : '' }}">{{ $prod->stock }}</td>
                    <td>R$ {{ number_format($prod->sale_price, 2, ',', '.') }}</td>
                    <td>
                        @if($prod->is_active)
                            <span class="badge badge-success">Ativo</span>
                        @else
                            <span class="badge badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $prod) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('products.edit', $prod) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
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