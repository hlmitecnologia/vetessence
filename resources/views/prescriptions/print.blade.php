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

    <div class="section">
        <div class="section-title">Dados do Paciente</div>
        <div class="info-row">
            <span class="info-label">Pet:</span>
            <span>{{ $prescription->medicalRecord->pet->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tutor:</span>
            <span>{{ $prescription->medicalRecord->user->name ?? $prescription->medicalRecord->pet->tutor->name ?? '-' }}</span>
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
        <p class="name">{{ $prescription->medicalRecord->user->name ?? 'Médico Veterinário' }}</p>
        <p>CRM/V: ___________________</p>
        <p>Assinatura e Carimbo</p>
    </div>

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} - {{ config('app.name', 'VetEssence') }}</p>
        <p>Este documento é válido apenas com assinatura e carimbo do profissional responsável.</p>
    </div>
</body>
</html>
