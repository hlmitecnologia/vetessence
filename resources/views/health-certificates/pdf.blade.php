<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Certificado Sanitário - {{ $healthCertificate->certificate_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0 0 5px; }
        .header h2 { font-size: 14px; margin: 0; color: #555; font-weight: normal; }
        .cert-number { font-size: 16px; font-weight: bold; text-align: center; margin: 15px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td, .info-table th { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        .info-table th { background: #f5f5f5; width: 30%; }
        .notes { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #4f46e5; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature { margin-top: 50px; }
        .signature-line { border-top: 1px solid #333; width: 250px; margin: 0 auto; padding-top: 5px; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; }
        .badge-success { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CERTIFICADO SANITÁRIO</h1>
        <h2>Atestado de Saúde Animal</h2>
    </div>

    <div class="cert-number">
        Certificado nº {{ $healthCertificate->certificate_number }}
    </div>

    <table class="info-table">
        <tr>
            <th>Pet</th>
            <td>{{ $healthCertificate->pet->name ?? '-' }}</td>
            <th>Espécie</th>
            <td>{{ $healthCertificate->pet->species ?? '-' }}</td>
        </tr>
        <tr>
            <th>Raça</th>
            <td>{{ $healthCertificate->pet->breed ?? '-' }}</td>
            <th>Sexo</th>
            <td>{{ $healthCertificate->pet->gender ?? '-' }}</td>
        </tr>
        <tr>
            <th>Data Nascimento</th>
            <td>{{ $healthCertificate->pet->birth_date ? $healthCertificate->pet->birth_date->format('d/m/Y') : '-' }}</td>
            <th>Microchip</th>
            <td>{{ $healthCertificate->pet->microchip ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tutor</th>
            <td colspan="3">{{ $healthCertificate->pet->tutors->first()->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tipo do Certificado</th>
            <td>{{ $healthCertificate->type }}</td>
            <th>Destino</th>
            <td>{{ $healthCertificate->destination ?? '-' }}</td>
        </tr>
        <tr>
            <th>Data Emissão</th>
            <td>{{ $healthCertificate->issue_date->format('d/m/Y') }}</td>
            <th>Validade</th>
            <td>{{ $healthCertificate->expiration_date ? $healthCertificate->expiration_date->format('d/m/Y') : 'Indeterminada' }}</td>
        </tr>
        <tr>
            <th>Veterinário Emissor</th>
            <td colspan="3">{{ $healthCertificate->issuerVet->name ?? '-' }} (CRMV: {{ $healthCertificate->issuerVet->crmv ?? '-' }})</td>
        </tr>
    </table>

    @if($healthCertificate->clinical_notes)
    <div class="notes">
        <strong>Observações Clínicas:</strong><br>
        {{ $healthCertificate->clinical_notes }}
    </div>
    @endif

    @if($healthCertificate->is_export)
    <p style="color: #856404; background: #fff3cd; padding: 8px; border-radius: 3px;">
        <strong>Certificado para Exportação (CITES)</strong> — Este documento atende aos requisitos fitossanitários para trânsito internacional.
    </p>
    @endif

    <p style="margin-top: 20px; font-size: 11px;">
        Certifico que o animal acima identificado foi examinado e encontra-se aparentemente saudável,
        sem sinais de doenças infectocontagiosas passíveis de quarentena,
        estando apto para o fim a que se destina este documento.
    </p>

    <div class="signature">
        <div class="signature-line">
            {{ $healthCertificate->issuerVet->name ?? 'Veterinário' }}<br>
            CRMV: {{ $healthCertificate->issuerVet->crmv ?? '-' }}
        </div>
    </div>

    <div class="footer">
        Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }} - VetEssence Sistema de Gestão Veterinária
    </div>
</body>
</html>
