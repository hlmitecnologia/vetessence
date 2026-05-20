@php
    $title = 'NFSe Emitidas';
@endphp
@extends('layouts.adminlte')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">NFSe Emitidas</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline mb-3">
            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">Todos</option>
                <option value="pending" @selected(request('status') === 'pending')>Pendente</option>
                <option value="issued" @selected(request('status') === 'issued')>Emitida</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm mr-2" value="{{ request('date_from') }}" placeholder="De">
            <input type="date" name="date_to" class="form-control form-control-sm mr-2" value="{{ request('date_to') }}" placeholder="Até">
            <button type="submit" class="btn btn-sm btn-primary mr-2"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('nfse.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i> Limpar</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nº NFSe</th>
                        <th>RPS</th>
                        <th>Fatura</th>
                        <th>Unidade</th>
                        <th>Status</th>
                        <th>Emissão</th>
                        <th width="180">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nfseInvoices as $nfse)
                    <tr>
                        <td>{{ $nfse->nfse_number ?? '-' }}</td>
                        <td>{{ $nfse->rps_number ?? '-' }}</td>
                        <td>
                            @if($nfse->invoice)
                            <a href="{{ route('invoices.show', $nfse->invoice) }}">#{{ $nfse->invoice_id }}</a>
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $nfse->branch->name ?? '-' }}</td>
                        <td>
                            @switch($nfse->status)
                                @case('issued') <span class="badge badge-success">Emitida</span> @break
                                @case('pending') <span class="badge badge-warning">Pendente</span> @break
                                @case('cancelled') <span class="badge badge-danger">Cancelada</span> @break
                                @default <span class="badge badge-secondary">{{ $nfse->status }}</span>
                            @endswitch
                        </td>
                        <td>{{ $nfse->issuance_date ? $nfse->issuance_date->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('nfse.show', $nfse) }}" class="btn btn-sm btn-info" title="Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($nfse->nfse_url_xml)
                            <a href="{{ route('nfse.download-xml', $nfse) }}" class="btn btn-sm btn-secondary" title="XML" target="_blank">
                                <i class="fas fa-file-code"></i>
                            </a>
                            @endif
                            @if($nfse->nfse_url_pdf)
                            <a href="{{ route('nfse.download-pdf', $nfse) }}" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">Nenhuma NFSe encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $nfseInvoices->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
