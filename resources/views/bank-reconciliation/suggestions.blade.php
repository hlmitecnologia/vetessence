@php $title = 'Sugestões de Conciliação - ' . $bankAccount->bank; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sugestões automáticas — {{ $bankAccount->bank }} ({{ $bankAccount->account }})</h3>
        <div class="card-tools">
            <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-sm btn-default"><i class="fas fa-list"></i> Todas as transações</a>
        </div>
    </div>
    <div class="card-body">
        @if(count($suggestions) > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Data Transação</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Fatura</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suggestions as $s)
                <tr>
                    <td data-order="{{ $s['transaction']->transaction_date->format('Y-m-d') }}">{{ $s['transaction']->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $s['transaction']->description }}</td>
                    <td>R$ {{ number_format($s['transaction']->amount, 2, ',', '.') }}</td>
                    <td><a href="{{ route('invoices.show', $s['invoice']) }}">#{{ $s['invoice']->invoice_number }}</a></td>
                    <td>
                        <form action="{{ route('bank-reconciliation.match', $s['transaction']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="invoice_id" value="{{ $s['invoice']->id }}">
                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Confirmar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma sugestão automática encontrada. <a href="{{ route('bank-reconciliation.index') }}">Visualizar transações</a> para conciliação manual.</p>
        @endif
    </div>
</div>
@endsection
