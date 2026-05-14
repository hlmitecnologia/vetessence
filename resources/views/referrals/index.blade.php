@extends('layouts.adminlte', ['title' => 'Encaminhamentos'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Encaminhamentos</h3>
        <div class="card-tools">
            <a href="{{ route('referrals.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Novo Encaminhamento
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($referrals->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nº Encaminhamento</th>
                    <th>Pet</th>
                    <th>Clínica Origem</th>
                    <th>Clínica Destino</th>
                    <th>Motivo</th>
                    <th>Status</th>
                    <th style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                <tr>
                    <td><strong>{{ $referral->referral_number }}</strong></td>
                    <td>{{ $referral->pet->name ?? '-' }}</td>
                    <td>{{ $referral->referring_clinic ?? ($referral->referringVet->name ?? '-') }}</td>
                    <td>{{ $referral->receiving_clinic ?? ($referral->receivingVet->name ?? '-') }}</td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $referral->reason ?? '-' }}</td>
                    <td>
                        @php
                            $statusLabels = ['sent' => 'Enviado', 'received' => 'Recebido', 'in_progress' => 'Em Atendimento', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                            $statusColors = ['sent' => 'primary', 'received' => 'info', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$referral->status] ?? 'secondary' }}">
                            {{ $statusLabels[$referral->status] ?? $referral->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('referrals.show', $referral) }}" class="btn btn-action btn-info" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('referrals.edit', $referral) }}" class="btn btn-action btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-center text-muted">Nenhum encaminhamento encontrado.</p>
        @endif
    </div>
</div>
@endsection
