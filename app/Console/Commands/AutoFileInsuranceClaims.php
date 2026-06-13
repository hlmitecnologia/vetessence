<?php

namespace App\Console\Commands;

use App\Models\Convenio;
use App\Models\ConvenioClaim;
use App\Services\Insurance\InsuranceProviderFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoFileInsuranceClaims extends Command
{
    protected $signature = 'claims:auto-file
        {--dry-run : Preview claims that would be submitted without sending}';

    protected $description = 'Submit pending insurance claims to partner insurers';

    public function handle(): int
    {
        $claims = ConvenioClaim::where('status', 'pending')
            ->with(['convenioPet.convenio', 'convenioPet.pet', 'invoice'])
            ->get();

        if ($claims->isEmpty()) {
            $this->info('Nenhum sinistro pendente para enviar.');
            return self::SUCCESS;
        }

        $this->info("Encontrado(s) {$claims->count()} sinistro(s) pendente(s).");

        $submitted = 0;
        $failed = 0;

        foreach ($claims as $claim) {
            $providerName = $this->resolveProviderName($claim);

            if (!$providerName) {
                $this->warn("Sinistro #{$claim->id}: não foi possível identificar a operadora.");
                $claim->update(['status' => 'failed', 'notes' => ($claim->notes ? $claim->notes . "\n" : '') . 'Provider not resolved']);
                $failed++;
                continue;
            }

            try {
                $provider = InsuranceProviderFactory::make($providerName);
            } catch (\Throwable $e) {
                $this->warn("Sinistro #{$claim->id}: operadora desconhecida '{$providerName}'.");
                $claim->update(['status' => 'failed', 'notes' => ($claim->notes ? $claim->notes . "\n" : '') . "Unknown provider: {$providerName}"]);
                $failed++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  [DRY-RUN] Enviaria sinistro #{$claim->id} ({$claim->claim_number}) para {$providerName}");
                continue;
            }

            $this->line("  Enviando sinistro #{$claim->id} ({$claim->claim_number}) para {$providerName}...");

            $result = $provider->submitClaim($claim);

            if ($result->success) {
                $claim->update([
                    'status' => 'submitted',
                    'external_id' => $result->externalId,
                    'filed_at' => $claim->filed_at ?? now(),
                ]);

                Log::info('Claim auto-submitted successfully', [
                    'claim_id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'provider' => $providerName,
                    'external_id' => $result->externalId,
                ]);

                $this->info("  -> Enviado (ID externo: {$result->externalId})");
                $submitted++;
            } else {
                $claim->update([
                    'status' => 'failed',
                    'notes' => ($claim->notes ? $claim->notes . "\n" : '') . "Auto-file failed: {$result->message}",
                ]);

                Log::warning('Claim auto-submission failed', [
                    'claim_id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'provider' => $providerName,
                    'error' => $result->message,
                ]);

                $this->error("  -> Falhou: {$result->message}");
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Total', 'Submitted', 'Failed'],
            [[$claims->count(), $submitted, $failed]]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolveProviderName(ConvenioClaim $claim): ?string
    {
        $convenio = $claim->convenioPet?->convenio;

        if (!$convenio) {
            return null;
        }

        $name = str($convenio->name)->lower()->ascii()->value();

        return match (true) {
            str_contains($name, 'porto seguro') => 'porto-seguro',
            default => null,
        };
    }
}
