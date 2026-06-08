@extends('layouts.adminlte', ['title' => 'NF-e Emitidas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">NF-e Emitidas</h3>
        <div class="card-tools">
            <a href="{{ route('nfe.config') }}" class="btn btn-info btn-sm">
                <i class="fas fa-cog"></i> Configurar
            </a>
            <a href="{{ route('nfe.export-form') }}" class="btn btn-default btn-sm">
                <i class="fas fa-download"></i> Exportar
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($nfeInvoices->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>NF-e</th>
                    <th>Fatura</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Emissão</th>
                    <th style="width: 200px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nfeInvoices as $nfe)
                <tr>
                    <td><strong>{{ $nfe->nfe_number ?? '-' }}</strong></td>
                    <td>
                        <a href="{{ route('invoices.show', $nfe->invoice) }}">
                            {{ $nfe->invoice->invoice_number ?? '-' }}
                        </a>
                    </td>
                    <td>{{ $nfe->branch->name ?? '-' }}</td>
                    <td>
                        @php
                            $statusColors = ['issued' => 'badge-success', 'issuing' => 'badge-warning', 'cancelled' => 'badge-secondary', 'error' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $statusColors[$nfe->status] ?? 'badge-secondary' }}">
                            {{ $nfe->status }}
                        </span>
                    </td>
                    <td>{{ $nfe->issuance_date ? $nfe->issuance_date->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('nfe.show', $nfe) }}" class="btn btn-sm btn-default">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($nfe->nfe_url_xml)
                        <a href="{{ route('nfe.download-xml', $nfe) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-file-code"></i>
                        </a>
                        @endif
                        @if($nfe->nfe_url_pdf)
                        <a href="{{ route('nfe.download-pdf', $nfe) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                        @if($nfe->danfe_url)
                        <a href="{{ route('nfe.download-danfe', $nfe) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-print"></i> DANFE
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $nfeInvoices->links() }}
        </div>
        @else
        <p class="text-muted">Nenhuma NF-e emitida ainda.</p>
        @endif
    </div>
</div>
@endsection
