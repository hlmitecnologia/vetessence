@extends('layouts.adminlte', ['title' => 'Termo de Consentimento - ' . $consentForm->consent_number])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Termo de Consentimento - {{ $consentForm->consent_number }}</h3>
                <div class="card-tools">
                    <a href="{{ route('consent-forms.edit', $consentForm) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('consent-forms.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="consent-document p-4" style="border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="text-center mb-4">
                        <h4>TERMO DE CONSENTIMENTO LIVRE E ESCLARECIDO</h4>
                        <p class="text-muted">Nº {{ $consentForm->consent_number }}</p>
                    </div>

                    <div class="mb-4">
                        <p><strong>Paciente:</strong> {{ $consentForm->pet->name ?? '-' }}</p>
                        <p><strong>Tutor/Responsável:</strong> {{ $consentForm->tutor->name ?? '-' }}</p>
                        @if($consentForm->client_document)
                            <p><strong>CPF/RG:</strong> {{ $consentForm->client_document }}</p>
                        @endif
                        <p><strong>Médico Veterinário:</strong> {{ $consentForm->veterinarian->name ?? '-' }}</p>
                        <p><strong>Data:</strong> {{ $consentForm->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <hr>

                    <div class="consent-content mb-4">
                        @if($consentForm->template)
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($consentForm->template->content)) !!}
                            </div>
                        @endif

                        @if($consentForm->signed_content)
                            <div class="mt-3">
                                <strong>Conteúdo Personalizado:</strong>
                                <div class="p-3 bg-light rounded mt-1">
                                    {!! nl2br(e($consentForm->signed_content)) !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($consentForm->status === 'signed')
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-6 text-center">
                            <div class="border-top pt-2">
                                <p><strong>{{ $consentForm->client_name ?? $consentForm->tutor->name ?? '___________________' }}</strong></p>
                                <p class="text-muted small">Tutor/Responsável</p>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="border-top pt-2">
                                <p><strong>{{ $consentForm->veterinarian->name ?? '___________________' }}</strong></p>
                                <p class="text-muted small">Médico Veterinário</p>
                            </div>
                        </div>
                    </div>

                    @if($consentForm->witness)
                    <div class="row mt-3">
                        <div class="col-md-6 offset-md-3 text-center">
                            <div class="border-top pt-2">
                                <p><strong>{{ $consentForm->witness->name ?? '___________________' }}</strong></p>
                                <p class="text-muted small">Testemunha</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($consentForm->signed_at)
                    <div class="text-center mt-3">
                        <p class="text-muted">Assinado em: {{ $consentForm->signed_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @endif

                    @if($consentForm->notes)
                    <hr>
                    <div class="mt-3">
                        <strong>Observações:</strong>
                        <p>{!! $consentForm->notes !!}</p>
                    </div>
                    @endif

                    <hr>
                    <div class="text-center">
                        <span class="badge badge-{{ $consentForm->status === 'signed' ? 'success' : ($consentForm->status === 'cancelled' ? 'danger' : 'secondary') }} p-2">
                            {{ $consentForm->status === 'signed' ? 'ASSINADO' : ($consentForm->status === 'cancelled' ? 'CANCELADO' : 'RASCUNHO') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
