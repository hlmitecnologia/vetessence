@php $title = 'Conta Bancária - ' . $bankAccount->bank; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ $bankAccount->bank }}</h3></div>
            <div class="card-body">
                <p><strong>Agência:</strong> {{ $bankAccount->agency }}</p>
                <p><strong>Conta:</strong> {{ $bankAccount->account }}</p>
                <p><strong>Tipo:</strong> {{ $bankAccount->account_type === 'checking' ? 'Corrente' : 'Poupança' }}</p>
                <p><strong>Unidade:</strong> {{ $bankAccount->branch->name ?? '-' }}</p>
                <p><strong>Descrição:</strong> {{ $bankAccount->description ?? '-' }}</p>
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
                <a href="{{ route('bank-reconciliation.suggest', $bankAccount) }}" class="btn btn-success"><i class="fas fa-handshake"></i> Conciliar</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Transações</h3></div>
            <div class="card-body">
                @if($bankAccount->transactions->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bankAccount->transactions as $tx)
                        <tr>
                            <td>{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td>{{ $tx->description }}</td>
                            <td class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($tx->amount, 2, ',', '.') }}
                            </td>
                            <td>{{ $tx->type === 'credit' ? 'Crédito' : 'Débito' }}</td>
                            <td>
                                @php $statusLabels = ['pending' => 'Pendente', 'reconciled' => 'Conciliada', 'unmatched' => 'Não Correspondida']; @endphp
                                <span class="badge badge-{{ $tx->status === 'reconciled' ? 'success' : ($tx->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ $statusLabels[$tx->status] ?? $tx->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-center text-muted">Nenhuma transação encontrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
