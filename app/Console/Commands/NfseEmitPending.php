<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\Nfse\NfseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NfseEmitPending extends Command
{
    protected $signature = 'nfse:emit-pending';
    protected $description = 'Emit NFSe for all pending invoices with active NFSe config';

    public function handle(NfseService $nfseService): int
    {
        $invoices = Invoice::where('nfse_status', 'pending')->get();

        if ($invoices->isEmpty()) {
            $this->info('Nenhuma fatura pendente de emissão.');
            return Command::SUCCESS;
        }

        $emitted = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $result = $nfseService->emitir($invoice);

            if ($result->success) {
                $emitted++;
                $this->info("Fatura #{$invoice->id}: NFSe emitida ({$result->nfseNumber})");
            } else {
                $failed++;
                Log::warning("nfse:emit-pending — Fatura #{$invoice->id}: {$result->errorMessage}");
                $this->warn("Fatura #{$invoice->id}: {$result->errorMessage}");
            }
        }

        $this->info("Emitidas: {$emitted}, Falhas: {$failed}");
        return Command::SUCCESS;
    }
}
