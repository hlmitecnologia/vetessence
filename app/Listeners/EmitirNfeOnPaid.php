<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Services\Nfe\NfeService;
use Illuminate\Support\Facades\Log;

class EmitirNfeOnPaid
{
    public function __construct(
        protected NfeService $nfeService,
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;

        $hasProducts = $invoice->items()->where('item_type', 'product')->exists();

        if (!$hasProducts) {
            return;
        }

        $config = $this->nfeService->getConfig();

        if (!$config) {
            return;
        }

        $result = $this->nfeService->emitir($invoice);

        if (!$result->success) {
            Log::warning("Falha ao emitir NF-e automática para fatura #{$invoice->id}: {$result->errorMessage}");
        }
    }
}
