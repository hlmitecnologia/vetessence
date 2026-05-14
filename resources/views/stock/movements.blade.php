@extends('layouts.adminlte', ['title' => 'Movimentações de Estoque'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Movimentações de Estoque</h3>
    </div>
    <div class="card-body">
        @if($movements->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Qtd</th>
                    <th>Saldo</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $mov->product->name ?? '-' }}</strong></td>
                    <td>
                        @php
                            $typeColors = ['entry' => 'badge-success', 'exit' => 'badge-danger', 'adjustment' => 'badge-info', 'loss' => 'badge-dark', 'return' => 'badge-warning'];
                            $typeLabels = ['entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste', 'loss' => 'Perda', 'return' => 'Devolução'];
                        @endphp
                        <span class="badge {{ $typeColors[$mov->type] ?? 'badge-secondary' }}">{{ $typeLabels[$mov->type] ?? $mov->type }}</span>
                    </td>
                    <td class="{{ $mov->type === 'entry' ? 'text-success' : 'text-danger' }}">
                        {{ $mov->type === 'entry' ? '+' : '-' }}{{ abs($mov->quantity) }}
                    </td>
                    <td>{{ $mov->balance_after }}</td>
                    <td>{{ $mov->user->name ?? '-' }}</td>
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