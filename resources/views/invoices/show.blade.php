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

                @if($invoice->appointments->isNotEmpty())
                <div class="mb-3">
                    <strong>Atendimentos:</strong>
                    <ul class="list-unstyled mb-0">
                        @foreach($invoice->appointments as $apt)
                        <li>
                            <a href="{{ route('appointments.show', $apt) }}" class="small">
                                #{{ $apt->id }} — {{ ucfirst($apt->type) }} em {{ $apt->date->format('d/m/Y') }}
                                (@if($apt->vet){{ $apt->vet->name }}@elseif($apt->creator){{ $apt->creator->name }}@endif)
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

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
                            <td>{!! $item->description !!}</td>
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
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="d-inline" data-confirm="Cancelar esta fatura?">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="fas fa-ban"></i> Cancelar</button>
                </form>
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

        {{-- PDV suspenso temporariamente
        @if($hasPdvGateway) ... @endif
        --}}

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
                <p class="text-muted">{{ $invoice->paid_at?->format('d/m/Y H:i') }}</p>
                <p class="text-muted">Método: {{ str_replace('_', ' ', ucfirst($invoice->payment_method ?? '-')) }}</p>
            </div>
        </div>
        @endif

        @canany(['nfse.emit', 'nfe.emit'])
        @if($invoice->status === 'paid' && ($invoice->items->where('item_type', 'service')->isNotEmpty() || $invoice->items->where('item_type', 'product')->isNotEmpty()))
        <button id="emitirNotaFiscalBtn" type="button" class="btn btn-success btn-block" onclick="emitirNotaFiscal(this)">
            <i class="fas fa-file-invoice"></i> Emitir Nota Fiscal
        </button>
        @endif
        @endcan

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

                @can('nfse.cancel')
                @if($invoice->nfse_status === 'issued' && $invoice->nfseInvoice && $invoice->nfseInvoice->issuance_date && $invoice->nfseInvoice->issuance_date->diffInHours(now()) <= 24)
                <hr>
                <form action="{{ route('nfse.cancelar', $invoice) }}" method="POST" data-confirm="Confirmar cancelamento da NFSe?">
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

        {{-- NFC-e --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-receipt"></i> NFC-e</h3>
            </div>
            <div class="card-body text-center">
                @php
                    $nfeLabels = ['none' => 'Não emitida', 'issuing' => 'Emitindo...', 'issued' => 'Emitida', 'cancelled' => 'Cancelada'];
                    $nfeColors = ['none' => 'secondary', 'issuing' => 'warning', 'issued' => 'success', 'cancelled' => 'secondary'];
                @endphp
                <span class="badge badge-{{ $nfeColors[$invoice->nfe_status ?? 'none'] ?? 'secondary' }}" style="font-size: 14px;">
                    {{ $nfeLabels[$invoice->nfe_status ?? 'none'] ?? $invoice->nfe_status }}
                </span>

                @if($invoice->nfe_status === 'issued' && $invoice->nfeInvoice)
                <hr>
                <p class="mb-1"><strong>Nº NFC-e:</strong> {{ $invoice->nfeInvoice->nfe_number ?? '-' }}</p>
                <p class="mb-1"><strong>Chave:</strong> {{ $invoice->nfeInvoice->nfe_key ?? '-' }}</p>
                <hr>
                <div class="btn-group">
                    @if($invoice->nfeInvoice->nfe_url_xml)
                    <a href="{{ route('nfce.download-xml', $invoice->nfeInvoice) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fas fa-file-code"></i> XML
                    </a>
                    @endif
                    @if($invoice->nfeInvoice->nfe_url_pdf)
                    <a href="{{ route('nfce.download-pdf', $invoice->nfeInvoice) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    @endif
                    @if($invoice->nfeInvoice->danfe_url)
                    <a href="{{ route('nfce.download-danfe', $invoice->nfeInvoice) }}" class="btn btn-sm btn-default" target="_blank">
                        <i class="fas fa-print"></i> DANFE
                    </a>
                    @endif
                </div>
                @endif

                @can('nfe.cancel')
                @if($invoice->nfe_status === 'issued' && $invoice->nfeInvoice && $invoice->nfeInvoice->issuance_date && $invoice->nfeInvoice->issuance_date->diffInHours(now()) <= 24)
                <hr>
                <form action="{{ route('nfe.cancelar', $invoice) }}" method="POST" data-confirm="Confirmar cancelamento da NFC-e?">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Motivo do cancelamento" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-ban"></i> Cancelar NFC-e
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
{{-- Loading overlay --}}
<div id="nfce-loading-overlay" class="d-none" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
    <div class="bg-white rounded p-4 text-center shadow-lg">
        <div class="spinner-border text-success mb-3" role="status" style="width:3rem;height:3rem;">
            <span class="sr-only">Emitindo...</span>
        </div>
        <h5 class="mb-1">Emitindo Nota Fiscal</h5>
        <p id="nfce-loading-status" class="text-muted mb-0">Aguardando autorização da SEFAZ...</p>
    </div>
</div>

@if($invoice->nfe_status === 'issuing' || $invoice->nfe_status === 'issued')
{{-- Polling para NFC-e issuing --}}
@if($invoice->nfe_status === 'issuing')
<input type="hidden" id="nfce-invoice-id" value="{{ $invoice->nfeInvoice?->nfe_number ?? '' }}">
<script>
document.addEventListener('DOMContentLoaded', function() {
    pollNfceStatus();
});
</script>
@endif
@endif

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

function emitirNotaFiscal(btn) {
    const overlay = document.getElementById('nfce-loading-overlay');
    overlay.classList.remove('d-none');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Emitindo...';

    fetch('{{ route('invoices.emitir-nota-fiscal', $invoice) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.issuing) {
            document.getElementById('nfce-loading-status').textContent = 'NFC-e enviada para SEFAZ. Aguardando autorização...';
            pollNfceStatus(data.nfceInvoiceId, overlay);
        } else if (data.success) {
            window.location.reload();
        } else {
            overlay.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-invoice"></i> Emitir Nota Fiscal';
            alert(data.message || 'Erro ao emitir nota fiscal.');
        }
    })
    .catch(() => {
        overlay.classList.add('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-invoice"></i> Emitir Nota Fiscal';
        alert('Erro de comunicação com o servidor.');
    });
}

function pollNfceStatus(invoiceId, overlay) {
    if (!overlay) overlay = document.getElementById('nfce-loading-overlay');

    function poll() {
        fetch('{{ route('nfce.consultar-status', $invoice) }}' + (invoiceId ? '/' + invoiceId : ''), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.issued) {
                document.getElementById('nfce-loading-status').textContent = 'NFC-e autorizada!';
                setTimeout(() => { window.location.reload(); }, 1000);
            } else if (data.issuing) {
                document.getElementById('nfce-loading-status').textContent = data.status || 'Aguardando autorização da SEFAZ...';
                setTimeout(poll, 3000);
            } else {
                overlay.classList.add('d-none');
                alert(data.message || 'Erro ao consultar NFC-e.');
            }
        })
        .catch(() => {
            document.getElementById('nfce-loading-status').textContent = 'Erro de conexão. Tentando novamente...';
            setTimeout(poll, 5000);
        });
    }

    setTimeout(poll, 2000);
}
</script>
@endpush
