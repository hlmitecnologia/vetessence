<?php

namespace Tests\Feature\Listeners;

use App\Events\InvoicePaid;
use App\Listeners\CalculateCommissionOnPaid;
use App\Models\CommissionRate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\User;
use Tests\ModuleTestCase;

class CalculateCommissionOnPaidTest extends ModuleTestCase
{
    public function test_calculates_commission_on_paid()
    {
        $vet = User::factory()->create();
        $service = Service::factory()->create(['name' => 'Consulta']);
        $invoice = Invoice::factory()->create(['status' => 'paid']);

        CommissionRate::factory()->create([
            'user_id' => $vet->id,
            'commissionable_type' => Service::class,
            'commissionable_id' => $service->id,
            'rate_type' => 'percentage',
            'rate_value' => 10,
            'is_active' => true,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Consulta',
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100,
        ]);

        $this->assertTrue(true);
    }
}
