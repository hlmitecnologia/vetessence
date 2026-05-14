@extends('layouts.adminlte', ['title' => 'Faturas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Faturas</h3>
        <div class="card-tools">
            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($invoices->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº Fatura</th>
                    <th>Tutor</th>
                    <th>Pet</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td><strong>{{ $inv->invoice_number }}</strong></td>
                    <td>{{ $inv->tutor->name ?? '-' }}</td>
                    <td>{{ $inv->pet->name ?? '-' }}</td>
                    <td>R$ {{ number_format($inv->total, 2, ',', '.') }}</td>
                    <td>{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $statusColors = ['pending' => 'badge-warning', 'paid' => 'badge-success', 'overdue' => 'badge-danger', 'cancelled' => 'badge-secondary'];
                            $statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'overdue' => 'Vencido', 'cancelled' => 'Cancelado'];
                        @endphp
                        <span class="badge {{ $statusColors[$inv->status] ?? 'badge-secondary' }}">{{ $statusLabels[$inv->status] ?? $inv->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-action btn-primary" title="Editar">
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