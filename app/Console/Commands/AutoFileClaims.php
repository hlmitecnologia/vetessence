<?php

namespace App\Console\Commands;

use App\Models\ConvenioClaim;
use App\Services\Insurance\PortoSeguroProvider;
use Illuminate\Console\Command;

class AutoFileClaims extends Command
{
    protected $signature = 'claims:auto-file {--dry-run : Simulate without submitting}';
    protected $description = 'Auto-file draft convenience claims to insurance providers';

    public function handle()
    {
        $claims = ConvenioClaim::with(['convenioPet.convenio', 'convenioPet.pet', 'invoice'])
            ->where('status', 'draft')
            ->get();

        if ($claims->isEmpty()) {
            $this->info('Nenhum sinistro pendente para envio.');
            return 0;
        }

        $provider = new PortoSeguroProvider();
        $submitted = 0;
        $failed = 0;

        foreach ($claims as $claim) {
            $this->line("Processando sinistro {$claim->claim_number}...");

            if ($this->option('dry-run')) {
                $this->warn("[DRY-RUN] Simularia envio para {$provider->getName()}");
                $submitted++;
                continue;
            }

            if ($provider->submit($claim)) {
                $claim->update(['status' => 'filed', 'filed_at' => now()]);
                $this->info("  ✓ Enviado: {$claim->claim_number}");
                $submitted++;
            } else {
                $claim->update(['status' => 'filed', 'filed_at' => now()]);
                $this->error("  ✗ Falha no envio: {$claim->claim_number}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Enviados: {$submitted}, Falhas: {$failed}");

        return $failed > 0 ? 1 : 0;
    }
}
