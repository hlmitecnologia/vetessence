@extends('layouts.adminlte', ['title' => 'Sugestão de Reposição'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Sugestão de Reposição — Estoque Inteligente</h3>
        <div class="card-tools">
            <a href="{{ route('stock.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($suggestions->isEmpty())
            <p class="text-center text-muted">Nenhum produto abaixo do ponto de reposição no momento.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Fornecedor</th>
                        <th>Estoque</th>
                        <th>Consumo Médio</th>
                        <th>Ponto de Reposição</th>
                        <th>Lead Time (dias)</th>
                        <th>Qtd. Sugerida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suggestions as $item)
                    <tr>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td>{{ $item->supplier->name ?? '-' }}</td>
                        <td class="text-danger font-weight-bold">{{ $item->current_stock }}</td>
                        <td>{{ number_format($item->avg_daily_consumption, 2, ',', '.') }}/dia</td>
                        <td>{{ number_format($item->reorder_point, 2, ',', '.') }}</td>
                        <td>{{ $item->lead_time_days }}</td>
                        <td class="font-weight-bold text-success">{{ $item->suggested_quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
