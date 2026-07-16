<?php

namespace Tests\Feature\Controllers;

use App\Models\Boarding;
use App\Models\BoardingKennel;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\ModuleTestCase;

class BoardingCheckoutInvoiceTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    private function makePet(Tutor $tutor): Pet
    {
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        return $pet;
    }

    public function test_checkout_creates_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $kennel = BoardingKennel::factory()->create();
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id,
            'kennel_id' => $kennel->id,
            'branch_id' => 1,
            'status' => 'checked_in',
            'check_in_at' => Carbon::now()->subDays(2),
            'daily_rate' => 50,
            'grooming_fee' => 30,
        ]);

        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total_amount' => 180,
        ]);

        $response->assertSessionHas('success');

        $boarding->refresh();
        $this->assertEquals('checked_out', $boarding->status);

        $invoice = Invoice::where('boarding_id', $boarding->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($pet->id, $invoice->pet_id);
        $this->assertEquals($tutor->id, $invoice->tutor_id);
        $this->assertEquals('pending', $invoice->status);
    }

    public function test_checkout_with_zero_amount_does_not_create_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);
        $kennel = BoardingKennel::factory()->create();
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id,
            'kennel_id' => $kennel->id,
            'branch_id' => 1,
            'status' => 'checked_in',
            'check_in_at' => Carbon::now()->subDays(1),
            'daily_rate' => 0,
        ]);

        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total_amount' => 0,
        ]);

        $response->assertSessionHas('success');

        $invoice = Invoice::where('boarding_id', $boarding->id)->first();
        $this->assertNull($invoice);
    }

    public function test_checkout_with_convenio_applies_discount()
    {
        $convenio = \App\Models\Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $tutor = Tutor::factory()->create();
        $pet = $this->makePet($tutor);

        $sub = \App\Models\ConvenioSubscription::create([
            'tutor_id' => $tutor->id,
            'convenio_id' => $convenio->id,
            'discount_percent' => 10,
            'is_active' => true,
        ]);
        $sub->coveredPets()->create(['pet_id' => $pet->id]);

        $kennel = BoardingKennel::factory()->create();
        $boarding = Boarding::factory()->create([
            'pet_id' => $pet->id,
            'kennel_id' => $kennel->id,
            'branch_id' => 1,
            'status' => 'checked_in',
            'check_in_at' => Carbon::now()->subDays(1),
            'daily_rate' => 100,
            'grooming_fee' => 0,
        ]);

        $response = $this->post(route('boardings.checkout', $boarding), [
            'check_out_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total_amount' => 100,
        ]);

        $response->assertSessionHas('success');

        $invoice = Invoice::where('boarding_id', $boarding->id)->first();
        $this->assertNotNull($invoice);
        // Check-in 1 day ago -> daysBoarded = 2 -> items total = 200 -> 10% discount = 20
        // Invoice total from request = 100, subtotal = 100, total - discount = 80
        $this->assertEquals(20, $invoice->convenio_discount);
        $this->assertEquals(80, $invoice->total);
    }
}
