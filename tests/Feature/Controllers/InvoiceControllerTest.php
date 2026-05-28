<?php

namespace Tests\Feature\Controllers;

use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class InvoiceControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Tutor::factory()->count(3)->create();
        Invoice::factory()->create();
        $response = $this->get(route('invoices.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        Tutor::factory()->create();
        $response = $this->get(route('invoices.create'));
        $response->assertOk();
    }

    public function test_store_creates_invoice_with_items()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();

        $response = $this->post(route('invoices.store'), [
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                ['description' => 'Consulta', 'quantity' => 1, 'unit_price' => 150.00],
                ['description' => 'Vacina', 'quantity' => 2, 'unit_price' => 80.00],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'tutor_id' => $tutor->id,
            'status' => 'pending',
        ]);
        $invoice = Invoice::where('tutor_id', $tutor->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(310.00, (float) $invoice->total);
    }

    public function test_store_validates_items_required()
    {
        $tutor = Tutor::factory()->create();
        $response = $this->post(route('invoices.store'), [
            'tutor_id' => $tutor->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors('items');
    }

    public function test_show()
    {
        $invoice = Invoice::factory()->create(['user_id' => auth()->id()]);
        $response = $this->get(route('invoices.show', $invoice));
        $response->assertOk();
    }

    public function test_edit()
    {
        $invoice = Invoice::factory()->create(['status' => 'pending', 'user_id' => auth()->id()]);
        $response = $this->get(route('invoices.edit', $invoice));
        $response->assertOk();
    }

    public function test_edit_fails_for_paid_invoice()
    {
        $invoice = Invoice::factory()->create(['status' => 'paid']);
        $response = $this->get(route('invoices.edit', $invoice));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_record()
    {
        $newDate = now()->addDays(20)->format('Y-m-d');
        $invoice = Invoice::factory()->create(['status' => 'pending', 'due_date' => now()->addDays(10), 'user_id' => auth()->id()]);

        $response = $this->put(route('invoices.update', $invoice), [
            'due_date' => $newDate,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'due_date' => $newDate,
        ]);
    }

    public function test_update_fails_for_paid_invoice()
    {
        $invoice = Invoice::factory()->create(['status' => 'paid']);

        $response = $this->put(route('invoices.update', $invoice), [
            'due_date' => now()->addDays(20)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_destroy_deletes_record()
    {
        $invoice = Invoice::factory()->create(['status' => 'pending', 'user_id' => auth()->id()]);

        $response = $this->delete(route('invoices.destroy', $invoice));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_destroy_fails_for_paid_invoice()
    {
        $invoice = Invoice::factory()->create(['status' => 'paid']);

        $response = $this->delete(route('invoices.destroy', $invoice));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
    }
}
