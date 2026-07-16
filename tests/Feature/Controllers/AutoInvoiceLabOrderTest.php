<?php

namespace Tests\Feature\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LaboratoryOrder;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\ModuleTestCase;

class AutoInvoiceLabOrderTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_updating_to_ready_creates_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $order = LaboratoryOrder::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'processing',
        ]);

        $this->put(route('laboratory-orders.update', $order), [
            'status' => 'ready',
            'result_date' => now()->format('Y-m-d'),
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNotNull($invoice, 'Invoice should be created when lab order becomes ready');
        $this->assertEquals('pending', $invoice->status);
    }

    public function test_other_status_transitions_do_not_create_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $order = LaboratoryOrder::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'requested',
        ]);

        $this->put(route('laboratory-orders.update', $order), [
            'status' => 'processing',
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNull($invoice, 'Invoice should not be created for non-ready status');
    }
}
