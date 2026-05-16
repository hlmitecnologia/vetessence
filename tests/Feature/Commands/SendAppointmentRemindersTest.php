<?php

namespace Tests\Feature\Commands;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\CommunicationQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Carbon\Carbon;

class SendAppointmentRemindersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_queues_reminder_for_tomorrow_appointment()
    {
        $tutor = Tutor::factory()->create(['phone' => '5511999999999']);
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor);
        $appointment = Appointment::factory()->create([
            'pet_id' => $pet->id,
            'date' => Carbon::today()->addDay(),
            'status' => 'scheduled',
        ]);

        $this->artisan('appointments:remind --days=1')
            ->assertExitCode(0);

        $this->assertDatabaseHas('communication_queue', [
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'channel' => 'whatsapp',
        ]);
    }

    public function test_skips_non_scheduled_appointments()
    {
        $tutor = Tutor::factory()->create(['phone' => '5511999999999']);
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor);
        $appointment = Appointment::factory()->create([
            'pet_id' => $pet->id,
            'date' => Carbon::today()->addDay(),
            'status' => 'completed',
        ]);

        $this->artisan('appointments:remind --days=1')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('communication_queue', [
            'tutor_id' => $tutor->id,
        ]);
    }
}
