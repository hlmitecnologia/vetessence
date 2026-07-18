<?php

namespace App\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;

interface NfeProvider
{
    public function emitir(NfeConfig $config, Invoice $invoice): NfeResult;
    public function emitirNfce(NfeConfig $config, Invoice $invoice): NfeResult;
    public function emitirTransferencia(NfeConfig $config, array $data): NfeResult;
    public function consultar(NfeConfig $config, string $nfeNumber): NfeResult;
    public function consultarNfce(NfeConfig $config, string $nfceInvoiceId): NfeResult;
    public function cancelar(NfeConfig $config, string $nfeNumber, string $motivo, ?string $nfeKey = null): NfeResult;
}
