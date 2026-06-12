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
                    <th>NFSe</th>
                    <th>NF-e</th>
                    <th style="width: 180px;">Ações</th>
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
                        @php
                            $nfseLabels = ['none' => '—', 'pending' => 'Pendente', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                            $nfseColors = ['none' => '', 'pending' => 'badge-warning', 'issued' => 'badge-success', 'cancelled' => 'badge-secondary'];
                        @endphp
                        @if($inv->nfse_status && $inv->nfse_status !== 'none')
                        <span class="badge {{ $nfseColors[$inv->nfse_status] ?? '' }}">{{ $nfseLabels[$inv->nfse_status] ?? $inv->nfse_status }}</span>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        @php
                            $nfeLabels = ['none' => '—', 'issuing' => 'Pendente', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                            $nfeColors = ['none' => '', 'issuing' => 'badge-warning', 'issued' => 'badge-success', 'cancelled' => 'badge-secondary'];
                        @endphp
                        @if($inv->nfe_status && $inv->nfe_status !== 'none')
                        <span class="badge {{ $nfeColors[$inv->nfe_status] ?? '' }}">{{ $nfeLabels[$inv->nfe_status] ?? $inv->nfe_status }}</span>
                        @else
                        —
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($inv->status !== 'paid')
                        <form action="{{ route('invoices.cancel', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar esta fatura?')">
                            @csrf
                            <button type="submit" class="btn btn-action btn-danger" title="Cancelar">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif
                        @if(auth()->user()->can('nfse.emit') || auth()->user()->can('nfe.emit'))
                        @if($inv->status === 'paid' && ($inv->has_services > 0 || $inv->has_products > 0) && ($inv->nfse_status === 'none' || $inv->nfe_status === 'none'))
                        <form action="{{ route('invoices.emitir-nota-fiscal', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Emitir nota(s) fiscal(is) para esta fatura?')">
                            @csrf
                            <button type="submit" class="btn btn-action btn-success" title="Emitir Nota Fiscal">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        </form>
                        @endif
                        @endif
                        @can('nfse.cancel')
                        @if($inv->nfse_status === 'issued' && $inv->nfseInvoice && $inv->nfseInvoice->issuance_date && $inv->nfseInvoice->issuance_date->diffInHours(now()) <= 24)
                        <form action="{{ route('nfse.cancelar', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar NFSe? Informe o motivo no campo abaixo.')">
                            @csrf
                            <input type="hidden" name="motivo" value="Cancelamento solicitado pelo usuário" required>
                            <button type="submit" class="btn btn-action btn-danger" title="Cancelar NFSe">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                        @can('nfe.cancel')
                        @if($inv->nfe_status === 'issued' && $inv->nfeInvoice && $inv->nfeInvoice->issuance_date && $inv->nfeInvoice->issuance_date->diffInHours(now()) <= 24)
                        <form action="{{ route('nfe.cancelar', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar NF-e? Informe o motivo no campo abaixo.')">
                            @csrf
                            <input type="hidden" name="motivo" value="Cancelamento solicitado pelo usuário" required>
                            <button type="submit" class="btn btn-action btn-danger" title="Cancelar NF-e">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
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