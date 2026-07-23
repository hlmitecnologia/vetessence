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
        <div class="row mt-2">
            <div class="col-md-4"><strong>Canal:</strong><p>
                @if($paymentGateway->channel === 'portal')
                    <span class="badge badge-info">Portal (pagamento online)</span>
                @elseif($paymentGateway->channel === 'pdv')
                    <span class="badge badge-primary">PDV (maquininha)</span>
                @else
                    <span class="badge badge-success">Ambos</span>
                @endif
            </p></div>
            @if($paymentGateway->provider === 'pix')
            <div class="col-md-4"><strong>Chave PIX:</strong><p>{{ $paymentGateway->public_key ?: '-' }}</p></div>
            @endif
            <div class="col-md-4"><strong>Unidade:</strong><p>{{ $paymentGateway->branch_id ? $paymentGateway->branch->name : 'Todas as unidades' }}</p></div>
        </div>
        @if($paymentGateway->provider === 'multicard')
        <div class="row mt-2">
            <div class="col-md-4"><strong>ID Dispositivo (PinPDV):</strong><p><code>{{ $paymentGateway->config['pinpdv_id'] ?? '-' }}</code></p></div>
            <div class="col-md-4"><strong>Ambiente:</strong><p>{{ ucfirst($paymentGateway->config['ambiente'] ?? '-') }}</p></div>
        </div>
        @endif
        @if($paymentGateway->config['url'] ?? false)
        <div class="row">
            <div class="col-md-12"><strong>URL (PIX dinâmico):</strong><p><code>{{ $paymentGateway->config['url'] }}</code></p></div>
        </div>
        @endif
        <div class="row mt-2">
            <div class="col-md-12">
                <strong>Webhook URL (configurar no provedor):</strong>
                <p><code id="webhook-url">{{ url('/api/payments/webhook/' . $paymentGateway->id) }}</code>
                    <button type="button" class="btn btn-xs btn-default" onclick="copyWebhookUrl()"><i class="fas fa-copy"></i></button>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyWebhookUrl() {
    const el = document.getElementById('webhook-url');
    navigator.clipboard.writeText(el.textContent.trim()).then(() => {
        alert('URL do webhook copiada!');
    });
}
</script>
@endpush
