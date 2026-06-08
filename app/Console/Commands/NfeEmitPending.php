<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\Nfe\NfeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NfeEmitPending extends Command
{
    protected $signature = 'nfe:emit-pending';
    protected $description = 'Emit NF-e for all pending invoices with active NFe config';

    public function handle(NfeService $nfeService): int
    {
        $invoices = Invoice::where('nfe_status', 'pending')->get();

        if ($invoices->isEmpty()) {
            $this->info('Nenhuma fatura pendente de emissão.');
            return Command::SUCCESS;
        }

        $emitted = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $result = $nfeService->emitir($invoice);

            if ($result->success) {
                $emitted++;
                $this->info("Fatura #{$invoice->id}: NF-e emitida ({$result->nfeNumber})");
            } else {
                $failed++;
                Log::warning("nfe:emit-pending — Fatura #{$invoice->id}: {$result->errorMessage}");
                $this->warn("Fatura #{$invoice->id}: {$result->errorMessage}");
            }
        }

        $this->info("Emitidas: {$emitted}, Falhas: {$failed}");
        return Command::SUCCESS;
    }
}
