@extends('layouts.adminlte', ['title' => 'Gateway de Pagamento'])

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $paymentGateway->name }} ({{ strtoupper($paymentGateway->provider) }})</h3>
        <div class="card-tools">
            <a href="{{ route('payment-gateways.edit', $paymentGateway) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('payment-gateways.index') }}" class="btn btn-default btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Nome:</strong><p>{{ $paymentGateway->name }}</p></div>
            <div class="col-md-4"><strong>Provedor:</strong><p>{{ strtoupper($paymentGateway->provider) }}</p></div>
            <div class="col-md-4">
                <strong>Status:</strong><p>
                    @if($paymentGateway->is_active) <span class="badge badge-success">Ativo</span> @else <span class="badge badge-secondary">Inativo</span> @endif
                    @if($paymentGateway->is_sandbox) <span class="badge badge-warning">Sandbox</span> @endif
                </p>
            </div>
        </div>
        @if($paymentGateway->webhook_url)
        <div class="row mt-2">
            <div class="col-md-12"><strong>Webhook URL:</strong><p><code>{{ $paymentGateway->webhook_url }}</code></p></div>
        </div>
        @endif
    </div>
</div>
@endsection
