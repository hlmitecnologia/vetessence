@php
    $title = "NFSe #{$nfseInvoice->nfse_number}";
@endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalhes da NFSe</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Número</th><td>{{ $nfseInvoice->nfse_number ?? '-' }}</td></tr>
                    <tr><th>Código</th><td>{{ $nfseInvoice->nfse_code ?? '-' }}</td></tr>
                    <tr><th>RPS</th><td>{{ $nfseInvoice->rps_number ?? '-' }}</td></tr>
                    <tr><th>Código de Verificação</th><td>{{ $nfseInvoice->verification_code ?? '-' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @switch($nfseInvoice->status)
                                @case('issued') <span class="badge badge-success">Emitida</span> @break
                                @case('pending') <span class="badge badge-warning">Pendente</span> @break
                                @case('cancelled') <span class="badge badge-danger">Cancelada</span> @break
                            @endswitch
                        </td>
                    </tr>
                    <tr><th>Data de Emissão</th><td>{{ $nfseInvoice->issuance_date ? $nfseInvoice->issuance_date->format('d/m/Y H:i:s') : '-' }}</td></tr>
                    @if($nfseInvoice->cancelled_at)
                    <tr><th>Cancelada em</th><td>{{ $nfseInvoice->cancelled_at->format('d/m/Y H:i:s') }}</td></tr>
                    @endif
                    <tr><th>Unidade</th><td>{{ $nfseInvoice->branch->name ?? '-' }}</td></tr>
                    <tr><th>Fatura</th>
                        <td>
                            @if($nfseInvoice->invoice)
                            <a href="{{ route('invoices.show', $nfseInvoice->invoice) }}">#{{ $nfseInvoice->invoice_id }} - R$ {{ number_format($nfseInvoice->invoice->total, 2, ',', '.') }}</a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr><th>Tutor</th><td>{{ $nfseInvoice->invoice?->tutor?->name ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="card-footer">
                @if($nfseInvoice->nfse_url_xml)
                <a href="{{ route('nfse.download-xml', $nfseInvoice) }}" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-file-code"></i> XML
                </a>
                @endif
                @if($nfseInvoice->nfse_url_pdf)
                <a href="{{ route('nfse.download-pdf', $nfseInvoice) }}" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                @endif
                <a href="{{ route('nfse.index') }}" class="btn btn-default">Voltar</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log da API</h3>
            </div>
            <div class="card-body">
                @if($nfseInvoice->provider_response)
                <pre class="text-sm" style="max-height: 400px; overflow-y: auto;">{{ json_encode(json_decode($nfseInvoice->provider_response), JSON_PRETTY_PRINT) }}</pre>
                @else
                <p class="text-muted">Nenhum log disponível.</p>
                @endif
            </div>
        </div>

        @if($nfseInvoice->error_message)
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title text-white">Erro</h3>
            </div>
            <div class="card-body">
                <p class="text-danger">{{ $nfseInvoice->error_message }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
