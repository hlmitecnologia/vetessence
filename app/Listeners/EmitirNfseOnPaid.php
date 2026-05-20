<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Services\Nfse\NfseService;
use Illuminate\Support\Facades\Log;

class EmitirNfseOnPaid
{
    public function __construct(
        protected NfseService $nfseService,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;

        $config = $this->nfseService->getConfig($invoice->branch_id);

        if (!$config) {
            return;
        }

        $result = $this->nfseService->emitir($invoice);

        if (!$result->success) {
            Log::warning("Falha ao emitir NFSe automática para fatura #{$invoice->id}: {$result->errorMessage}");
        }
    }
}
