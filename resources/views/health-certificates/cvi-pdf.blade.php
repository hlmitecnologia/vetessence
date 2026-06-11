<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>CVI - {{ $healthCertificate->cvi_number ?? $healthCertificate->certificate_number }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; }
        .border-page { border: 3px double #000; padding: 1.2cm; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 3px; }
        .header h2 { font-size: 13px; margin: 0; font-weight: normal; }
        .header .crmv-seal { width: 80px; height: 80px; border: 2px solid #000; border-radius: 50%; margin: 10px auto; display: flex; align-items: center; justify-content: center; font-size: 9px; text-align: center; font-weight: bold; }
        .cvi-number { font-size: 14px; font-weight: bold; text-align: center; margin: 15px 0; border: 1px solid #000; padding: 8px; background: #f9f9f9; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table td, .info-table th { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        .info-table th { background: #eaeaea; width: 25%; font-size: 10px; }
        .section-title { font-weight: bold; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #000; margin: 20px 0 10px; padding-bottom: 3px; }
        .checklist { margin: 10px 0; }
        .checklist-item { padding: 3px 0; }
        .checklist-item .check { border: 1px solid #000; width: 12px; height: 12px; display: inline-block; margin-right: 6px; vertical-align: middle; }
        .checklist-item .checked { background: #000; }
        .declaration { margin: 20px 0; padding: 15px; border: 1px solid #000; font-size: 11px; line-height: 1.8; }
        .signature-area { margin-top: 40px; text-align: center; }
        .signature-line { border-top: 1px solid #000; width: 280px; margin: 0 auto 4px; padding-top: 8px; }
        .signature-line .name { font-weight: bold; }
        .signature-line .crmv { font-size: 10px; }
        .digital-signature { margin-top: 25px; border: 1px dashed #666; padding: 10px; font-size: 9px; color: #555; text-align: center; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; border-top: 1px solid #000; padding-top: 8px; }
        .watermark { position: fixed; top: 40%; left: 0; width: 100%; text-align: center; font-size: 40px; color: rgba(0,0,0,0.04); font-weight: bold; text-transform: uppercase; pointer-events: none; z-index: -1; transform: rotate(-20deg); letter-spacing: 8px; }
    </style>
</head>
<body>
    <div class="watermark">REPÚBLICA FEDERATIVA DO BRASIL</div>
    <div class="border-page">
        <div class="header">
            <div class="crmv-seal">
                CRMV<br>Conselho Regional<br>Medicina Veterinária
            </div>
            <h1>Certificado Veterinário Internacional</h1>
            <h2>CVI — {{ config('app.name', 'Clínica Veterinária') }}</h2>
            <p style="font-size: 10px; margin-top: 5px;">
                Emissão: {{ $healthCertificate->issue_date->format('d/m/Y') }}
                @if($healthCertificate->embarkation_date)
                    &nbsp;|&nbsp; Embarque: {{ $healthCertificate->embarkation_date->format('d/m/Y') }}
                @endif
                @if($healthCertificate->expiration_date)
                    &nbsp;|&nbsp; Validade: {{ $healthCertificate->expiration_date->format('d/m/Y') }}
                @endif
            </p>
        </div>

        <div class="cvi-number">
            CVI Nº {{ $healthCertificate->cvi_number ?? $healthCertificate->certificate_number }}
        </div>

        <div class="section-title">1. Identificação do Animal</div>
        <table class="info-table">
            <tr>
                <th>Nome</th>
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
                <th>Pelagem</th>
                <td>{{ $healthCertificate->pet->coat ?? '-' }}</td>
            </tr>
            <tr>
                <th>Porte</th>
                <td>{{ $healthCertificate->pet->size ?? '-' }}</td>
                <th>Microchip</th>
                <td>{{ $healthCertificate->pet->microchip_number ?? '-' }}</td>
            </tr>
            @if($healthCertificate->pet->microchip_date)
            <tr>
                <th>Data Microchip</th>
                <td>{{ $healthCertificate->pet->microchip_date->format('d/m/Y') }}</td>
                <th>RG Animal</th>
                <td>{{ $healthCertificate->pet->rg_number ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <div class="section-title">2. Tutor / Proprietário</div>
        <table class="info-table">
            <tr>
                <th>Nome</th>
                <td colspan="3">{{ $healthCertificate->pet->tutors->first()->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>CPF</th>
                <td>{{ $healthCertificate->pet->tutors->first()->cpf ?? '-' }}</td>
                <th>Telefone</th>
                <td>{{ $healthCertificate->pet->tutors->first()->phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Endereço</th>
                <td colspan="3">{{ $healthCertificate->pet->tutors->first()->address ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">3. Destino</div>
        <table class="info-table">
            <tr>
                <th>País de Destino</th>
                <td>{{ $healthCertificate->destination_country ?? $healthCertificate->destination ?? '-' }}</td>
                <th>Modo de Transporte</th>
                <td>{{ $healthCertificate->transport_mode ?? '-' }}</td>
            </tr>
            <tr>
                <th>Data de Embarque</th>
                <td>{{ $healthCertificate->embarkation_date ? $healthCertificate->embarkation_date->format('d/m/Y') : '-' }}</td>
                <th>Emitido por</th>
                <td>CRMV/{{ $healthCertificate->crmv_emitter ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">4. Requisitos Sanitários</div>
        <div class="checklist">
            @php
                $items = [
                    'Vacinado contra Raiva (validade dentro do período)',
                    'Vacinado contra Cinomose / Parvovirose',
                    'Teste de Leishmaniose negativo',
                    'Teste de Brucelose negativo (para reprodução)',
                    'Tratamento antiparasitário interno (30 dias)',
                    'Tratamento antiparasitário externo (15 dias)',
                    'Microchip implantado e legível',
                    'Identificação eletrônica compatível com ISO 11784/11785',
                    'Exame clínico geral sem alterações',
                ];
                $checklist = $healthCertificate->requirements_checklist ?? [];
            @endphp
            @foreach($items as $i => $item)
            <div class="checklist-item">
                <span class="check {{ in_array($item, $checklist) ? 'checked' : '' }}"></span>
                {{ $item }}
            </div>
            @endforeach
        </div>

        <div class="section-title">5. Declaração do Médico Veterinário</div>
        <div class="declaration">
            <p>Eu, <strong>{{ $healthCertificate->issuerVet->name ?? '____________________' }}</strong>,
            CRMV nº <strong>{{ $healthCertificate->issuerVet->crmv ?? $healthCertificate->crmv_emitter ?? '____________________' }}</strong>,
            declaro sob minha responsabilidade profissional que o animal acima identificado foi examinado
            e encontra-se clinicamente saudável, sem sinais de doenças infectocontagiosas,
            atendendo aos requisitos sanitários exigidos para o trânsito internacional
            conforme disposto na Resolução CFMV nº 974/2006 e demais legislações vigentes.</p>

            @if($healthCertificate->clinical_notes)
            <p style="margin-top: 10px;"><strong>Observações:</strong> {{ $healthCertificate->clinical_notes }}</p>
            @endif
        </div>

        <div class="signature-area">
            <div class="signature-line">
                <div class="name">{{ $healthCertificate->issuerVet->name ?? 'Médico Veterinário' }}</div>
                <div class="crmv">CRMV: {{ $healthCertificate->issuerVet->crmv ?? $healthCertificate->crmv_emitter ?? '-' }}</div>
            </div>
            <p style="font-size: 10px; margin-top: 3px;">Assinatura e Carimbo</p>
        </div>

        <div class="digital-signature">
            <strong>Assinatura Digital</strong><br>
            Documento assinado digitalmente em {{ now()->format('d/m/Y H:i:s') }}<br>
            Hash de verificação: {{ md5($healthCertificate->id . $healthCertificate->certificate_number . now()) }}<br>
            <span style="font-size: 8px;">Este documento eletrônico é válido nos termos da MP 2.200-2/2001</span>
        </div>

        <div class="footer">
            <p>{{ branding('clinic_name', config('app.name', 'VetEssence')) }} — Sistema de Gestão Veterinária</p>
            <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} — Consulte a validade junto ao CRMV de sua jurisdição</p>
        </div>
    </div>
</body>
</html>
