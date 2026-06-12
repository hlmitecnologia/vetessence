<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\NotificationLog;
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

        $hasServices = $invoice->items()->where('item_type', 'service')->exists();

        if (!$hasServices) {
            return;
        }

        $config = $this->nfseService->getConfig();

        if (!$config) {
            return;
        }

        $result = $this->nfseService->emitir($invoice);

        if (!$result->success) {
            Log::warning("Falha ao emitir NFSe automática para fatura #{$invoice->id}: {$result->errorMessage}");

            NotificationLog::create([
                'tutor_id' => $invoice->tutor_id,
                'type' => 'nfse_emission_error',
                'channel' => 'system',
                'status' => 'failed',
                'sent_at' => now(),
                'message' => "Falha ao emitir NFSe automática para fatura {$invoice->invoice_number}",
                'error_message' => $result->errorMessage,
                'branch_id' => $invoice->branch_id,
            ]);
        }
    }
}
