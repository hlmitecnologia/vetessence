@extends('layouts.adminlte', ['title' => 'Encaminhamento - ' . $referral->referral_number])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Encaminhamento - {{ $referral->referral_number }}</h3>
                <div class="card-tools">
                    <a href="{{ route('referrals.edit', $referral) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('referrals.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="referral-document p-4" style="border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="text-center mb-4">
                        <h4>ENCAMINHAMENTO VETERINÁRIO</h4>
                        <p class="text-muted">Nº {{ $referral->referral_number }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted text-uppercase">Clínica de Origem</h6>
                                <p class="mb-1"><strong>Clínica:</strong> {{ $referral->referring_clinic ?? 'VetEssence' }}</p>
                                <p class="mb-0"><strong>Veterinário:</strong> {{ $referral->referringVet->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted text-uppercase">Clínica de Destino</h6>
                                <p class="mb-1"><strong>Clínica:</strong> {{ $referral->receiving_clinic ?? '-' }}</p>
                                <p class="mb-0"><strong>Veterinário:</strong> {{ $referral->receivingVet->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Paciente</h6>
                        <p><strong>Nome:</strong> {{ $referral->pet->name ?? '-' }}</p>
                        @if($referral->pet)
                            <p><strong>Espécie:</strong> {{ $referral->pet->species ?? '-' }} | <strong>Raça:</strong> {{ $referral->pet->breed ?? 'SRD' }}</p>
                        @endif
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6>Motivo do Encaminhamento</h6>
                        <p>{{ $referral->reason ?? 'Não informado' }}</p>
                    </div>

                    @if($referral->clinical_history)
                    <div class="mb-4">
                        <h6>Histórico Clínico</h6>
                        <p>{!! $referral->clinical_history !!}</p>
                    </div>
                    @endif

                    @if($referral->requested_procedures)
                    <div class="mb-4">
                        <h6>Procedimentos Solicitados</h6>
                        <p>{{ $referral->requested_procedures }}</p>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Veterinário Responsável:</strong></p>
                            <div class="border-top pt-2 mt-4" style="width: 250px;">
                                <p class="mb-0">{{ $referral->referringVet->name ?? '___________________' }}</p>
                                <p class="text-muted small">CRM: _______________</p>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Data:</strong> {{ $referral->created_at->format('d/m/Y') }}</p>
                            @if($referral->completed_at)
                                <p><strong>Concluído em:</strong> {{ $referral->completed_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($referral->response_notes)
                    <hr>
                    <div class="mt-3 p-3 bg-success-light rounded">
                        <h6>Resposta do Destino</h6>
                        <p>{{ $referral->response_notes }}</p>
                    </div>
                    @endif

                    <hr>
                    <div class="text-center">
                        @php
                            $statusLabels = ['sent' => 'Enviado', 'received' => 'Recebido', 'in_progress' => 'Em Atendimento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                            $statusColors = ['sent' => 'primary', 'received' => 'info', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$referral->status] ?? 'secondary' }} p-2">
                            {{ $statusLabels[$referral->status] ?? $referral->status }}
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
