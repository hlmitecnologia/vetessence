<?php

namespace Tests\Feature\Integrations;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class InvoicePaymentFlowTest extends ModuleTestCase
{
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
        $this->branch = Branch::factory()->create();
    }

    public function test_invoice_creation_items_and_payment()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 350.00,
            'discount' => 0,
            'total' => 350.00,
            'status' => 'pending',
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'notes' => 'Consulta + exames',
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'total' => 350.00,
            'status' => 'pending',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consulta clínica',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total' => 200.00,
            'branch_id' => $this->branch->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Exame de sangue',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total' => 150.00,
            'branch_id' => $this->branch->id,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Consulta clínica',
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Exame de sangue',
        ]);

        $this->assertEquals(2, $invoice->items()->count());
        $this->assertEquals(350.00, $invoice->fresh()->total);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now()->format('Y-m-d'),
            'payment_method' => 'pix',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
            'payment_method' => 'pix',
        ]);
        $this->assertNotNull($invoice->fresh()->paid_at);
    }
}
