<?php

namespace App\Http\Controllers\Traits;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceTypeMap;
use App\Services\ConvenioService;

trait AutoInvoicesEntity
{
    private function autoInvoice(
        object $entity,
        string $serviceType,
        string $itemDescription,
        ?int $quantity = 1,
        ?float $unitPrice = null,
    ): ?Invoice {
        $pet = $entity->pet;
        $tutor = $pet->tutors()
            ->wherePivot('is_primary', true)
            ->first() ?? $pet->tutors()->first();

        if (!$tutor) return null;

        if ($unitPrice === null) {
            $map = ServiceTypeMap::where('type', $serviceType)
                ->where(function ($q) use ($entity) {
                    $q->whereNull('branch_id')
                      ->orWhere('branch_id', $entity->branch_id ?? $entity->branchId ?? null);
                })
                ->orderBy('branch_id', 'desc')
                ->first();
            $unitPrice = $map?->service?->price ?? 0;
        }

        $existingInvoice = Invoice::where('tutor_id', $tutor->id)
            ->where('pet_id', $pet->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvoice) {
            $invoice = $existingInvoice;
            $invoice->increment('subtotal', $unitPrice * $quantity);
            $invoice->increment('total', $unitPrice * $quantity);
        } else {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'pet_id' => $pet->id,
                'tutor_id' => $tutor->id,
                'user_id' => auth()->id(),
                'branch_id' => $entity->branch_id ?? $entity->branchId ?? auth()->user()->branch_id,
                'status' => 'pending',
                'total' => $unitPrice * $quantity,
                'subtotal' => $unitPrice * $quantity,
                'due_date' => now(),
            ]);
        }

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $itemDescription,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $unitPrice * $quantity,
            'item_type' => 'service',
        ]);

        $convenioService = app(ConvenioService::class);
        $subscription = $convenioService->findActiveSubscription($tutor, $pet);
        if ($subscription) {
            $convenioService->applyDiscount($invoice, $subscription);
        }

        $invoice->refresh();
        return $invoice;
    }
}
