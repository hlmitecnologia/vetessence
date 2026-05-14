<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado de Vacinas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header p { margin: 2px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #eee; }
        .info { margin-bottom: 10px; }
        .info td { border: none; padding: 2px 8px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Certificado de Vacinação</h1>
        <p>VetEssence - Sistema de Gestão Veterinária</p>
        <hr>
    </div>

    <table class="info">
        <tr><td><strong>Pet:</strong> {{ $pet->name }}</td><td><strong>Espécie:</strong> {{ $pet->species }}</td></tr>
        <tr><td><strong>Raça:</strong> {{ $pet->breed ?? 'SRD' }}</td><td><strong>Sexo:</strong> {{ $pet->gender ?? '-' }}</td></tr>
        <tr><td><strong>Responsável:</strong> {{ $pet->tutors->pluck('name')->join(', ') }}</td><td><strong>Data de emissão:</strong> {{ now()->format('d/m/Y') }}</td></tr>
    </table>

    @if($vaccinations->isEmpty())
        <p style="text-align: center; margin-top: 20px;">Nenhuma vacina registrada para este pet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Vacina</th>
                    <th>Data</th>
                    <th>Lote</th>
                    <th>Fabricante</th>
                    <th>Veterinário</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vaccinations as $vac)
                <tr>
                    <td>{{ $vac->vaccine }}</td>
                    <td>{{ $vac->date instanceof \Carbon\Carbon ? $vac->date->format('d/m/Y') : $vac->date }}</td>
                    <td>{{ $vac->batch ?? '-' }}</td>
                    <td>{{ $vac->manufacturer ?? '-' }}</td>
                    <td>{{ $vac->vet->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php
        $nextDose = $vaccinations->firstWhere('next_date', '!=', null);
    @endphp
    @if($nextDose)
        <p style="margin-top: 15px;"><strong>Próxima dose:</strong> {{ $nextDose->next_date instanceof \Carbon\Carbon ? $nextDose->next_date->format('d/m/Y') : $nextDose->next_date }}</p>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i') }} - VetEssence v{{ config('app.version', '1.0') }}</p>
    </div>
</body>
</html>
