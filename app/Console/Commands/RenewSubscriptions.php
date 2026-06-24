<?php

namespace App\Console\Commands;

use App\Models\PetShopSubscription;
use App\Models\PetShopPackage;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';
    protected $description = 'Renova assinaturas com auto_renew ativo próximas do vencimento';

    public function handle(): int
    {
        $renewed = 0;
        $due = PetShopSubscription::where('auto_renew', true)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->addDays(3))
            ->get();

        foreach ($due as $subscription) {
            $package = $subscription->package;
            if (!$package || !$package->is_active) continue;

            PetShopSubscription::create([
                'pet_id' => $subscription->pet_id,
                'package_id' => $subscription->package_id,
                'branch_id' => $subscription->branch_id,
                'start_date' => now(),
                'end_date' => $package->validity_days ? now()->addDays($package->validity_days) : null,
                'remaining_uses' => $package->max_uses,
                'total_uses' => $package->max_uses,
                'total_savings' => 0,
                'status' => 'active',
                'auto_renew' => $subscription->auto_renew,
            ]);

            $renewed++;
        }

        $this->info("{$renewed} assinaturas renovadas.");
        return Command::SUCCESS;
    }
}
