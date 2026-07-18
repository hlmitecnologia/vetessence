<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\NotificationLog;
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

        $result = $this->nfeService->emitirNfce($invoice);

        if (!$result->success) {
            Log::warning("Falha ao emitir NFC-e automática para fatura #{$invoice->id}: {$result->errorMessage}");

            NotificationLog::create([
                'tutor_id' => $invoice->tutor_id,
                'type' => 'nfe_emission_error',
                'channel' => 'system',
                'status' => 'failed',
                'sent_at' => now(),
                'message' => "Falha ao emitir NFC-e automática para fatura {$invoice->invoice_number}",
                'error_message' => $result->errorMessage,
                'branch_id' => $invoice->branch_id,
            ]);
        }
    }
}
