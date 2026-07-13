<?php

namespace App\Services\Nfse;

use App\Models\NfseConfig;
use App\Models\Invoice;

interface NfseProvider
{
    public function emitir(NfseConfig $config, Invoice $invoice): NfseResult;

    public function consultar(NfseConfig $config, string $nfseNumber): NfseResult;

    public function cancelar(NfseConfig $config, string $nfseNumber, string $motivo, ?string $uuid = null): NfseResult;
}
