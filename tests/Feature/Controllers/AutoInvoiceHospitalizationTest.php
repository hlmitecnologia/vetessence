<?php

namespace Tests\Feature\Controllers;

use App\Models\Hospitalization;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\ModuleTestCase;

class AutoInvoiceHospitalizationTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_discharge_creates_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $hospitalization = Hospitalization::factory()->create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'active',
        ]);

        $this->put(route('hospitalizations.update', $hospitalization), [
            'status' => 'discharged',
            'discharged_at' => now()->format('Y-m-d H:i:s'),
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNotNull($invoice, 'Invoice should be created on discharge');
        $this->assertEquals('pending', $invoice->status);
    }

    public function test_non_discharge_update_does_not_create_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $hospitalization = Hospitalization::factory()->create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'status' => 'active',
        ]);

        $this->put(route('hospitalizations.update', $hospitalization), [
            'bed' => 'Novo leito',
        ])->assertSessionHas('success');

        $invoice = Invoice::where('pet_id', $pet->id)
            ->where('tutor_id', $tutor->id)
            ->first();
        $this->assertNull($invoice, 'Invoice should not be created for non-discharge update');
    }
}
