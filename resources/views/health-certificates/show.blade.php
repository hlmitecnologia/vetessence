@extends('layouts.adminlte', ['title' => 'Certificado Sanitário'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Certificado {{ $healthCertificate->certificate_number }}</h3>
        <div class="card-tools">
            <a href="{{ route('health-certificates.pdf', $healthCertificate) }}" class="btn btn-success btn-sm"><i class="fas fa-file-pdf"></i> Download PDF</a>
            <a href="{{ route('health-certificates.cvi-pdf', $healthCertificate) }}" class="btn btn-info btn-sm"><i class="fas fa-file-export"></i> Download CVI (PDF)</a>
            <a href="{{ route('health-certificates.edit', $healthCertificate) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('health-certificates.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Nº Certificado:</strong><p class="h4">{{ $healthCertificate->certificate_number }}</p></div>
            <div class="col-md-4"><strong>Pet:</strong><p>{{ $healthCertificate->pet->name ?? '-' }}</p></div>
            <div class="col-md-4"><strong>Tipo:</strong><p>{{ $healthCertificate->type }}</p></div>
        </div>
        <div class="row">
            <div class="col-md-3"><strong>Destino:</strong><p>{{ $healthCertificate->destination ?? '-' }}</p></div>
            <div class="col-md-3"><strong>Emissão:</strong><p>{{ $healthCertificate->issue_date->format('d/m/Y') }}</p></div>
            <div class="col-md-3"><strong>Validade:</strong><p>{{ $healthCertificate->expiration_date ? $healthCertificate->expiration_date->format('d/m/Y') : '-' }}</p></div>
            <div class="col-md-3"><strong>Veterinário:</strong><p>{{ $healthCertificate->issuerVet->name ?? '-' }}</p></div>
        </div>
        <div class="row">
            <div class="col-md-4"><strong>Status:</strong><p><span class="badge badge-{{ $healthCertificate->status == 'issued' ? 'success' : ($healthCertificate->status == 'expired' ? 'danger' : 'secondary') }}">{{ $healthCertificate->status }}</span></p></div>
            <div class="col-md-4"><strong>Exportação:</strong><p>{{ $healthCertificate->is_export ? 'Sim' : 'Não' }}</p></div>
            @if($healthCertificate->is_cvi)
            <div class="col-md-4"><strong>CVI:</strong><p><span class="badge badge-info">Certificado Veterinário Internacional</span> {{ $healthCertificate->cvi_number ? "nº {$healthCertificate->cvi_number}" : '' }}</p></div>
            @endif
            <div class="col-md-4"><strong>PDF Gerado em:</strong><p>{{ $healthCertificate->pdf_generated_at ? $healthCertificate->pdf_generated_at->format('d/m/Y H:i') : 'Ainda não' }}</p></div>
        </div>
        @if($healthCertificate->clinical_notes)
        <div class="row mt-3"><div class="col-md-12"><strong>Observações Clínicas:</strong><p>{!! $healthCertificate->clinical_notes !!}</p></div></div>
        @endif
        @if($healthCertificate->notes)
        <div class="row mt-3"><div class="col-md-12"><strong>Informações Adicionais:</strong><p>{!! $healthCertificate->notes !!}</p></div></div>
        @endif
    </div>
</div>
@endsection
