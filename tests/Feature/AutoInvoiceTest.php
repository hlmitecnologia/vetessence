<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Events\AppointmentCompleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AutoInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_event_dispatched_on_completed_status_in_update()
    {
        Event::fake();

        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $appointment = Appointment::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $this->actingAs($user);
        $this->put(route('appointments.update', $appointment), [
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'completed',
        ]);

        Event::assertDispatched(AppointmentCompleted::class);
    }

    public function test_invoice_created_when_appointment_completed()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $appointment = Appointment::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $this->actingAs($user);
        $this->put(route('appointments.update', $appointment), [
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('invoices', [
            'pet_id' => $pet->id,
            'status' => 'pending',
        ]);
    }

    public function test_no_invoice_for_non_completed_status()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $appointment = Appointment::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'status' => 'scheduled',
        ]);

        $this->actingAs($user);
        $this->put(route('appointments.update', $appointment), [
            'pet_id' => $pet->id,
            'vet_id' => $user->id,
            'date' => now()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'consulta',
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseMissing('invoices', [
            'pet_id' => $pet->id,
        ]);
    }
}
