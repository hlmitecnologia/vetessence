@extends('layouts.adminlte')
@section('content')
<div class="container-fluid">
    <h4><i class="fas fa-camera"></i> Scanner</h4>
    <div class="card">
        <div class="card-body text-center">
            <div id="scanner-reader" style="width:100%;max-width:400px;margin:0 auto;"></div>
            <p class="text-muted mt-2">Aponte a câmera para um código de barras ou QR code.</p>
            <div id="scan-result" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const reader = new Html5Qrcode("scanner-reader");
reader.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 150 } },
    function(code) {
        reader.stop();
        document.getElementById('scan-result').innerHTML =
            '<div class="alert alert-success">Código lido: <strong>' + code + '</strong></div>' +
            '<a href="{{ url('/products?barcode=') }}' + code + '" class="btn btn-primary">Buscar Produto</a> ' +
            '<a href="{{ url('/r/') }}/' + code + '" class="btn btn-info">Verificar Receita</a>';
    }
);
</script>
@endpush
