@extends('layouts.adminlte', ['title' => $tutor->name])

@section('content')
<div class="mb-3">
    <a href="{{ route('tutors.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar para lista
    </a>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white);">
                    <span class="font-weight-bold" style="font-size: 1.75rem; color: var(--brand-primary, #455e36);">
                        {{ substr($tutor->name, 0, 1) }}
                    </span>
                </div>
                <h5 class="font-weight-bold">{{ $tutor->name }}</h5>
                <p class="text-muted small">Tutor desde {{ $tutor->created_at->format('d/m/Y') }}</p>

                <hr>

                <div class="text-left">
                    <div class="mb-2">
                        <i class="fas fa-id-card text-muted mr-2" style="width: 18px;"></i>
                        <span>{{ $tutor->cpf }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted mr-2" style="width: 18px;"></i>
                        <span>{{ $tutor->phone }}</span>
                    </div>
                    @if($tutor->phone_secondary)
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted mr-2" style="width: 18px;"></i>
                        <span>{{ $tutor->phone_secondary }}</span>
                    </div>
                    @endif
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted mr-2" style="width: 18px;"></i>
                        <span>{{ $tutor->email }}</span>
                    </div>
                    @if($tutor->address)
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted mr-2" style="width: 18px;"></i>
                        <span>{{ $tutor->address }}{{ $tutor->city ? ', ' . $tutor->city . ' - ' . $tutor->state : '' }}</span>
                    </div>
                    @endif
                </div>

                <hr>

                <a href="{{ route('tutors.edit', $tutor) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-paw mr-2"></i>Pets</h5>
                <a href="{{ route('pets.create') }}?tutor_id={{ $tutor->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Novo Pet
                </a>
            </div>
            <div class="card-body">
                @if($tutor->pets->count() > 0)
                <div class="row">
                    @foreach($tutor->pets as $pet)
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('pets.show', $pet) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mr-3" style="width: 48px; height: 48px; background: color-mix(in srgb, var(--brand-primary, #455e36) 15%, white);">
                                    <i class="fas fa-paw" style="color: var(--brand-primary, #455e36);"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $pet->name }}</div>
                                    <small class="text-muted">{{ ucfirst($pet->species) }} - {{ $pet->breed ?? 'SRD' }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0 py-4">Nenhum pet cadastrado.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice mr-2"></i>Faturas</h5>
                <a href="{{ route('invoices.create') }}?tutor_id={{ $tutor->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Nova Fatura
                </a>
            </div>
            <div class="card-body p-0">
                @if($tutor->invoices->count() > 0)
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tutor->invoices->take(5) as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                            <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $statusLabels = ['paid' => 'Pago', 'pending' => 'Pendente', 'overdue' => 'Vencida', 'cancelled' => 'Cancelada'];
                                    $statusColors = ['paid' => 'badge-success', 'pending' => 'badge-warning', 'overdue' => 'badge-danger', 'cancelled' => 'badge-secondary'];
                                @endphp
                                <span class="badge {{ $statusColors[$invoice->status] ?? 'badge-secondary' }}">
                                    {{ $statusLabels[$invoice->status] ?? ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center mb-0 py-4">Nenhuma fatura registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
