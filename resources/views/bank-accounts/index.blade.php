@php $title = 'Contas Bancárias'; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contas Bancárias</h3>
        <div class="card-tools">
            @can('bank-reconciliation.create')
            <a href="{{ route('bank-accounts.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova</a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if($accounts->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Banco</th>
                    <th>Agência</th>
                    <th>Conta</th>
                    <th>Tipo</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th style="width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $acc)
                <tr>
                    <td><strong>{{ $acc->bank }}</strong></td>
                    <td>{{ $acc->agency }}</td>
                    <td>{{ $acc->account }}</td>
                    <td>{{ $acc->account_type === 'checking' ? 'Corrente' : 'Poupança' }}</td>
                    <td>{{ $acc->branch->name ?? '-' }}</td>
                    <td>
                        @if($acc->is_active)
                            <span class="badge badge-success">Ativa</span>
                        @else
                            <span class="badge badge-danger">Inativa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('bank-accounts.show', $acc) }}" class="btn btn-sm btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('bank-reconciliation.suggest', $acc) }}" class="btn btn-sm btn-success" title="Conciliar"><i class="fas fa-handshake"></i></a>
                        <a href="{{ route('bank-accounts.edit', $acc) }}" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhuma conta bancária cadastrada.</p>
        @endif
    </div>
</div>
@endsection
