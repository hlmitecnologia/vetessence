<?php

namespace Tests\Feature\Listeners;

use App\Events\AppointmentCompleted;
use App\Listeners\GenerateInvoiceFromAppointment;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class GenerateInvoiceFromAppointmentTest extends ModuleTestCase
{
    public function test_skips_when_appointment_has_paid_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'completed',
        ]);
        $existingInvoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        $appointment->invoices()->attach($existingInvoice->id);

        $listener = new GenerateInvoiceFromAppointment();
        $listener->handle(new AppointmentCompleted($appointment));

        $this->assertEquals(1, Invoice::where('tutor_id', $tutor->id)->count());
    }

    public function test_generates_when_appointment_has_no_paid_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = User::factory()->create();
        $branch = \App\Models\Branch::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'completed',
            'branch_id' => $branch->id,
        ]);
        $service = Service::create(['name' => 'Consulta', 'price' => 100, 'is_active' => true]);
        AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => 100,
        ]);

        $listener = new GenerateInvoiceFromAppointment();
        $listener->handle(new AppointmentCompleted($appointment));

        $this->assertEquals(1, Invoice::where('tutor_id', $tutor->id)->count());
    }
}
