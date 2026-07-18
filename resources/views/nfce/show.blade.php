@extends('layouts.adminlte', ['title' => 'NFC-e'])

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">NFC-e {{ $nfceInvoice->nfe_number ?? '#' . $nfceInvoice->id }}</h3>
                <div class="card-tools">
                    @php
                        $statusColors = ['issued' => 'badge-success', 'issuing' => 'badge-warning', 'cancelled' => 'badge-secondary', 'error' => 'badge-danger'];
                    @endphp
                    @php $statusLabels = ['issuing' => 'Emitindo', 'issued' => 'Emitida', 'cancelled' => 'Cancelada', 'error' => 'Erro']; @endphp
                    <span class="badge {{ $statusColors[$nfceInvoice->status] ?? 'badge-secondary' }}">
                        {{ $statusLabels[$nfceInvoice->status] ?? $nfceInvoice->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Fatura:</strong>
                        <a href="{{ route('invoices.show', $nfceInvoice->invoice) }}">
                            {{ $nfceInvoice->invoice->invoice_number ?? '-' }}
                        </a><br>
                        <strong>Tutor:</strong> {{ $nfceInvoice->invoice->tutor->name ?? '-' }}<br>
                        <strong>Unidade:</strong> {{ $nfceInvoice->branch->name ?? '-' }}
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Nº NFC-e:</strong> {{ $nfceInvoice->nfe_number ?? '-' }}<br>
                        <strong>Chave:</strong> {{ $nfceInvoice->nfe_key ?? '-' }}<br>
                        <strong>Emissão:</strong> {{ $nfceInvoice->issuance_date ? $nfceInvoice->issuance_date->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                @if($nfceInvoice->error_message)
                <div class="alert alert-danger">
                    <strong>Erro:</strong> {{ $nfceInvoice->error_message }}
                </div>
                @endif

                <hr>
                <h5>Documentos</h5>
                <div class="row mt-3">
                    @if($nfceInvoice->nfe_url_xml)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfce.download-xml', $nfceInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-file-code"></i> XML
                        </a>
                    </div>
                    @endif
                    @if($nfceInvoice->nfe_url_pdf)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfce.download-pdf', $nfceInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                    @endif
                    @if($nfceInvoice->danfe_url)
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('nfce.download-danfe', $nfceInvoice) }}" class="btn btn-default btn-block" target="_blank">
                            <i class="fas fa-print"></i> DANFE
                        </a>
                    </div>
                    @endif
                </div>

                <hr>
                <h5>Log da API</h5>
                @if($nfceInvoice->provider_response)
                    <pre style="max-height: 400px; overflow-y: auto; background: #f4f6f9; padding: 10px; border-radius: 4px; font-size: 12px;">{{ json_encode($nfceInvoice->provider_response, JSON_PRETTY_PRINT) }}</pre>
                @else
                    <p class="text-muted">Nenhum log disponível.</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('nfce.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection
