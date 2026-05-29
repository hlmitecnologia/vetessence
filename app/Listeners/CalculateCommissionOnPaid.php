<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Models\CommissionLog;
use App\Models\CommissionRate;

class CalculateCommissionOnPaid
{
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;

        $appointments = $invoice->appointments;

        if ($appointments->isEmpty()) {
            return;
        }

        $vetIds = $appointments->pluck('vet_id')->merge($appointments->pluck('user_id'))->unique()->filter();

        foreach ($vetIds as $vetId) {
            $rates = CommissionRate::where('user_id', $vetId)
                ->where('is_active', true)
                ->get();

            if ($rates->isEmpty()) {
                continue;
            }

            $vetItems = $invoice->items;

            foreach ($vetItems as $item) {
                foreach ($rates as $rate) {
                    $baseValue = (float) $item->total;

                    if ($rate->rate_type === 'percentage') {
                        $commissionValue = $baseValue * ((float) $rate->rate_value / 100);
                    } else {
                        $commissionValue = (float) $rate->rate_value;
                    }

                    CommissionLog::create([
                        'user_id' => $vetId,
                        'invoice_id' => $invoice->id,
                        'commission_rate_id' => $rate->id,
                        'description' => $item->description ?? 'Item da fatura',
                        'base_value' => $baseValue,
                        'commission_value' => $commissionValue,
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }
}
