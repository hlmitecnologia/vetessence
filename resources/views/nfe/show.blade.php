@extends('layouts.adminlte', ['title' => 'NF-e'])

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">NF-e {{ $nfeInvoice->nfe_number ?? '#' . $nfeInvoice->id }}</h3>
                <div class="card-tools">
                    @php
                        $statusColors = ['issued' => 'badge-success', 'issuing' => 'badge-warning', 'cancelled' => 'badge-secondary', 'error' => 'badge-danger'];
                    @endphp
                    <span class="badge {{ $statusColors[$nfeInvoice->status] ?? 'badge-secondary' }}">
                        {{ $nfeInvoice->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Fatura:</strong>
                        <a href="{{ route('invoices.show', $nfeInvoice->invoice) }}">
                            {{ $nfeInvoice->invoice->invoice_number ?? '-' }}
                        </a><br>
                        <strong>Tutor:</strong> {{ $nfeInvoice->invoice->tutor->name ?? '-' }}<br>
                        <strong>Unidade:</strong> {{ $nfeInvoice->branch->name ?? '-' }}
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Nº NF-e:</strong> {{ $nfeInvoice->nfe_number ?? '-' }}<br>
                        <strong>Chave:</strong> {{ $nfeInvoice->nfe_key ?? '-' }}<br>
                        <strong>Emissão:</strong> {{ $nfeInvoice->issuance_date ? $nfeInvoice->issuance_date->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                @if($nfeInvoice->error_message)
                <div class="alert alert-danger">
                    <strong>Erro:</strong> {{ $nfeInvoice->error_message }}
                </div>
                @endif

                <hr>
                <h5>Documentos</h5>
                <div class="row mt-3">
                    @if($nfeInvoice->nfe_url_xml)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfe.download-xml', $nfeInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-file-code"></i> XML
                        </a>
                    </div>
                    @endif
                    @if($nfeInvoice->nfe_url_pdf)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfe.download-pdf', $nfeInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                    @endif
                    @if($nfeInvoice->danfe_url)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfe.download-danfe', $nfeInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-print"></i> DANFE
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('nfe.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection
