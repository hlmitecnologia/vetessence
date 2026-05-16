<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Prescrição - {{ $prescription->medicalRecord->pet->name ?? 'Pet' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 2cm;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            font-size: 10pt;
        }
        .section {
            margin-bottom: 1.5rem;
        }
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 0.25rem;
            margin-bottom: 0.75rem;
        }
        .info-row {
            display: flex;
            margin-bottom: 0.25rem;
        }
        .info-label {
            font-weight: bold;
            min-width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 0.5rem;
            text-align: left;
            font-size: 11pt;
        }
        table th {
            background: #f0f0f0;
        }
        .controlled-badge {
            display: inline-block;
            background: #dc2626;
            color: #fff;
            font-size: 9pt;
            font-weight: bold;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .controlled-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 48pt;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.12);
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 10px;
            z-index: -1;
        }
        .signature-area {
            margin-top: 3rem;
            padding-top: 1rem;
            text-align: center;
            border-top: 1px solid #000;
        }
        .signature-area .line {
            width: 300px;
            margin: 2rem auto 0.5rem;
        }
        .signature-area .name {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 9pt;
            margin-top: 2rem;
            color: #666;
        }
        @media print {
            body { padding: 1cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 1rem;">
        <button onclick="window.print()" style="padding: 0.5rem 1rem; cursor: pointer;">Imprimir</button>
    </div>

    <div class="header">
        <h1>{{ config('app.name', 'Clínica Veterinária') }}</h1>
        <p>{{ config('app.address', 'Endereço da Clínica') }} | Tel: {{ config('app.phone', '(XX) XXXX-XXXX') }}</p>
        <p>CNPJ: {{ config('app.cnpj', '00.000.000/0001-00') }}</p>
    </div>

    @if($prescription->is_controlled)
    <div class="controlled-watermark">SUBSTÂNCIA CONTROLADA</div>
    <div style="text-align: right; margin-bottom: 1rem;">
        <span class="controlled-badge">Substância Controlada - ANVISA</span>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Dados do Paciente</div>
        <div class="info-row">
            <span class="info-label">Pet:</span>
            <span>{{ $prescription->medicalRecord->pet->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tutor:</span>
            <span>{{ $prescription->medicalRecord->vet->name ?? $prescription->medicalRecord->pet->tutor->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Data:</span>
            <span>{{ $prescription->created_at->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Medicação Prescrita</div>
        <table>
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Dosagem</th>
                    <th>Frequência</th>
                    <th>Duração</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $prescription->medication }}</td>
                    <td>{{ $prescription->dosage }}</td>
                    <td>{{ $prescription->frequency }}</td>
                    <td>{{ $prescription->duration }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($prescription->instructions)
    <div class="section">
        <div class="section-title">Instruções</div>
        <p>{{ $prescription->instructions }}</p>
    </div>
    @endif

    @if($prescription->notes)
    <div class="section">
        <div class="section-title">Observações</div>
        <p>{{ $prescription->notes }}</p>
    </div>
    @endif

    <div class="signature-area">
        <p>_________________________________________________________</p>
        <p class="name">{{ $prescription->medicalRecord->vet->name ?? 'Médico Veterinário' }}</p>
        <p>CRM/V: {{ optional($prescription->medicalRecord->vet)->crmv ?? '___________________' }}</p>
        <p>Assinatura e Carimbo</p>
        @if($prescription->isSigned())
        <p style="font-size: 8pt; color: #666; margin-top: 0.5rem;">
            Assinatura digital: {{ $prescription->signed_at->format('d/m/Y H:i:s') }}
        </p>
        @endif
    </div>

    @php
        use Endroid\QrCode\QrCode;
        use Endroid\QrCode\Writer\PngWriter;
        if ($prescription->verification_hash) {
            $qrCode = new QrCode(route('prescriptions.verify', $prescription->verification_hash));
            $writer = new PngWriter();
            $qrResult = $writer->write($qrCode);
            $qrBase64 = base64_encode($qrResult->getString());
        }
    @endphp

    @if(isset($qrBase64))
    <div class="section" style="text-align: center; margin-top: 2rem;">
        <div class="section-title">Verificação Digital</div>
        <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code" style="width: 150px; height: 150px;">
        <p style="font-size: 9pt; margin-top: 0.5rem;">
            Escaneie para verificar a autenticidade<br>
            <span style="font-size: 8pt; color: #666;">
                {{ route('prescriptions.verify', $prescription->verification_hash) }}
            </span>
        </p>
    </div>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} - {{ config('app.name', 'VetEssence') }}</p>
        <p>Este documento é válido apenas com assinatura e carrimbo do profissional responsável.</p>
    </div>
</body>
</html>
