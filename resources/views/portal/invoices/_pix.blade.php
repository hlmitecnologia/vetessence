<div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
    <h3 class="text-lg font-bold text-blue-800 mb-4">Pagamento via PIX</h3>
    <div class="mb-4">
        {!! $invoice->pix_code !!}
    </div>
    <button onclick="copyPix()"
        class="portal-btn bg-blue-600 hover:bg-blue-700 text-white font-semibold">
        <i class="fas fa-copy"></i>Copiar código PIX
    </button>
    <p class="text-base text-blue-600 mt-3">Escaneie o QR Code ou copie o código para pagar</p>
</div>
