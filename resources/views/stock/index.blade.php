@extends('layouts.adminlte', ['title' => 'Dashboard de Estoque'])

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalProducts }}</h3>
                <p>Total de Produtos</p>
            </div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $lowStock }}</h3>
                <p>Abaixo do Mínimo</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $belowReorder }}</h3>
                <p>Abaixo do Ponto de Reposição</p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $expiringSoon }}</h3>
                <p>Vencendo em 30 dias</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-times"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $expired }}</h3>
                <p>Vencidos</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>R$ {{ number_format($totalStockValue, 2, ',', '.') }}</h3>
                <p>Valor Total em Estoque</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ações Rápidas</h3>
    </div>
    <div class="card-body">
        <a href="{{ route('stock.adjust') }}" class="btn btn-primary mr-2">
            <i class="fas fa-pen"></i> Ajustar Estoque
        </a>
        <a href="{{ route('stock.reorder-suggestions') }}" class="btn btn-warning mr-2">
            <i class="fas fa-shopping-cart"></i> Sugestão de Compra
        </a>
        <a href="{{ route('stock.expiring') }}" class="btn btn-info mr-2">
            <i class="fas fa-calendar-times"></i> Produtos Vencendo
        </a>
        <a href="{{ route('stock.movements') }}" class="btn btn-secondary">
            <i class="fas fa-exchange-alt"></i> Movimentações
        </a>
    </div>
</div>
@endsection
