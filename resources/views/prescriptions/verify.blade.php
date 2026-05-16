<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="utf-8"><title>Verificação de Prescrição</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Verificação de Prescrição Veterinária</h3>
                </div>
                <div class="card-body text-center">
                    @if($valid)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle fa-3x"></i>
                            <p class="mt-2">{{ $message }}</p>
                        </div>
                        <ul class="list-group text-left">
                            <li class="list-group-item"><strong>Pet:</strong> {{ $pet_name }}</li>
                            <li class="list-group-item"><strong>Medicação:</strong> {{ $medication }}</li>
                            <li class="list-group-item"><strong>Dosagem:</strong> {{ $dosage }}</li>
                            @if($signed_at)
                            <li class="list-group-item"><strong>Assinada em:</strong> {{ $signed_at->format('d/m/Y H:i') }}</li>
                            @endif
                        </ul>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle fa-3x"></i>
                            <p class="mt-2">{{ $message }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
