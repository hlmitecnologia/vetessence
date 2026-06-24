<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Fatura #{{ $invoice->id }} - {{ branding('clinic_name', config('app.name', 'VetEssence')) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            padding: 2cm;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 20pt;
            color: #333;
            margin-bottom: 0.25rem;
        }
        .header .clinic-info {
            font-size: 9pt;
            color: #555;
        }
        .invoice-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .section {
            margin-bottom: 1.5rem;
        }
        .section-title {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 0.25rem;
            margin-bottom: 0.75rem;
        }
        .info-row {
            display: flex;
            margin-bottom: 0.25rem;
        }
        .info-label {
            font-weight: bold;
            min-width: 140px;
            font-size: 10pt;
        }
        .info-value {
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        table th, table td {
            border: 1px solid #333;
            padding: 0.5rem;
            text-align: left;
            font-size: 10pt;
        }
        table th {
            background: #eaeaea;
            font-weight: bold;
        }
        table td.text-right {
            text-align: right;
        }
        .totals {
            margin-top: 1rem;
            text-align: right;
        }
        .totals .total-row {
            font-size: 11pt;
            padding: 0.25rem 0;
        }
        .totals .grand-total {
            font-size: 14pt;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            border: 1px solid #333;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 2rem;
            border-top: 1px solid #ccc;
            padding-top: 1rem;
        }
        @media print {
            body { padding: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ branding('clinic_name', config('app.name', 'VetEssence')) }}</h1>
        <div class="clinic-info">
            {{ branding('clinic_address', '') }}<br>
            CNPJ: {{ branding('clinic_cnpj', '') }} | Tel: {{ branding('clinic_phone', '') }}
        </div>
    </div>

    <div class="invoice-title">Fatura #{{ $invoice->invoice_number ?? $invoice->id }}</div>

    <div class="section">
        <div class="section-title">Dados do Cliente</div>
        <div class="info-row">
            <span class="info-label">Tutor:</span>
            <span class="info-value">{{ $invoice->tutor->name ?? '-' }}</span>
        </div>
        @if($invoice->pet)
        <div class="info-row">
            <span class="info-label">Pet:</span>
            <span class="info-value">{{ $invoice->pet->name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Data de emissão:</span>
            <span class="info-value">{{ $invoice->created_at->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vencimento:</span>
            <span class="info-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                {{ $invoice->status === 'paid' ? 'Pago' : ($invoice->status === 'pending' ? 'Pendente' : ($invoice->status === 'overdue' ? 'Vencido' : 'Cancelado')) }}
            </span>
        </div>
    </div>

    @if($invoice->items->isNotEmpty())
    <div class="section">
        <div class="section-title">Itens</div>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Valor Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="totals">
        @if($invoice->subtotal > 0)
        <div class="total-row">Subtotal: R$ {{ number_format($invoice->subtotal, 2, ',', '.') }}</div>
        @endif
        @if($invoice->discount > 0)
        <div class="total-row">Desconto: R$ {{ number_format($invoice->discount, 2, ',', '.') }}</div>
        @endif
        <div class="grand-total">Total: R$ {{ number_format($invoice->total, 2, ',', '.') }}</div>
    </div>

    @if($invoice->paid_at)
    <div class="section" style="margin-top: 2rem;">
        <div class="section-title">Pagamento</div>
        <div class="info-row">
            <span class="info-label">Data do pagamento:</span>
            <span class="info-value">{{ $invoice->paid_at->format('d/m/Y') }}</span>
        </div>
        @if($invoice->payment_method)
        <div class="info-row">
            <span class="info-label">Forma de pagamento:</span>
            <span class="info-value">{{ $invoice->payment_method }}</span>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>{{ branding('clinic_name', config('app.name', 'VetEssence')) }} - Documento gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
