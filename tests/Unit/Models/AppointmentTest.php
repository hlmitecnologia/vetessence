<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'scheduled',
            'reason' => 'Checkup',
            'notes' => 'Some notes',
            'is_recurring' => false,
            'recurrence_rule' => null,
            'recurrence_end_date' => null,
            'parent_appointment_id' => null,
            'branch_id' => null,
        ]);

        $this->assertDatabaseHas('appointments', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);

        $this->assertInstanceOf(Pet::class, $appointment->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);

        $this->assertInstanceOf(User::class, $appointment->vet);
    }

    public function test_services_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $service = Service::create(['name' => 'Consulta', 'price' => 100.50, 'is_active' => true]);
        AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => 100.50,
        ]);

        $this->assertCount(1, $appointment->services);
        $this->assertInstanceOf(AppointmentService::class, $appointment->services->first());
    }

    public function test_has_paid_invoice_returns_true_when_paid()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'completed',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        $appointment->invoices()->attach($invoice->id);

        $this->assertTrue($appointment->hasPaidInvoice());
    }

    public function test_has_paid_invoice_returns_false_when_pending()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'completed',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'status' => 'pending',
        ]);
        $appointment->invoices()->attach($invoice->id);

        $this->assertFalse($appointment->hasPaidInvoice());
    }

    public function test_has_paid_invoice_returns_false_when_cancelled()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'completed',
        ]);
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'status' => 'cancelled',
        ]);
        $appointment->invoices()->attach($invoice->id);

        $this->assertFalse($appointment->hasPaidInvoice());
    }

    public function test_has_paid_invoice_returns_false_without_invoices()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consultation',
            'status' => 'completed',
        ]);

        $this->assertFalse($appointment->hasPaidInvoice());
    }
}
