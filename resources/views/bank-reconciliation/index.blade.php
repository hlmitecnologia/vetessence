@php $title = 'Conciliação Bancária'; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Transações Bancárias</h3>
        <div class="card-tools">
            <a href="{{ route('bank-accounts.index') }}" class="btn btn-sm btn-default"><i class="fas fa-university"></i> Contas</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <select name="bank_account_id" class="form-control form-control-sm mr-2">
                <option value="">Todas as contas</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(request('bank_account_id') == $acc->id)>{{ $acc->bank }} - {{ $acc->account }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">Todos os status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pendente</option>
                <option value="reconciled" @selected(request('status') === 'reconciled')>Conciliada</option>
                <option value="unmatched" @selected(request('status') === 'unmatched')>Não Correspondida</option>
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm mr-2" value="{{ request('date_from') }}" placeholder="De">
            <input type="date" name="date_to" class="form-control form-control-sm mr-2" value="{{ request('date_to') }}" placeholder="Até">
            <button type="submit" class="btn btn-sm btn-primary mr-2"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i> Limpar</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Conta</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Fatura</th>
                        <th width="180">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td data-order="{{ $tx->transaction_date->format('Y-m-d') }}">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                        <td>{{ $tx->bankAccount->bank ?? '-' }}</td>
                        <td>{!! $tx->description !!}</td>
                        <td class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($tx->amount, 2, ',', '.') }}
                        </td>
                        <td>{{ $tx->type === 'credit' ? 'Crédito' : 'Débito' }}</td>
                        <td>
                            @php $sColors = ['pending' => 'warning', 'reconciled' => 'success', 'unmatched' => 'secondary']; @endphp
                            <span class="badge badge-{{ $sColors[$tx->status] ?? 'secondary' }}">
                                {{ ['pending' => 'Pendente', 'reconciled' => 'Conciliada', 'unmatched' => 'Não Correspondida'][$tx->status] ?? $tx->status }}
                            </span>
                        </td>
                        <td>
                            @if($tx->invoice)
                                <a href="{{ route('invoices.show', $tx->invoice) }}">#{{ $tx->invoice->invoice_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($tx->status !== 'reconciled' && $tx->type === 'credit')
                            <form action="{{ route('bank-reconciliation.match', $tx) }}" method="POST" class="form-inline">
                                @csrf
                                <select name="invoice_id" class="form-control form-control-sm mr-1" required style="max-width: 100px;">
                                    <option value="">Fatura...</option>
                                    @foreach(\App\Models\Invoice::whereIn('status', ['paid','pending'])->where('total', '>', 0)->orderBy('invoice_number')->limit(50)->get() as $inv)
                                    <option value="{{ $inv->id }}">#{{ $inv->invoice_number }} - R$ {{ number_format($inv->total, 2, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success" title="Conciliar"><i class="fas fa-link"></i></button>
                            </form>
                            @elseif($tx->invoice)
                            <form action="{{ route('bank-reconciliation.unmatch', $tx) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning" title="Desfazer"><i class="fas fa-unlink"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">Nenhuma transação encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
