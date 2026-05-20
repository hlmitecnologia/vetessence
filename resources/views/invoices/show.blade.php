@extends('layouts.adminlte', ['title' => 'Fatura'])

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">{{ $invoice->invoice_number }}</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        @php
                            $statusColors = ['paid' => 'badge badge-success', 'pending' => 'badge badge-warning', 'overdue' => 'badge badge-danger', 'cancelled' => 'badge badge-secondary'];
                        @endphp
                        <span class="{{ $statusColors[$invoice->status] ?? 'badge badge-secondary' }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Tutor:</strong> {{ $invoice->tutor->name ?? '-' }}<br>
                        <strong>Pet:</strong> {{ $invoice->pet->name ?? '-' }}
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Data Emissão:</strong> {{ $invoice->created_at->format('d/m/Y') }}<br>
                        <strong>Vencimento:</strong> {{ $invoice->due_date->format('d/m/Y') }}
                    </div>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-right">Valor Unit.</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="text-right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <table class="table table-borderless">
                    <tr><td class="text-right">Subtotal:</td><td class="text-right">R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</td></tr>
                    @if($invoice->discount > 0)
                    <tr class="text-danger"><td class="text-right">Desconto:</td><td class="text-right">- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</td></tr>
                    @endif
                    <tr class="font-weight-bold"><td class="text-right">Total:</td><td class="text-right">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td></tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('invoices.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Voltar</a>
                @if($invoice->status !== 'paid')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        @if($invoice->status === 'pending' || $invoice->status === 'overdue')
        <div class="card card-success" id="pix-section">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-qrcode"></i> Pagamento via PIX</h3>
            </div>
            <div class="card-body text-center">
                <div id="pix-loading">
                    <button onclick="generatePix()" class="btn btn-success btn-lg">
                        <i class="fas fa-qrcode"></i> Gerar QR Code
                    </button>
                </div>
                <div id="pix-qrcode" class="d-none">
                    <img id="qrcode-image" src="" alt="QR Code PIX" class="mb-3" style="max-width: 200px;">
                    <p class="text-muted">Valor: <strong>R$ {{ number_format($invoice->total, 2, ',', '.') }}</strong></p>
                    <p class="text-muted" id="pix-expiration"></p>
                    <div class="bg-light p-2 rounded text-left" style="font-size: 10px; word-break: break-all;" id="pix-payload"></div>
                    <button onclick="copyPix()" class="btn btn-default btn-sm mt-2"><i class="fas fa-copy"></i> Copiar código</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registrar Pagamento</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.pay', $invoice) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <select name="payment_method" required class="form-control">
                            <option value="">Selecione...</option>
                            <option value="pix">PIX</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="transferencia">Transferência</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check"></i> Confirmar Pagamento</button>
                </form>
            </div>
        </div>
        @endif

        @if($invoice->status === 'paid')
        <div class="card card-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                <h4 class="mt-2">Pagamento Confirmado</h4>
                <p class="text-muted">{{ $invoice->paid_at->format('d/m/Y H:i') }}</p>
                <p class="text-muted">Método: {{ str_replace('_', ' ', ucfirst($invoice->payment_method ?? '-')) }}</p>
            </div>
        </div>
        @endif

        {{-- NFSe --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice"></i> NFSe</h3>
            </div>
            <div class="card-body text-center">
                @php
                    $nfseLabels = ['none' => 'Não emitida', 'pending' => 'Pendente', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                    $nfseColors = ['none' => 'secondary', 'pending' => 'warning', 'issued' => 'success', 'cancelled' => 'secondary'];
                @endphp
                <span class="badge badge-{{ $nfseColors[$invoice->nfse_status ?? 'none'] ?? 'secondary' }}" style="font-size: 14px;">
                    {{ $nfseLabels[$invoice->nfse_status ?? 'none'] ?? $invoice->nfse_status }}
                </span>

                @if($invoice->nfse_status === 'issued' && $invoice->nfseInvoice)
                <hr>
                <p class="mb-1"><strong>Nº NFSe:</strong> {{ $invoice->nfseInvoice->nfse_number ?? '-' }}</p>
                <p class="mb-1"><strong>Código:</strong> {{ $invoice->nfseInvoice->nfse_code ?? '-' }}</p>
                <p class="mb-1"><strong>RPS:</strong> {{ $invoice->nfseInvoice->rps_number ?? '-' }}</p>
                @if($invoice->nfseInvoice->verification_code)
                <p class="mb-1"><strong>Código Verificação:</strong> {{ $invoice->nfseInvoice->verification_code }}</p>
                @endif
                <hr>
                <div class="btn-group">
                    @if($invoice->nfseInvoice->nfse_url_xml)
                    <a href="{{ route('nfse.download-xml', $invoice->nfseInvoice) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fas fa-file-code"></i> XML
                    </a>
                    @endif
                    @if($invoice->nfseInvoice->nfse_url_pdf)
                    <a href="{{ route('nfse.download-pdf', $invoice->nfseInvoice) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    @endif
                </div>
                @endif

                @can('nfse.emit')
                @if(($invoice->nfse_status ?? 'none') === 'none' && $hasNfseConfig)
                <hr>
                <form action="{{ route('nfse.emitir', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-file-invoice"></i> Emitir NFSe
                    </button>
                </form>
                @endif
                @endcan

                @can('nfse.cancel')
                @if($invoice->nfse_status === 'issued' && $invoice->nfseInvoice && $invoice->nfseInvoice->issuance_date && $invoice->nfseInvoice->issuance_date->diffInHours(now()) <= 24)
                <hr>
                <form action="{{ route('nfse.cancelar', $invoice) }}" method="POST" onsubmit="return confirm('Confirmar cancelamento da NFSe?')">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Motivo do cancelamento" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-ban"></i> Cancelar NFSe
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPayload = '';

function generatePix() {
    const loading = document.getElementById('pix-loading');
    const qrcode = document.getElementById('pix-qrcode');
    
    loading.innerHTML = '<i class="fas fa-spinner fa-spin text-success" style="font-size: 24px;"></i>';
    
    fetch('{{ route('invoices.pix', $invoice) }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('qrcode-image').src = data.qrcode;
            document.getElementById('pix-payload').textContent = data.payload;
            document.getElementById('pix-expiration').textContent = 'Expira em: ' + new Date(data.expiration).toLocaleString('pt-BR');
            currentPayload = data.payload;
            
            loading.classList.add('d-none');
            qrcode.classList.remove('d-none');
        })
        .catch(error => {
            loading.innerHTML = '<p class="text-danger">Erro ao gerar QR Code</p>';
            console.error(error);
        });
}

function copyPix() {
    navigator.clipboard.writeText(currentPayload).then(() => {
        alert('Código PIX copiado!');
    });
}
</script>
@endpush
