<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5><i class="fas fa-camera"></i> Escanear Código</h5></div>
            <div class="modal-body">
                <div id="scanner-reader" style="width:100%;"></div>
                <p class="text-center text-muted small mt-2">Aponte a câmera para o código de barras ou QR code.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let scannerInstance = null;
$('#scannerModal').on('shown.bs.modal', function () {
    if (!scannerInstance) {
        scannerInstance = new Html5Qrcode("scanner-reader");
    }
    scannerInstance.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 150 } },
        function(decodedText) {
            scannerInstance.stop();
            $('#scannerModal').modal('hide');
            Livewire.emit('scannedCode', decodedText);
        }
    );
});
$('#scannerModal').on('hidden.bs.modal', function () {
    if (scannerInstance) scannerInstance.stop();
});
</script>
@endpush
