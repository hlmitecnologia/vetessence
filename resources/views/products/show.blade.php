@extends('layouts.adminlte', ['title' => $product->name])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted text-uppercase">SKU</small>
                        <p class="font-monospace">{{ $product->sku }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Estoque</small>
                        <p class="{{ $product->isLowStock ? 'text-danger font-weight-bold' : '' }}">{{ $product->stock }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Preço Custo</small>
                        <p>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Preço Venda</small>
                        <p class="font-weight-bold">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Margem</small>
                        <p>{{ number_format($product->margin, 1) }}%</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Estoque Mín.</small>
                        <p>{{ $product->min_stock }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Validade</small>
                        <p>{{ $product->expiration_date ? $product->expiration_date->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase">Categoria</small>
                        <p>{{ $product->category->name ?? '-' }}</p>
                    </div>
                </div>
                @if($product->description)
                <hr>
                <small class="text-muted text-uppercase">Descrição</small>
                <p>{{ $product->description }}</p>
                @endif
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Voltar</a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit mr-1"></i>Editar</a>
        </div>
    </div>
</div>
@endsection
