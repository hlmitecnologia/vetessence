@extends('layouts.adminlte', ['title' => 'NFC-e Emitidas'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">NFC-e Emitidas</h3>
        <div class="card-tools">
            <a href="{{ route('nf.config') }}" class="btn btn-info btn-sm">
                <i class="fas fa-cog"></i> Configurar
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($nfceInvoices->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>NFC-e</th>
                    <th>Fatura</th>
                    <th>Tutor</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Emissão</th>
                    <th style="width: 200px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nfceInvoices as $nfce)
                <tr>
                    <td><strong>{{ $nfce->nfe_number ?? '-' }}</strong></td>
                    <td>
                        <a href="{{ route('invoices.show', $nfce->invoice) }}">
                            {{ $nfce->invoice->invoice_number ?? '-' }}
                        </a>
                    </td>
                    <td>{{ $nfce->invoice->tutor->name ?? '-' }}</td>
                    <td>{{ $nfce->branch->name ?? '-' }}</td>
                    <td>
                        @php
                            $statusColors = ['issued' => 'badge-success', 'issuing' => 'badge-warning', 'cancelled' => 'badge-secondary', 'error' => 'badge-danger'];
                        @endphp
                        <span class="badge {{ $statusColors[$nfce->status] ?? 'badge-secondary' }}">
                            @php $statusLabels = ['issuing' => 'Emitindo', 'issued' => 'Emitida', 'cancelled' => 'Cancelada', 'error' => 'Erro']; @endphp
                            {{ $statusLabels[$nfce->status] ?? $nfce->status }}
                        </span>
                    </td>
                    <td>{{ $nfce->issuance_date ? $nfce->issuance_date->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('nfce.show', $nfce) }}" class="btn btn-sm btn-default">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($nfce->nfe_url_xml)
                        <a href="{{ route('nfce.download-xml', $nfce) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-file-code"></i>
                        </a>
                        @endif
                        @if($nfce->nfe_url_pdf)
                        <a href="{{ route('nfce.download-pdf', $nfce) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                        @if($nfce->danfe_url)
                        <a href="{{ route('nfce.download-danfe', $nfce) }}" class="btn btn-sm btn-default" target="_blank">
                            <i class="fas fa-print"></i> DANFE
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted">Nenhuma NFC-e emitida ainda.</p>
        @endif
    </div>
</div>
@endsection
