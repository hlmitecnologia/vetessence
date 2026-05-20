@php $title = 'Comissão - ' . $commissionLog->user->name; @endphp
@extends('layouts.adminlte')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Detalhes da Comissão</h3></div>
            <div class="card-body">
                <p><strong>Veterinário:</strong> {{ $commissionLog->user->name ?? '-' }}</p>
                <p>
                    <strong>Fatura:</strong>
                    @if($commissionLog->invoice)
                    <a href="{{ route('invoices.show', $commissionLog->invoice) }}">#{{ $commissionLog->invoice->invoice_number }}</a>
                    @else
                    -
                    @endif
                </p>
                <p><strong>Descrição:</strong> {{ $commissionLog->description ?? '-' }}</p>
                <p><strong>Valor Base:</strong> R$ {{ number_format($commissionLog->base_value, 2, ',', '.') }}</p>
                <p><strong>Valor Comissão:</strong> R$ {{ number_format($commissionLog->commission_value, 2, ',', '.') }}</p>
                <p><strong>Status:</strong>
                    @if($commissionLog->status === 'paid')
                        <span class="badge badge-success">Pago em {{ $commissionLog->paid_at->format('d/m/Y') }}</span>
                    @else
                        <span class="badge badge-warning">Pendente</span>
                    @endif
                </p>
                @if($commissionLog->commissionRate)
                <p><strong>Taxa Aplicada:</strong>
                    {{ $commissionLog->commissionRate->rate_type === 'percentage' ? $commissionLog->commissionRate->rate_value . '%' : 'R$ ' . number_format($commissionLog->commissionRate->rate_value, 2, ',', '.') }}
                </p>
                @endif
                @if($commissionLog->status === 'pending')
                <form action="{{ route('commissions.mark-paid', $commissionLog) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Marcar como Pago</button>
                </form>
                @endif
                <a href="{{ route('commissions.index') }}" class="btn btn-secondary mt-2"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
@endsection
