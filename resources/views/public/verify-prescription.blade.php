<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Verificação de Prescrição Veterinária</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #333;
            min-height: 100vh;
        }
        .header {
            background: #1a73e8;
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }
        .header h1 { font-size: 1.25rem; font-weight: 600; }
        .header p { font-size: 0.875rem; opacity: 0.9; margin-top: 0.25rem; }
        .container { max-width: 640px; margin: 0 auto; padding: 1rem; }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .field { margin-bottom: 0.75rem; }
        .field-label { font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: .5px; }
        .field-value { font-size: 1rem; color: #111; margin-top: 0.125rem; }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1rem;
        }
        .alert-success { background: #e6f4ea; color: #1e7e34; }
        .alert-danger { background: #fce8e6; color: #c5221f; }
        .alert-icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-valid { background: #e6f4ea; color: #1e7e34; }
        .badge-invalid { background: #fce8e6; color: #c5221f; }
        .clinic-logo {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 0.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-primary { background: #1a73e8; color: #fff; }
        .btn-primary:hover { background: #1557b0; }
        .btn-outline {
            background: transparent;
            color: #1a73e8;
            border: 1px solid #1a73e8;
        }
        .btn-outline:hover { background: #e8f0fe; }
        #scanner {
            width: 100%;
            max-width: 400px;
            margin: 1rem auto;
            border-radius: 12px;
            overflow: hidden;
        }
        #scanner video { border-radius: 12px; }
        .scanner-controls { text-align: center; margin-top: 0.75rem; }
        .footer {
            text-align: center;
            font-size: 0.75rem;
            color: #999;
            padding: 1.5rem;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ config('app.name', 'Clínica Veterinária') }}</h1>
    <p>Verificação Digital de Prescrição</p>
</div>

<div class="container">

    @if(isset($valid) && $valid)
        <div class="card" style="text-align: center;">
            <div class="alert alert-success">
                <div class="alert-icon">&#10003;</div>
                <strong>{{ $message }}</strong>
            </div>
            <span class="badge badge-valid">Prescrição Verificada</span>
        </div>

        <div class="card">
            <div class="card-title">Paciente</div>
            <div class="field">
                <div class="field-label">Pet</div>
                <div class="field-value">{{ $pet_name }}</div>
            </div>
            <div class="field">
                <div class="field-label">Tutor</div>
                <div class="field-value">{{ $tutor_name }}</div>
            </div>
            <div class="field">
                <div class="field-label">Data da Prescrição</div>
                <div class="field-value">{{ $created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Medicação Prescrita</div>
            <div class="field">
                <div class="field-label">Medicamento</div>
                <div class="field-value">{{ $medication }}</div>
            </div>
            <div class="field">
                <div class="field-label">Dosagem</div>
                <div class="field-value">{{ $dosage }}</div>
            </div>
            <div class="field">
                <div class="field-label">Frequência</div>
                <div class="field-value">{{ $frequency }}</div>
            </div>
            <div class="field">
                <div class="field-label">Duração</div>
                <div class="field-value">{{ $duration }}</div>
            </div>
            @if($instructions)
            <div class="field">
                <div class="field-label">Instruções</div>
                <div class="field-value">{{ $instructions }}</div>
            </div>
            @endif
        </div>

        <div class="card">
            <div class="card-title">Veterinário Responsável</div>
            <div class="field">
                <div class="field-label">Nome</div>
                <div class="field-value">{{ $vet_name }}</div>
            </div>
            <div class="field">
                <div class="field-label">CRM/V</div>
                <div class="field-value">{{ $crmv }}</div>
            </div>
            @if($signed_at)
            <div class="field">
                <div class="field-label">Assinada em</div>
                <div class="field-value">{{ $signed_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
        </div>
    @elseif(isset($valid) && !$valid)
        <div class="card" style="text-align: center;">
            <div class="alert alert-danger">
                <div class="alert-icon">&#10007;</div>
                <strong>{{ $message }}</strong>
            </div>
            <span class="badge badge-invalid">Prescrição Não Encontrada</span>
        </div>
    @endif

    <div class="card" style="text-align: center;">
        <div class="card-title">Escanear outra Prescrição</div>
        <p style="font-size: 0.875rem; color: #666; margin-bottom: 1rem;">
            Aponte a câmera para o QR Code da prescrição
        </p>
        <div id="scanner"></div>
        <div class="scanner-controls">
            <button id="startScanBtn" class="btn btn-primary">Abrir Câmera</button>
            <button id="stopScanBtn" class="btn btn-outline" style="display:none;">Parar</button>
        </div>
        <p style="margin-top: 0.75rem; font-size: 0.875rem;">
            <a href="#" style="color: #1a73e8;" onclick="event.preventDefault(); var h=prompt('Digite o código da prescrição:'); if(h) window.location.href='{{ url('/r') }}/'+h;">Digitar código manualmente</a>
        </p>
    </div>

</div>

<div class="footer">
    {{ config('app.name', 'VetEssence') }} — Documento verificado digitalmente
</div>

<script>
(function() {
    let html5QrCode = null;
    const scannerDiv = document.getElementById('scanner');
    const startBtn = document.getElementById('startScanBtn');
    const stopBtn = document.getElementById('stopScanBtn');

    function onScanSuccess(decodedText) {
        try {
            stopScanner();
            const url = new URL(decodedText);
            if (url.pathname.startsWith('/r/')) {
                window.location.href = decodedText;
                return;
            }
        } catch (_) {}
        const match = decodedText.match(/\/r\/([a-f0-9]+)/i);
        if (match) {
            window.location.href = '{{ url('/r') }}/' + match[1];
        } else {
            window.location.href = decodedText;
        }
    }

    function startScanner() {
        if (html5QrCode) {
            html5QrCode.resume();
            return;
        }
        html5QrCode = new Html5Qrcode("scanner");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess
        ).then(() => {
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
        }).catch(() => {
            alert('Não foi possível acessar a câmera. Verifique as permissões.');
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(() => {});
        }
        startBtn.style.display = 'inline-block';
        stopBtn.style.display = 'none';
    }

    startBtn.addEventListener('click', startScanner);
    stopBtn.addEventListener('click', stopScanner);
})();
</script>
</body>
</html>
