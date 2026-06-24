@extends('layouts.adminlte', ['title' => 'Produtos Próximos ao Vencimento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-times"></i> Produtos Próximos ao Vencimento</h3>
        <div class="card-tools">
            <a href="{{ route('stock.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <label class="mr-2">Produtos vencendo em até</label>
            <select name="days" class="form-control mr-2" onchange="this.form.submit()">
                <option value="15" {{ $days == 15 ? 'selected' : '' }}>15 dias</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 dias</option>
                <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 dias</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 dias</option>
            </select>
        </form>

        @if($products->isEmpty())
            <p class="text-center text-muted">Nenhum produto próximo ao vencimento no período selecionado.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Lote</th>
                        <th>Validade</th>
                        <th>Dias Restantes</th>
                        <th>Estoque</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                    <tr>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td>{{ $item->batch_number ?? '-' }}</td>
                        <td>{{ $item->product->expiration_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $item->days_to_expiry <= 15 ? 'badge-danger' : ($item->days_to_expiry <= 30 ? 'badge-warning' : 'badge-info') }}">
                                {{ $item->days_to_expiry }} dias
                            </span>
                        </td>
                        <td>{{ $item->product->stock }}</td>
                        <td>R$ {{ number_format($item->total_value, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
