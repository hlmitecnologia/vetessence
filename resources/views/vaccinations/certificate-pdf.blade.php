<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Certificado de Vacinação</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; }
        .border-page { border: 3px double #000; padding: 1cm; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 2px; }
        .header h2 { font-size: 11px; margin: 0; font-weight: normal; }
        .header .crmv-seal { width: 70px; height: 70px; border: 2px solid #000; border-radius: 50%; margin: 8px auto; display: flex; align-items: center; justify-content: center; font-size: 8px; text-align: center; font-weight: bold; }
        .pet-info { width: 100%; margin: 10px 0; }
        .pet-info td { padding: 3px 8px; border: none; }
        .pet-info .label { font-weight: bold; width: 25%; }
        table.vax { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table.vax th, table.vax td { border: 1px solid #000; padding: 5px 6px; text-align: left; font-size: 10px; }
        table.vax th { background: #eaeaea; }
        .signature { margin-top: 35px; text-align: center; }
        .signature .line { border-top: 1px solid #000; width: 260px; margin: 0 auto 4px; padding-top: 6px; }
        .signature .name { font-weight: bold; }
        .signature .crmv { font-size: 9px; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; border-top: 1px solid #000; padding-top: 6px; color: #555; }
        .disclaimer { font-size: 8px; text-align: center; margin-top: 10px; color: #888; }
    </style>
</head>
<body>
    <div class="border-page">
        <div class="header">
            <div class="crmv-seal">
                CRMV<br>Conselho Regional<br>Medicina Veterinária
            </div>
            <h1>Certificado de Vacinação</h1>
            <h2>{{ config('app.name', 'Clínica Veterinária') }} — {{ config('app.cnpj', '') }}</h2>
            <p style="font-size: 9px; margin-top: 4px;">Emissão: {{ now()->format('d/m/Y') }}</p>
        </div>

        <table class="pet-info">
            <tr><td class="label">Pet:</td><td>{{ $pet->name }}</td><td class="label">Espécie:</td><td>{{ $pet->species ?? '-' }}</td></tr>
            <tr><td class="label">Raça:</td><td>{{ $pet->breed ?? 'SRD' }}</td><td class="label">Sexo:</td><td>{{ $pet->gender ?? '-' }}</td></tr>
            <tr><td class="label">Pelagem:</td><td>{{ $pet->coat ?? '-' }}</td><td class="label">Porte:</td><td>{{ $pet->size ?? '-' }}</td></tr>
            <tr><td class="label">Data Nasc.:</td><td>{{ $pet->birth_date ? $pet->birth_date->format('d/m/Y') : '-' }}</td><td class="label">Microchip:</td><td>{{ $pet->microchip_number ?? '-' }}</td></tr>
            <tr><td class="label">Tutor:</td><td colspan="3">{{ $pet->tutors->pluck('name')->join(', ') }}</td></tr>
        </table>

        @if($vaccinations->isEmpty())
            <p style="text-align: center; margin-top: 20px; padding: 10px; border: 1px solid #000;">
                Nenhuma vacina registrada para este pet.
            </p>
        @else
            <table class="vax">
                <thead>
                    <tr>
                        <th>Vacina</th>
                        <th>Data</th>
                        <th>Lote</th>
                        <th>Fabricante</th>
                        <th>Próx. Dose</th>
                        <th>Veterinário</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccinations as $vac)
                    <tr>
                        <td>{{ $vac->vaccine }}</td>
                        <td>{{ $vac->date instanceof \Carbon\Carbon ? $vac->date->format('d/m/Y') : $vac->date }}</td>
                        <td>{{ $vac->batch ?? $vac->lot ?? '-' }}</td>
                        <td>{{ $vac->manufacturer ?? '-' }}</td>
                        <td>{{ $vac->next_due_date ? ($vac->next_due_date instanceof \Carbon\Carbon ? $vac->next_due_date->format('d/m/Y') : $vac->next_due_date) : '-' }}</td>
                        <td>{{ $vac->vet->name ?? '-' }} {{ $vac->vet->crmv ? 'CRMV/'.$vac->vet->crmv : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="signature">
            <div class="line">
                <div class="name">{{ $vaccinations->first()->vet->name ?? 'Médico Veterinário' }}</div>
                <div class="crmv">CRMV: {{ $vaccinations->first()->vet->crmv ?? '___________________' }}</div>
            </div>
            <p style="font-size: 9px; margin-top: 2px;">Assinatura e Carimbo</p>
        </div>

        <div class="footer">
            <p>Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i:s') }} — {{ config('app.name', 'VetEssence') }}</p>
        </div>
        <div class="disclaimer">
            <p>Este certificado é válido apenas com assinatura e carimbo do veterinário responsável, nos termos da Resolução CFMV nº 957/2006.</p>
            <p>Consulte o CRMV de sua jurisdição para validação.</p>
        </div>
    </div>
</body>
</html>
