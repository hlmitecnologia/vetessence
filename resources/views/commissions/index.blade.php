@php $title = 'Comissões'; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Comissões</h3>
        <div class="card-tools">
            <a href="{{ route('commissions.rates') }}" class="btn btn-sm btn-default"><i class="fas fa-percent"></i> Taxas</a>
        </div>
    </div>
    <div class="card-body">
        @if($totals && $totals->total_base)
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <div class="info-box-content">
                        <span class="info-box-text">Base Total</span>
                        <span class="info-box-number">R$ {{ number_format($totals->total_base, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <div class="info-box-content">
                        <span class="info-box-text">Comissão Total</span>
                        <span class="info-box-number">R$ {{ number_format($totals->total_commission, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form method="GET" class="form-inline mb-3">
            <select name="user_id" class="form-control form-control-sm mr-2">
                <option value="">Todos os veterinários</option>
                @foreach($vets as $vet)
                <option value="{{ $vet->id }}" @selected(request('user_id') == $vet->id)>{{ $vet->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">Todos os status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pendente</option>
                <option value="paid" @selected(request('status') === 'paid')>Pago</option>
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm mr-2" value="{{ request('date_from') }}" placeholder="De">
            <input type="date" name="date_to" class="form-control form-control-sm mr-2" value="{{ request('date_to') }}" placeholder="Até">
            <button type="submit" class="btn btn-sm btn-primary mr-2"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('commissions.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i> Limpar</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Veterinário</th>
                        <th>Fatura</th>
                        <th>Descrição</th>
                        <th>Base</th>
                        <th>Comissão</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><strong>{{ $log->user->name ?? '-' }}</strong></td>
                        <td>
                            @if($log->invoice)
                            <a href="{{ route('invoices.show', $log->invoice) }}">#{{ $log->invoice->invoice_number }}</a>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>R$ {{ number_format($log->base_value, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($log->commission_value, 2, ',', '.') }}</td>
                        <td>
                            @if($log->status === 'paid')
                                <span class="badge badge-success">Pago</span>
                            @else
                                <span class="badge badge-warning">Pendente</span>
                            @endif
                        </td>
                        <td data-order="{{ $log->created_at->format('Y-m-d') }}">{{ $log->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('commissions.show', $log) }}" class="btn btn-sm btn-info" title="Detalhes"><i class="fas fa-eye"></i></a>
                            @if($log->status === 'pending')
                            <form action="{{ route('commissions.mark-paid', $log) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Pagar"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8">Nenhuma comissão encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
