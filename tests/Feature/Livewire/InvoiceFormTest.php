<?php

namespace Tests\Feature\Livewire;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tutor;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class InvoiceFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_can_create_invoice_with_items()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->set('items.0.description', 'Consulta')
            ->set('items.0.quantity', '1')
            ->set('items.0.unit_price', '150.00')
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'tutor_id' => $tutor->id,
            'subtotal' => 150.00,
            'total' => 150.00,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Consulta',
            'quantity' => 1,
            'unit_price' => 150.00,
        ]);
    }

    public function test_can_create_invoice_with_multiple_items()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->set('items.0.description', 'Consulta')
            ->set('items.0.quantity', '1')
            ->set('items.0.unit_price', '150.00')
            ->call('addItem')
            ->set('items.1.description', 'Exame de Sangue')
            ->set('items.1.quantity', '2')
            ->set('items.1.unit_price', '75.00')
            ->call('save')
            ->assertRedirect();

        $invoice = Invoice::where('tutor_id', $tutor->id)->first();
        $this->assertEquals(300.00, $invoice->total);
        $this->assertCount(2, InvoiceItem::where('invoice_id', $invoice->id)->get());
    }

    public function test_validates_required_fields()
    {
        Livewire::test('invoice-form')
            ->call('save')
            ->assertHasErrors(['tutor_id']);
        // due_date has default (7 days from now), items has one row from mount
    }

    public function test_validates_item_fields()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->set('items.0.description', '')
            ->set('items.0.quantity', '0')
            ->set('items.0.unit_price', '-1')
            ->call('save')
            ->assertHasErrors(['items.0.description', 'items.0.quantity', 'items.0.unit_price']);
    }

    public function test_can_add_and_remove_items()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->call('addItem')
            ->call('addItem')
            ->assertSet('items', [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0],
                ['description' => '', 'quantity' => 1, 'unit_price' => 0],
                ['description' => '', 'quantity' => 1, 'unit_price' => 0],
            ])
            ->call('removeItem', 1)
            ->assertSet('items', [
                ['description' => '', 'quantity' => 1, 'unit_price' => 0],
                ['description' => '', 'quantity' => 1, 'unit_price' => 0],
            ]);
    }

    public function test_calculates_subtotal_correctly()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->set('items.0.description', 'Serviço A')
            ->set('items.0.quantity', '2')
            ->set('items.0.unit_price', '100.00')
            ->call('addItem')
            ->set('items.1.description', 'Serviço B')
            ->set('items.1.quantity', '3')
            ->set('items.1.unit_price', '50.00')
            ->assertSet('subtotal', 350.00);
    }

    public function test_can_create_invoice_with_product_and_service_items()
    {
        $tutor = Tutor::factory()->create();

        Livewire::test('invoice-form')
            ->set('tutor_id', (string) $tutor->id)
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->set('items.0.description', 'Vacina V10')
            ->set('items.0.quantity', '1')
            ->set('items.0.unit_price', '90.00')
            ->call('addItem')
            ->set('items.1.description', 'Aplicação')
            ->set('items.1.quantity', '1')
            ->set('items.1.unit_price', '30.00')
            ->call('save')
            ->assertRedirect();

        $invoice = Invoice::where('tutor_id', $tutor->id)->first();
        $this->assertEquals(120.00, $invoice->total);
    }
}
