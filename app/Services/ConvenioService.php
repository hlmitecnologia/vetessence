<?php

namespace App\Services;

use App\Models\ConvenioCoverageRule;
use App\Models\ConvenioSubscription;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Pet;
use App\Models\Tutor;

class ConvenioService
{
    public function findActiveSubscription(Tutor $tutor, Pet $pet): ?ConvenioSubscription
    {
        return $tutor->activeConvenioSubscriptionForPet($pet);
    }

    public function applyDiscount(Invoice $invoice, ConvenioSubscription $subscription): void
    {
        if ($invoice->convenio_subscription_id) {
            return;
        }

        $convenio = $subscription->convenio;
        $rules = $convenio->coverageRules;

        $totalDiscount = 0;

        foreach ($invoice->items as $item) {
            if ($item->item_type === 'product') continue;

            $discountPercent = $subscription->discount_percent;

            if ($rules->isNotEmpty()) {
                $rule = $rules->firstWhere('service_id', $item->service_id)
                    ?? $rules->firstWhere('item_type', $item->item_type);
                if ($rule) {
                    $discountPercent = $rule->coverage_percent;
                    if ($rule->max_value && $item->total * ($discountPercent / 100) > $rule->max_value) {
                        $discountPercent = ($rule->max_value / $item->total) * 100;
                    }
                } else {
                    continue;
                }
            }

            if ($discountPercent <= 0) continue;

            $itemDiscount = round($item->total * ($discountPercent / 100), 2);
            $totalDiscount += $itemDiscount;
        }

        if ($totalDiscount <= 0) return;

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Desconto Convênio (' . $convenio->name . ')',
            'quantity' => 1,
            'unit_price' => -$totalDiscount,
            'total' => -$totalDiscount,
            'item_type' => 'avulso',
        ]);

        $invoice->convenio_subscription_id = $subscription->id;
        $invoice->convenio_discount = $totalDiscount;
        $invoice->discount = ($invoice->discount ?? 0) + $totalDiscount;
        $invoice->total = $invoice->subtotal - $invoice->discount;
        $invoice->save();
    }
}
