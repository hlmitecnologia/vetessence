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
            $this->info('No pending claims to submit.');
            return self::SUCCESS;
        }

        $this->info("Found {$claims->count()} pending claim(s).");

        $submitted = 0;
        $failed = 0;

        foreach ($claims as $claim) {
            $providerName = $this->resolveProviderName($claim);

            if (!$providerName) {
                $this->warn("Claim #{$claim->id}: unable to resolve insurance provider.");
                $claim->update(['status' => 'failed', 'notes' => ($claim->notes ? $claim->notes . "\n" : '') . 'Provider not resolved']);
                $failed++;
                continue;
            }

            try {
                $provider = InsuranceProviderFactory::make($providerName);
            } catch (\Throwable $e) {
                $this->warn("Claim #{$claim->id}: unknown provider '{$providerName}'.");
                $claim->update(['status' => 'failed', 'notes' => ($claim->notes ? $claim->notes . "\n" : '') . "Unknown provider: {$providerName}"]);
                $failed++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("  [DRY-RUN] Would submit claim #{$claim->id} ({$claim->claim_number}) to {$providerName}");
                continue;
            }

            $this->line("  Submitting claim #{$claim->id} ({$claim->claim_number}) to {$providerName}...");

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

                $this->info("  -> Submitted (external ID: {$result->externalId})");
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

                $this->error("  -> Failed: {$result->message}");
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
